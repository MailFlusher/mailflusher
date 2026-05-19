<?php

namespace App\Http\Controllers\Alias;

use App\Http\Controllers\Controller;

class AliasExportController extends Controller
{
    public function export()
    {
        if (! user()->allAliases()->count()) {
            return back()->withErrors(['aliases_export' => 'You don\'t have any aliases to export.']);
        }

        $filename = 'aliases-'.now()->toDateString().'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');

            $first = true;
            foreach (user()->aliases()->withTrashed()->cursor() as $alias) {
                $row = $alias->toArray();

                if ($first) {
                    fputcsv($out, array_keys($row), escape: '\\');
                    $first = false;
                }

                fputcsv($out, array_values($row), escape: '\\');
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
