<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        return DB::table('orelavori')
            ->join('reg_anacom', 'reg_anacom.cky_comssa', '=', 'orelavori.commessa')
            ->join('reg_cdgananl', 'reg_cdgananl.id', '=', 'orelavori.tipoAttivita')
            ->join('utenti', 'utenti.id', '=', 'orelavori.utente')
            ->select([
                "orelavori.data",
                "reg_anacom.cds_comssa",
                "reg_cdgananl.CDS_ANAL",
                "reg_anacom.cky_cnt_des",
                "utenti.nome",
                "orelavori.ore_lavorate",
            ])
            ->orderBy('data', 'desc')
            ->orderBy('utente', 'desc')
            ->get();

    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["Data", "Commessa", "Attività", "Cliente", "Utente", "Minuti"];
    }
}
