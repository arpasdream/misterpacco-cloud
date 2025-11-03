<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    /** Lista con filtri */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $from   = $request->get('from');
        $to     = $request->get('to');
        $s      = $request->get('s');

        $spedizioni = Shipment::query()
            ->onlyComplete()
            ->status($status)
            ->dateFrom($from)
            ->dateTo($to)
            ->search($s)
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('spedizioni.index', compact('spedizioni','status','from','to','s'));
    }

    /** Form creazione */
    public function create()
    {
        return view('spedizioni.create');
    }

    /** Salvataggio */
    public function store(Request $r)
    {
        $data = $r->validate($this->rules());
        $shipment = Shipment::create($data);

        return redirect()
            ->route('lista-spedizioni')
            ->with('success', 'Spedizione creata correttamente (#'.$shipment->id.').');
    }

    /** Dettaglio */
    public function show(Shipment $spedizione)
    {
        return view('spedizioni.show', ['s' => $spedizione]);
    }

    /** Edit */
    public function edit(Shipment $spedizione)
    {
        return view('spedizioni.edit', ['s' => $spedizione]);
    }

    /** Update */
    public function update(Request $r, Shipment $spedizione)
    {
        $data = $r->validate($this->rules());
        $spedizione->update($data);

        return redirect()
            ->route('lista-spedizioni')
            ->with('success', 'Spedizione aggiornata (#'.$spedizione->id.').');
    }

    /** Delete */
    public function destroy(Shipment $spedizione)
    {
        $spedizione->delete();
        return back()->with('success', 'Spedizione eliminata (#'.$spedizione->id.').');
    }

    /** DOWNLOAD ETICHETTA: decodifica base64/JSON/dataURI e forza download */
    public function downloadLabel(Shipment $spedizione)
    {
        $raw = (string)($spedizione->label_data ?? '');
        if ($raw === '') abort(404, 'Etichetta non presente.');

        [$bytes, $mime, $filename] = $this->parseLabelPayload(
            $raw,
            $spedizione->shipment_id ?: (string)$spedizione->id
        );

        return response($bytes, 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /* ===========================
       Helpers
       =========================== */

    /** Regole di validazione condivise tra store/update */
    private function rules(): array
    {
        return [
            // client_id rimosso
            'user_id'          => ['nullable','integer','exists:users,id'],
            'courier'          => ['nullable','string','max:255'],
            'pickupZip'        => ['nullable','string','max:20'],
            'deliveryZip'      => ['nullable','string','max:20'],
            'pickupDate'       => ['nullable','date'],
            'accessories'      => ['nullable','string'],
            'costa_accessory'  => ['nullable','numeric'],
            'total_amount'     => ['nullable','numeric'],
            'paypal_order_id'  => ['nullable','string','max:255'],

            'sender_name'      => ['required','string','max:255'],
            'sender_address'   => ['required','string','max:255'],
            'sender_phone'     => ['nullable','string','max:20'],
            'sender_city'      => ['nullable','string','max:255'],
            'sender_province'  => ['nullable','string','max:255'],
            'sender_email'     => ['nullable','string','max:255'],

            'recipient_name'     => ['required','string','max:255'],
            'recipient_address'  => ['required','string','max:255'],
            'recipient_phone'    => ['nullable','string','max:20'],
            'recipient_city'     => ['nullable','string','max:255'],
            'recipient_province' => ['nullable','string','max:255'],
            'recipient_email'    => ['nullable','string','max:255'],

            'parcels_data'     => ['nullable','string'],
            'weight'           => ['nullable','numeric'],
            'volume'           => ['nullable','numeric'],
            'additional_notes' => ['nullable','string'],
            'label_data'       => ['nullable','string'],
            'status'           => ['nullable','string','max:50'],
            'shipment_id'      => ['nullable','string','max:255'],
            'package_type'     => ['nullable','string','max:32'],
            'length'           => ['nullable','numeric'],
            'width'            => ['nullable','numeric'],
            'height'           => ['nullable','numeric'],
        ];
    }

    /**
     * Converte label_data in [bytes, mime, filename]
     * Accetta: data URI / JSON / base64 puro / testo (ZPL)
     */
    private function parseLabelPayload(string $raw, string $id): array
    {
        $mime = 'application/octet-stream';
        $ext  = 'bin';
        $b64  = null;

        // 1) data URI
        if (preg_match('/^data:([^;]+);base64,(.*)$/s', $raw, $m)) {
            $mime = trim($m[1]);
            $b64  = $m[2];
        } else {
            // 2) JSON {mime, content}
            $j = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($j)) {
                if (!empty($j['content'])) $b64 = $j['content'];
                if (!empty($j['mime']))    $mime = (string)$j['mime'];
            } else {
                // 3) base64 “puro” o testo
                $b64 = $raw;
            }
        }

        $bytes = base64_decode($b64 ?? '', true);
        if ($bytes === false) {
            // non base64: probabile ZPL o testo
            $bytes = $raw;
            if (stripos($raw, '^XA') !== false && stripos($raw, '^XZ') !== false) {
                $mime = 'text/plain';
                $ext  = 'zpl';
            } else {
                $mime = 'text/plain';
                $ext  = 'txt';
            }
        } else {
            $map = [
                'application/pdf' => 'pdf',
                'image/png'       => 'png',
                'image/jpeg'      => 'jpg',
                'text/plain'      => 'txt',
                'application/zpl' => 'zpl',
                'text/zpl'        => 'zpl',
            ];
            $ext = $map[$mime] ?? $ext;

            // firma file per dedurre mime se mancante
            if ($ext === 'bin') {
                if (strncmp($bytes, "%PDF", 4) === 0) { $mime='application/pdf'; $ext='pdf'; }
                elseif (substr($bytes, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") { $mime='image/png'; $ext='png'; }
                elseif (substr($bytes, 0, 3) === "\xFF\xD8\xFF") { $mime='image/jpeg'; $ext='jpg'; }
            }
        }

        $name = 'etichetta_' . $id . '.' . $ext;
        return [$bytes, $mime, $name];
    }
}
