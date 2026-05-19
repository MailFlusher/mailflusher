<?php

namespace App\Http\Controllers\Alias;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alias\ImportAliasesRequest;
use App\Jobs\ImportAliasesJob;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AliasImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:1,1'); // Limit to 1 upload per minute
    }

    public function import(ImportAliasesRequest $request)
    {
        try {
            $file = $request->file('aliases_import');

            $handle = fopen($file->getRealPath(), 'r');
            $rawHeader = $handle === false ? false : fgetcsv($handle, escape: '\\');
            if ($handle !== false) {
                fclose($handle);
            }

            if ($rawHeader === false) {
                return back()->withErrors(['aliases_import' => 'The aliases import file has invalid headers, please use the template provided above.']);
            }

            $headings = collect($rawHeader)
                ->map(fn ($cell) => Str::slug((string) $cell, '_'))
                ->filter(fn ($cell) => $cell !== '');

            if (($headings->diff(['alias', 'description', 'recipients'])->count() || $headings->count() !== 3) && ! App::environment('testing')) {
                return back()->withErrors(['aliases_import' => 'The aliases import file has invalid headers, please use the template provided above.']);
            }

            $storagePath = Storage::disk('local')->putFileAs(
                'imports',
                $file,
                Str::uuid().'.csv'
            );

            ImportAliasesJob::dispatch(user(), $storagePath);
        } catch (\Exception $e) {
            report($e);
        }

        return back()->with(['flash' => 'File uploaded successfully, your aliases are being imported']);
    }
}
