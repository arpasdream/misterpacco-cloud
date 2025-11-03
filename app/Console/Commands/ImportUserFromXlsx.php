<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\UsersWithRatesParser;

class ImportUserFromXlsx extends Command
{
    protected $signature = 'import:user-xlsx
                            {file : Percorso del file .xlsx (anche relativo a storage/app)}
                            {--email= : Email da usare/forzare per il cliente (per import singolo)}
                            {--block= : Numero del blocco nel Foglio1 (1=primo, 2=secondo, ...)}
                            {--list : Mostra un riepilogo di tutti i blocchi rilevati}
                            {--all : Importa tutti i blocchi (auto-email se manca)}
                            {--dry-run : Analizza senza scrivere su DB (solo per singolo)}
                            {--keep-rates : Non cancellare le fasce esistenti per quell\'utente}';

    protected $description = 'Importa da Foglio1 (anagrafica + fasce) in users e users_rates. Supporta --list e --all.';

    public function handle(): int
    {
        $pathArg = $this->argument('file');

        // risoluzione percorso
        $candidates = [
            $pathArg,
            base_path($pathArg),
            storage_path($pathArg),
            storage_path('app/' . ltrim($pathArg, '/')),
        ];
        $path = null;
        foreach ($candidates as $p) { if (is_file($p)) { $path = $p; break; } }
        if (!$path) { $this->error("File non trovato: {$pathArg}"); return self::FAILURE; }

        $forcedEmail = $this->option('email') ? trim((string)$this->option('email')) : null;
        if ($forcedEmail && !filter_var($forcedEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error("L'email forzata non è valida: {$forcedEmail}");
            return self::FAILURE;
        }

        $blockOpt   = $this->option('block');
        $wantList   = (bool)$this->option('list');
        $wantAll    = (bool)$this->option('all');
        $dryRun     = (bool)$this->option('dry-run');
        $keepRates  = (bool)$this->option('keep-rates');

        $this->info("Leggo: {$path}");

        $all = Excel::toCollection(null, $path);
        // Foglio1 (case-insensitive) o prima sheet
        $sheet = null;
        foreach ($all as $name => $s) {
            $lname = is_string($name) ? mb_strtolower($name) : '';
            if ($lname === 'foglio1' || $lname === 'sheet1') { $sheet = $s; break; }
        }
        if (!$sheet) { $sheet = $all->first(); }
        if (!$sheet || $sheet->isEmpty()) { $this->error('Foglio1 non trovato o vuoto.'); return self::FAILURE; }

        $parser = new UsersWithRatesParser();
        $parser->setSkipRatesDelete($keepRates);

        // ===== LIST: anteprima di tutti i blocchi
        if ($wantList) {
            $blocks = $parser->listBlocks($sheet);
            $this->line("Blocchi trovati: ".count($blocks));
            foreach ($blocks as $i => $info) {
                $idx = $i+1;
                $this->line(str_repeat('-', 60));
                $this->line("Blocco #{$idx}");
                $this->line("  Ragione sociale: ".($info['ragione_sociale'] ?? '—'));
                $this->line("  Email/PEC: ".(($info['email'] ?? '') ?: '—'));
                $this->line("  Città/Prov: ".(($info['citta'] ?? '—')." / ".($info['provincia'] ?? '—')));
                $this->line("  CAP: ".(($info['cap'] ?? '—')));
                $this->line("  Fasce rilevate: ".$info['fasce_count']);
            }
            $this->line(str_repeat('-', 60));
            $this->info("Usa --all per importare tutto. Oppure --block=N (--dry-run / --email=...) per singolo.");
            return self::SUCCESS;
        }

        // ===== ALL: importa TUTTI i blocchi (con auto-email se manca)
        if ($wantAll) {
            $blocks = $parser->listBlocks($sheet);
            $ok = 0; $skipped = 0; $failed = 0;
            foreach ($blocks as $i => $info) {
                $bIndex = $i+1;
                // email priorità: campo/pec da preview, altrimenti auto
                $email  = $info['email'] ?? null;
                if (!$email) {
                    $auto = $parser->autoEmailFrom($info['ragione_sociale'] ?? null, $info['piva'] ?? null);
                    if ($auto) $email = $auto;
                }
                if (!$email) {
                    $this->warn("⏭️  Salto blocco #{$bIndex} (impossibile determinare email tecnica).");
                    $skipped++; continue;
                }
                $this->line("▶️  Import blocco #{$bIndex} — ".(($info['ragione_sociale'] ?? 'SENZA RS'))." ({$email})");
                try {
                    [$outEmail, $stats, $src] = $parser->parseAndPersist($sheet, $email, $bIndex);
                    $this->line("   ✅ {$outEmail} {$src} | fasce: ".$stats['rates_inserted']);
                    $ok++;
                } catch (\Throwable $e) {
                    $this->error("   ❌ Errore blocco #{$bIndex}: ".$e->getMessage());
                    $failed++;
                }
            }
            $this->info("=== RISULTATO: OK={$ok}  SKIPPED={$skipped}  FAILED={$failed}  (tot blocchi=".count($blocks).")");
            return ($failed>0) ? self::FAILURE : self::SUCCESS;
        }

        // ===== SINGOLO: block/email/dry-run
        $blockIndex = null;
        if ($blockOpt !== null && $blockOpt !== '') {
            if (!ctype_digit((string)$blockOpt) || (int)$blockOpt < 1) {
                $this->error("--block deve essere un intero >= 1");
                return self::FAILURE;
            }
            $blockIndex = (int)$blockOpt;
        }

        if ($dryRun) {
            [$email, $stats, $preview, $source] = $parser->parseOnly($sheet, $forcedEmail, $blockIndex);
            $this->line("DRY-RUN ► email: {$email} {$source}");
            $this->line("DRY-RUN ► utente: ".json_encode($preview['user'], JSON_UNESCAPED_UNICODE));
            $this->line("DRY-RUN ► fasce trovate: ".count($preview['rates']));
            return self::SUCCESS;
        }

        [$email, $stats, $source] = $parser->parseAndPersist($sheet, $forcedEmail, $blockIndex);
        $this->info("✅ Import completato per {$email} {$source}");
        $this->line("Fasce inserite: {$stats['rates_inserted']}");
        return self::SUCCESS;
    }
}
