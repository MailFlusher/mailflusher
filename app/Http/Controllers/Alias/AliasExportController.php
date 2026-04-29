<?php

namespace App\Http\Controllers\Alias;

use App\Http\Controllers\Controller;

use App\Exports\AliasesExport;
use Maatwebsite\Excel\Facades\Excel;

class AliasExportController extends Controller
{
    public function export()
    {
        if (! user()->allAliases()->count()) {
            return back()->withErrors(['aliases_export' => 'You don\'t have any aliases to export.']);
        }

        return Excel::download(new AliasesExport, 'aliases-'.now()->toDateString().'.csv');
    }
}
