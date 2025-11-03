<?php

namespace App\Imports;

use App\Models\Mail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MailImport implements ToCollection, WithHeadingRow
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function collection(Collection $rows)
    {
        $rows->each(function($row, $key) {

            // Verifica se la chiave 'email' esiste nel row
            if (isset($row['email'])) {

                // Controllo se l'email è valida
                if (filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {

                    // Verifica se l'email non esiste già nel database
                    $mailinglist = DB::table('mailinglist_mail')
                        ->where('email', '=', $row['email'])
                        ->first();

                    if (!$mailinglist) {
                        // Crea una nuova entry nel database
                        Mail::create(array_merge([
                            'email' => $row['email'],
                        ], $this->data));
                    }

                }

            }

        });
    }
}
