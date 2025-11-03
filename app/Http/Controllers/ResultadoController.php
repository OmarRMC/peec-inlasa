<?php

namespace App\Http\Controllers;


use App\Exports\ResultadosExport;
use Maatwebsite\Excel\Facades\Excel;

class ResultadoController extends Controller
{
    public function export($id)
    {
        return Excel::download(new ResultadosExport($id), 'resultados.xlsx');
    }
}
