<?php

namespace App\Imports;

use App\Models\Mail;
use App\UnitType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class failSendMailImport implements ToCollection, withHeadingRow
{
    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function collection(Collection $rows)
    {
        $rows->each(function($row, $key) {

            $mailinglist = DB::table('mailinglist_mail')
                ->where('email', '=', $row['email'])
                ->first();

            if($mailinglist) {

                // modifica mail
                $data = array('disiscritto'=>1);
                DB::table('mailinglist_mail')
                    ->where('email', $row['email'])
                    ->update($data);

            }

        });

    }

}
