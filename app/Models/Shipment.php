<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $table = 'shipments';
    protected $primaryKey = 'id';
    public $incrementing = true;

    // Lasciamo false così usi il default DB su created_at e puoi lasciare updated_at null
    public $timestamps = false;

    /** Stati reali presenti nel DB + etichette per la UI */
    public const STATUS_LABELS = [
        'data_entered'    => 'Inserita',
        'estimate'        => 'Preventivo',
        'label_generated' => 'Etichetta generata',
        'paid'            => 'Pagata',
        'started'         => 'Avviata',
    ];

    /** alias -> stato normalizzato */
    public const STATUS_ALIASES = [
        'nuova' => 'data_entered', 'new' => 'data_entered', 'created' => 'data_entered',
        'quote' => 'estimate', 'preventivo' => 'estimate',
        'label' => 'label_generated', 'etichetta' => 'label_generated',
        'pagata' => 'paid', 'paid' => 'paid',
        'started' => 'started', 'avviata' => 'started',
    ];

    // Colori per Bootstrap 5
    public const STATUS_COLORS = [
        'data_entered' => 'secondary',
        'estimate' => 'warning',
        'label_generated' => 'primary',
        'paid' => 'success',
        'started' => 'info',
        'delivered' => 'success',
        'in_transit' => 'info',
        'failed' => 'danger',
    ];

    /** Classe CSS bootstrap 5 per il badge */
    public function getStatusClassAttribute(): string
    {
        $color = self::STATUS_COLORS[$this->status ?? ''] ?? 'secondary';
        return 'badge bg-' . $color;
    }

    protected $fillable = [
        'user_id','session_id','courier','price','pickupZip','deliveryZip','pickupDate',
        'accessories','costa_accessory','total_amount','paypal_order_id',
        'sender_name','sender_address','sender_phone','sender_city','sender_province','sender_email',
        'recipient_name','recipient_address','recipient_phone','recipient_city','recipient_province','recipient_email',
        'parcels_data','weight','volume','additional_notes','label_data','status','created_at','shipment_id',
        'package_type','updated_at','length','width','height'
    ];

    protected $casts = [
        'price' => 'float',
        'costa_accessory' => 'float',
        'total_amount' => 'float',
        'weight' => 'float',
        'volume' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
        'pickupDate' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ---------- Relazioni ---------- */
    public function user()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'user_id');
    }

    /* ---------- Accessors ---------- */

    /** Volume calcolato (cm³ / 3333) */
    public function getVolumeCalculatedAttribute(): float
    {
        $L = (float)($this->length ?? 0);
        $W = (float)($this->width ?? 0);
        $H = (float)($this->height ?? 0);
        if ($L <= 0 || $W <= 0 || $H <= 0) return 0.0;
        return round(($L * $W * $H) / 3333, 2);
    }

    /** Peso tassabile = max(peso reale, volumetrico) */
    public function getChargeableWeightAttribute(): float
    {
        $real = (float)($this->weight ?? 0);
        $vol  = $this->volume_calculated;
        return round(max($real, $vol), 2);
    }

    /** Accessories come array */
    public function getAccessoriesArrayAttribute(): array
    {
        $raw = $this->accessories ?? '';
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) return $json;
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /** Etichetta stato per la UI */
    public function getStatusLabelAttribute(): string
    {
        $s = (string)($this->status ?? '');
        return self::STATUS_LABELS[$s] ?? ($s !== '' ? $s : '—');
    }

    /* ---------- Scopes ---------- */

    public function scopeStatus($q, $status)
    {
        if (!$status || $status === 'all') return $q;

        $normalize = function($s){
            $s = strtolower(trim($s));
            return self::STATUS_ALIASES[$s] ?? $s;
        };

        if (is_array($status)) {
            $wanted = array_map($normalize, $status);
            $valid  = array_intersect($wanted, array_keys(self::STATUS_LABELS));
            return count($valid) ? $q->whereIn('status', $valid) : $q;
        }

        $status = $normalize($status);
        return array_key_exists($status, self::STATUS_LABELS)
            ? $q->where('status', $status)
            : $q;
    }

    public function scopeDateFrom($q, ?string $from)
    {
        if ($from) $q->whereDate('pickupDate', '>=', $from);
        return $q;
    }

    public function scopeDateTo($q, ?string $to)
    {
        if ($to) $q->whereDate('pickupDate', '<=', $to);
        return $q;
    }

    public function scopeSearch($q, ?string $s)
    {
        if (!$s) return $q;
        return $q->where(function($w) use ($s){
            $w->where('shipment_id','like',"%$s%")
                ->orWhere('sender_name','like',"%$s%")
                ->orWhere('recipient_name','like',"%$s%")
                ->orWhere('sender_city','like',"%$s%")
                ->orWhere('recipient_city','like',"%$s%");
        });
    }

    /** Solo spedizioni con dati minimi */
    public function scopeOnlyComplete($q)
    {
        return $q->whereNotNull('sender_name')->where('sender_name','<>','')
            ->whereNotNull('recipient_name')->where('recipient_name','<>','');
    }

    /* ---------- Mutators ---------- */

    /** Normalizza e imposta status, default 'data_entered' */
    public function setStatusAttribute($value)
    {
        $v = trim((string)$value);
        if ($v === '') {
            $this->attributes['status'] = 'data_entered';
            return;
        }
        $v = strtolower($v);
        $norm = self::STATUS_ALIASES[$v] ?? $v;
        $this->attributes['status'] = array_key_exists($norm, self::STATUS_LABELS) ? $norm : 'data_entered';
    }
}
