<?php
/**
 * Cron "a rate" per invio newsletter SENZA Laravel Queue.
 * - Va messo nella root progetto (dove c'è .env e artisan)
 * - Da lanciare via cron CLI: php cron_send_newsletter.php
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleMail;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "[".date('Y-m-d H:i:s')."] Avvio cron newsletter\n";

// ------- Parametri fissi -------
$limit    = 50;  // quante email per run
$interval = 1;   // secondi di pausa tra invii
$lockPath = storage_path('app/cron_newsletter.lock');
// -------------------------------

// ------- Lock ------------
if (!is_dir(dirname($lockPath))) {
    @mkdir(dirname($lockPath), 0775, true);
}
$fp = fopen($lockPath, 'c+');
if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) {
    echo "Un altro cron è in esecuzione, esco\n";
    exit;
}
// -------------------------

try {
    $rows = DB::table('newsletter_inviate_mail')
        ->where('stato', 'queued')
        ->orderBy('id')
        ->limit($limit)
        ->get();

    if ($rows->isEmpty()) {
        echo "Niente da inviare\n";
        exit;
    }

    $ok = 0; $err = 0;

    foreach ($rows as $row) {
        $nl = DB::table('newsletter')->where('id', $row->newsletterID)->first();
        if (!$nl) {
            DB::table('newsletter_inviate_mail')->where('id', $row->id)->update(['stato' => 'errore']);
            $err++;
            continue;
        }

        $content = [
            'subject' => $nl->oggetto ?? 'Newsletter',
            'body'    => $nl->corpo ?? '',
            'email'   => $row->email,
        ];

        try {
            Mail::to($row->email)->send(new SampleMail($content));
            DB::table('newsletter_inviate_mail')->where('id', $row->id)->update(['stato' => 'inviata']);
            $ok++;
            echo "OK -> {$row->email}\n";
        } catch (\Throwable $e) {
            DB::table('newsletter_inviate_mail')->where('id', $row->id)->update(['stato' => 'errore']);
            $err++;
            echo "ERR -> {$row->email} : ".$e->getMessage()."\n";
        }

        if ($interval > 0) {
            sleep($interval); // throttling
        }
    }

    $remain = DB::table('newsletter_inviate_mail')->where('stato', 'queued')->count();
    echo "Fatte: $ok inviate, $err errori. Rimaste in coda: $remain\n";

} finally {
    if ($fp) {
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
