<?php

namespace App\Services;

use App\Models\User;
use App\Models\UsersRate;
use Illuminate\Support\Collection;

class UsersWithRatesParser
{
    protected bool $skipRatesDelete = false;
    public function setSkipRatesDelete(bool $v): void { $this->skipRatesDelete = $v; }

    // Mappa etichette → campi DB (tollerante)
    protected array $map = [
        'ragione sociale'  => 'ragione_sociale',
        'via/pz.'          => 'indirizzo',
        'via/pz'           => 'indirizzo',
        'via'              => 'indirizzo',
        "citta'"           => 'citta',
        'citta'            => 'citta',
        'cap'              => 'cap',
        'provincia'        => 'provincia',
        'codice fiscale'   => 'codice_fiscale',
        'partita iva'      => 'piva',
        'p.iva'            => 'piva',
        'piva'             => 'piva',
        'email'            => 'email',
        'e-mail'           => 'email',
        'mail'             => 'email',
        'indirizzo email'  => 'email',
        'email:'           => 'email',
        'telefono'         => 'telefono',
        // in note
        'sdi'              => '_note_sdi',
        'pec'              => '_note_pec',
        'pagamento'        => '_note_pagamento',
    ];

    /* =========================
       Helpers
       ========================= */

    protected function norm(?string $v): string {
        $v = (string)$v;
        // NBSP → spazio
        $v = str_replace("\xC2\xA0", ' ', $v);
        $v = trim($v);
        $v = mb_strtolower($v);
        $v = str_replace(['à','è','é','ì','ò','ù'], ['a','e','e','i','o','u'], $v);
        $v = preg_replace('/\s+/', ' ', $v);
        return $v;
    }

    protected function slug(?string $s): ?string {
        $s = trim((string)$s);
        if ($s === '') return null;
        $s = mb_strtolower($s);
        $s = str_replace(['à','è','é','ì','ò','ù'], ['a','e','e','i','o','u'], $s);
        $s = preg_replace('/[^a-z0-9]+/u', '-', $s);
        $s = trim($s, '-');
        return $s ?: null;
    }

    /** Crea email tecnica se manca: preferisce PIVA, altrimenti slug(ragione_sociale) */
    public function autoEmailFrom(?string $ragione, ?string $piva): ?string
    {
        $piva = preg_replace('/\D+/', '', (string)$piva);
        if ($piva && strlen($piva) >= 8) return $piva.'@noemail.local';
        $slug = $this->slug($ragione);
        if ($slug) return $slug.'@noemail.local';
        return null;
    }

    protected function cleanMoney(?string $v): ?float {
        if ($v === null) return null;
        $s = trim((string)$v);
        if ($s === '') return null;

        // Rimuovi valuta/parole e NBSP
        $s = str_ireplace(['€','eur','euro'], '', $s);
        $s = preg_replace('/\x{00A0}/u', '', $s);
        $s = trim($s);

        // Tieni solo cifre, virgole, punti e segno meno
        $raw = preg_replace('/[^\d,.\-]/u', '', $s);
        if ($raw === '' || $raw === '-') return null;

        $hasComma = strpos($raw, ',') !== false;
        $hasDot   = strpos($raw, '.') !== false;

        if ($hasComma && $hasDot) {
            // usa l’ULTIMO come separatore decimale
            $lastComma = strrpos($raw, ',');
            $lastDot   = strrpos($raw, '.');
            $decSep = ($lastComma > $lastDot) ? ',' : '.';
            $thSep  = ($decSep === ',') ? '.' : ',';
            $raw = str_replace($thSep, '', $raw);   // togli migliaia
            $raw = str_replace($decSep, '.', $raw); // decimale a '.'
        } elseif ($hasComma) {
            // solo virgola = decimale
            $raw = str_replace('.', '', $raw);      // togli migliaia
            $raw = str_replace(',', '.', $raw);
        } else {
            // solo punti o solo cifre
            if (strpos($raw, '.') === false) {
                // nessun separatore → interpreta come centesimi se 3+ cifre (es. "2750" => 27.50)
                if (preg_match('/^\-?\d{3,}$/', $raw)) {
                    $neg = ($raw[0] === '-') ? '-' : '';
                    $digits = ltrim($raw, '-');
                    $raw = $neg . substr($digits, 0, -2) . '.' . substr($digits, -2);
                }
            } else {
                // pattern "1.234.567" → togli punti
                if (preg_match('/^\d{1,3}(\.\d{3})+$/', $raw)) {
                    $raw = str_replace('.', '', $raw);
                }
            }
        }

        if (!is_numeric($raw)) return null;
        return round((float)$raw, 2);
    }

    protected function cleanCap(?string $v): ?string {
        $s = preg_replace('/\D+/', '', (string)$v);
        if ($s === '') return null;
        if (strlen($s) < 5) $s = str_pad($s, 5, '0', STR_PAD_LEFT);
        return $s;
    }

    protected function cellToString($cell): string
    {
        if ($cell === null) return '';
        if (is_scalar($cell)) return trim((string)$cell);
        if (is_object($cell) && method_exists($cell, '__toString')) {
            return trim((string)$cell);
        }
        if (is_array($cell)) {
            $flat = [];
            array_walk_recursive($cell, function($v) use (&$flat){
                if ($v === null) return;
                if (is_scalar($v)) $flat[] = trim((string)$v);
                elseif (is_object($v) && method_exists($v, '__toString')) $flat[] = trim((string)$v);
            });
            return trim(implode(' ', array_filter($flat)));
        }
        return '';
    }

    protected function eqLabel(string $label, string $expected): bool
    {
        // normalizza entrambe e compara
        return $this->norm($label) === $this->norm($expected);
    }

    /* =========================
       API pubbliche usate dal Command
       ========================= */

    /** Elenco “preview” dei blocchi (per --list) */
    public function listBlocks(Collection $sheet): array
    {
        $rows = $sheet->values();
        $rowCount = $rows->count();

        // inizio blocchi tollerante
        $blockStarts = [];
        for ($i = 0; $i < $rowCount; $i++) {
            $lab = $this->norm((string)($rows[$i][0] ?? ''));
            if (preg_match('/^ragione\s+sociale:?$/', $lab)) {
                $blockStarts[] = $i;
            }
        }
        if (empty($blockStarts)) $blockStarts = [0];

        // range blocchi
        $blocks = [];
        foreach ($blockStarts as $idx => $start) {
            $end = ($idx + 1 < count($blockStarts)) ? $blockStarts[$idx + 1] - 1 : ($rowCount - 1);
            $blocks[] = [$start, $end];
        }

        $out = [];
        foreach ($blocks as $b => [$startRow, $endRow]) {
            $profile = [];
            $pecRaw  = null;

            for ($i = $startRow; $i <= $endRow; $i++) {
                $label = $this->norm((string)($rows[$i][0] ?? ''));
                $value = trim((string)($rows[$i][1] ?? ''));

                if ($label === 'ragione sociale' && empty($profile['ragione_sociale'])) $profile['ragione_sociale'] = $value;
                if (($label === 'partita iva' || $label === 'p.iva' || $label === 'piva') && empty($profile['piva'])) $profile['piva'] = $value;
                if (($label === "citta'" || $label === 'citta') && empty($profile['citta'])) $profile['citta'] = $value;
                if ($label === 'provincia' && empty($profile['provincia'])) $profile['provincia'] = $value;
                if ($label === 'cap' && empty($profile['cap'])) $profile['cap'] = $value;

                // email/pec
                if (in_array($label, ['email','e-mail','mail','indirizzo email','email:'], true)) {
                    if (empty($profile['email']) && filter_var(trim($value), FILTER_VALIDATE_EMAIL)) $profile['email'] = trim($value);
                }
                if ($label === 'pec' && empty($profile['email']) && filter_var(trim($value), FILTER_VALIDATE_EMAIL)) {
                    $profile['email'] = trim($value);
                    $pecRaw = $value;
                }
            }

            // conta fasce
            $fasceCount = 0;
            for ($i = $startRow; $i <= $endRow; $i++) {
                $r0 = $this->norm((string)($rows[$i][0] ?? ''));
                if (str_starts_with($r0, 'fino a kg')) {
                    $fasceRow = $rows[$i];
                    for ($c = 1; $c < count($fasceRow); $c++) {
                        $cell = trim((string)($fasceRow[$c] ?? ''));
                        if ($cell !== '') $fasceCount++;
                    }
                    break;
                }
            }

            $out[] = [
                'ragione_sociale' => $profile['ragione_sociale'] ?? null,
                'piva'            => $profile['piva'] ?? null,
                'email'           => $profile['email'] ?? null,
                'citta'           => $profile['citta'] ?? null,
                'provincia'       => $profile['provincia'] ?? null,
                'cap'             => $profile['cap'] ?? null,
                'fasce_count'     => $fasceCount,
                'block'           => $b + 1,
            ];
        }

        return $out;
    }

    /** Solo parsing (no DB) per dry-run */
    public function parseOnly(Collection $sheet, ?string $forcedEmail = null, ?int $blockIndex = null): array
    {
        [$user, $thresholds, $prices] = $this->extract($sheet, $forcedEmail, $blockIndex);
        $rates = $this->buildIntervals($thresholds, $prices);
        return [$user['email'], ['rates_inserted' => count($rates)], ['user' => $user, 'rates' => $rates], $forcedEmail ? '(forzata)' : '(rilevata)'];
    }

    /** Parsing + scrittura DB */
    public function parseAndPersist(Collection $sheet, ?string $forcedEmail = null, ?int $blockIndex = null): array
    {
        [$profile, $thresholds, $prices] = $this->extract($sheet, $forcedEmail, $blockIndex);

        if (empty($profile['password'])) {
            // Lascio in chiaro: verrà hashata automaticamente dal tuo Model (se casts 'password' => 'hashed'),
            // altrimenti usa Hash::make qui.
            $profile['password'] = 'Temporanea123!';
        }

        $user = User::updateOrCreate(
            ['email' => $profile['email']],
            [
                'ragione_sociale' => $profile['ragione_sociale'] ?? null,
                'piva'            => $profile['piva'] ?? null,
                'codice_fiscale'  => $profile['codice_fiscale'] ?? null,
                'email'           => $profile['email'],
                'password'        => $profile['password'],
                'telefono'        => $profile['telefono'] ?? null,
                'indirizzo'       => $profile['indirizzo'] ?? null,
                'cap'             => $profile['cap'] ?? null,
                'citta'           => $profile['citta'] ?? null,
                'provincia'       => $profile['provincia'] ?? null,
                'note'            => $profile['note'] ?? null,
            ]
        );

        $intervals = $this->buildIntervals($thresholds, $prices);

        if (!$this->skipRatesDelete) {
            UsersRate::where('user_id', $user->id)->delete();
        }

        $inserted = 0;
        foreach ($intervals as [$min, $max, $price]) {
            UsersRate::create([
                'user_id'  => $user->id,
                'min_peso' => number_format($min, 2, '.', ''),
                'max_peso' => number_format($max, 2, '.', ''),
                'prezzo'   => number_format($price, 2, '.', ''),
            ]);
            $inserted++;
        }

        return [$user->email, ['rates_inserted' => $inserted], $forcedEmail ? '(forzata)' : '(rilevata)'];
    }

    /* =========================
       Core parsing
       ========================= */

    /**
     * Estrae profilo + righe fasce da Foglio1.
     * Se $blockIndex è valorizzato (1-based), usa QUEL blocco.
     * Altrimenti, se $forcedEmail è valorizzata, prova a trovare il blocco che la contiene.
     * Altrimenti usa il primo blocco.
     */
    protected function extract(Collection $sheet, ?string $forcedEmail = null, ?int $blockIndex = null): array
    {
        if ($sheet->isEmpty()) throw new \RuntimeException('Foglio1 vuoto.');

        $rows = $sheet->values();
        $rowCount = $rows->count();

        // -------- individua gli INIZI BLOCCO (label tollerante) --------
        $blockStarts = [];
        for ($i = 0; $i < $rowCount; $i++) {
            $label = $this->norm((string)($rows[$i][0] ?? ''));
            if (preg_match('/^ragione\s+sociale:?$/', $label)) {
                $blockStarts[] = $i;
            }
        }
        if (empty($blockStarts)) $blockStarts = [0];

        // costruisci [start,end] per ogni blocco
        $blocks = [];
        foreach ($blockStarts as $idx => $start) {
            $end = ($idx + 1 < count($blockStarts)) ? $blockStarts[$idx + 1] - 1 : ($rowCount - 1);
            $blocks[] = [$start, $end];
        }

        // -------- scegli il BLOCCO TARGET --------
        $targetBlockIndex = 0; // default: primo
        if ($blockIndex !== null) {
            $b = $blockIndex - 1;
            if ($b >= 0 && $b < count($blocks)) {
                $targetBlockIndex = $b;
            } else {
                throw new \RuntimeException("Blocco {$blockIndex} non esiste nel foglio (tot blocchi: ".count($blocks).").");
            }
        } elseif ($forcedEmail) {
            // cerca il blocco che contiene quella email
            $fe = trim($forcedEmail);
            for ($b = 0; $b < count($blocks); $b++) {
                [$bs, $be] = $blocks[$b];
                $found = false;
                for ($r = $bs; $r <= $be; $r++) {
                    $row = (array)($rows[$r] ?? []);
                    foreach ($row as $cell) {
                        $raw = $this->cellToString($cell ?? '');
                        if ($raw === '') continue;
                        if (strcasecmp($raw, $fe) === 0) { $found = true; break; }
                        if (strpos($raw, '@') !== false) {
                            $cand = preg_replace('/\s+/', '', $raw);
                            $cand = preg_replace('/^email\s*:\s*/i', '', $cand);
                            if (strcasecmp($cand, $fe) === 0) { $found = true; break; }
                        }
                    }
                    if ($found) break;
                }
                if ($found) { $targetBlockIndex = $b; break; }
            }
        }

        // -------- PARSA SOLO IL BLOCCO SCELTO --------
        [$startRow, $endRow] = $blocks[$targetBlockIndex];

        $profile = [];
        $notes   = [];
        $pecRaw  = null;

        for ($i = $startRow; $i <= $endRow; $i++) {
            $label = $this->norm((string)($rows[$i][0] ?? ''));
            $value = trim((string)($rows[$i][1] ?? ''));

            if ($label === '' && $value === '') continue;

            foreach ($this->map as $k => $field) {
                if ($label !== $k) continue;

                if ($field === '_note_sdi')          { if ($value !== '') $notes[] = 'SDI: '.$value; break; }
                if ($field === '_note_pec')          { if ($value !== '') { $notes[] = 'PEC: '.$value; $pecRaw = $value; } break; }
                if ($field === '_note_pagamento')    { if ($value !== '') $notes[] = 'Pagamento: '.$value; break; }

                // prendi SOLO la prima occorrenza del campo nel blocco
                if (!array_key_exists($field, $profile) || $profile[$field] === '') {
                    $profile[$field] = $value;
                }
                break;
            }
        }

        // normalizzazioni
        if (!empty($profile['cap'])) $profile['cap'] = $this->cleanCap($profile['cap']);
        if (!empty($profile['provincia'])) {
            $prov = trim((string)$profile['provincia']);
            if (mb_strtolower($prov) === 'roma') $profile['provincia'] = 'rm';
            else $profile['provincia'] = mb_strtoupper(mb_substr($prov, 0, 2));
        }
        if (!empty($notes)) $profile['note'] = implode(' | ', $notes);

        // email (forzata > PEC > scan > auto)
        if ($forcedEmail) {
            $profile['email'] = trim($forcedEmail);
        } else {
            if (empty($profile['email']) && $pecRaw && filter_var(trim($pecRaw), FILTER_VALIDATE_EMAIL)) {
                $profile['email'] = trim($pecRaw);
            }
            if (empty($profile['email'])) {
                // scan email solo nel blocco
                for ($i = $startRow; $i <= $endRow; $i++) {
                    $row = (array)($rows[$i] ?? []);
                    foreach ($row as $cell) {
                        $raw = $this->cellToString($cell ?? '');
                        if ($raw === '' || strpos($raw, '@') === false) continue;
                        $cand = preg_replace('/\s+/', '', $raw);
                        $cand = preg_replace('/^email\s*:\s*/i', '', $cand);
                        if (filter_var($cand, FILTER_VALIDATE_EMAIL)) { $profile['email'] = $cand; break 2; }
                    }
                }
            }
            if (empty($profile['email'])) {
                $auto = $this->autoEmailFrom($profile['ragione_sociale'] ?? null, $profile['piva'] ?? null);
                if ($auto) {
                    $profile['email'] = $auto;
                } else {
                    throw new \RuntimeException('Email mancante e impossibile generarne una tecnica.');
                }
            }
        }

        // -------- Fasce (prima riga "fino a kg" dentro o subito dopo il blocco) --------
        $thresholds = []; $prices = [];
        $fasceRowIndex = null;

        for ($i = $startRow; $i <= $endRow; $i++) {
            $r0 = $this->norm((string)($rows[$i][0] ?? ''));
            if (str_starts_with($r0, 'fino a kg')) { $fasceRowIndex = $i; break; }
        }
        if ($fasceRowIndex === null) {
            $limit = ($targetBlockIndex + 1 < count($blocks)) ? $blocks[$targetBlockIndex + 1][0] - 1 : ($rowCount - 1);
            for ($i = $endRow + 1; $i <= $limit; $i++) {
                $r0 = $this->norm((string)($rows[$i][0] ?? ''));
                if (str_starts_with($r0, 'fino a kg')) { $fasceRowIndex = $i; break; }
            }
        }

        if ($fasceRowIndex !== null) {
            $fasceRow = $rows[$fasceRowIndex];
            for ($c = 1; $c < count($fasceRow); $c++) {
                $cell = trim((string)($fasceRow[$c] ?? ''));
                if ($cell === '') continue;
                $nc = $this->norm($cell);
                if (str_contains($nc, 'oltre')) $thresholds[] = 'oltre';
                else {
                    $num = str_replace([' ', ','], ['', '.'], $cell);
                    if (is_numeric($num)) $thresholds[] = (float)$num;
                }
            }
            $costRowIndex = $fasceRowIndex + 1;
            if ($costRowIndex < $rowCount) {
                $costRow = $rows[$costRowIndex];
                for ($c = 1; $c < count($costRow); $c++) {
                    $cell = trim((string)($costRow[$c] ?? ''));
                    if ($cell === '') continue;
                    $prices[] = $this->cleanMoney($cell);
                }
            }
        }

        return [$profile, $thresholds, $prices];
    }

    /** Converte soglie+prezzi in intervalli [min..max] */
    protected function buildIntervals(array $thresholds, array $prices): array
    {
        $n = min(count($thresholds), count($prices));
        $thresholds = array_slice($thresholds, 0, $n);
        $prices     = array_slice($prices, 0, $n);

        $intervals = [];
        for ($k = 0; $k < $n; $k++) {
            $thr   = $thresholds[$k];
            $price = $prices[$k] ?? null;
            if ($price === null) continue;

            if ($k === 0 && is_numeric($thr)) {
                $min = 0.00; $max = (float)$thr;
            } elseif (is_numeric($thr)) {
                $prev = $thresholds[$k-1];
                $min = (is_numeric($prev) ? (float)$prev + 0.01 : 0.00);
                $max = (float)$thr;
            } else { // 'oltre'
                $prev = $thresholds[$k-1] ?? 0;
                $min = (is_numeric($prev) ? (float)$prev + 0.01 : 0.00);
                $max = 99999.00;
            }

            $intervals[] = [$min, $max, (float)$price];
        }
        return $intervals;
    }
}
