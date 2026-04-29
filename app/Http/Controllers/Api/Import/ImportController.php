<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Services\Importers\AddyImporter;
use App\Services\Importers\AliasImporter;
use App\Services\Importers\SimpleLoginImporter;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function dryRun(Request $request)
    {
        $request->validate([
            'service' => 'required|string|in:simplelogin,addy',
            'token' => 'required|string|min:4|max:500',
        ]);

        $importer = $this->resolveImporter($request->input('service'));

        try {
            $result = $importer->dryRun(user(), $request->input('token'));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'service' => $importer->slug(),
            ...$result,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'service' => 'required|string|in:simplelogin,addy',
            'token' => 'required|string|min:4|max:500',
        ]);

        $importer = $this->resolveImporter($request->input('service'));

        try {
            $result = $importer->import(user(), $request->input('token'));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'service' => $importer->slug(),
            ...$result,
        ]);
    }

    protected function resolveImporter(string $slug): AliasImporter
    {
        return match ($slug) {
            'simplelogin' => app(SimpleLoginImporter::class),
            'addy' => app(AddyImporter::class),
            default => abort(422, 'Unknown service'),
        };
    }
}
