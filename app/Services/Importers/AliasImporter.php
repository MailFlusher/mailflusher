<?php

namespace App\Services\Importers;

use App\Models\User;

interface AliasImporter
{
    /**
     * Count how many aliases would be imported without writing anything.
     *
     * @return array{total:int, importable:int, skipped:int, samples:array<array{email:string,description:?string,active:bool}>}
     */
    public function dryRun(User $user, string $token): array;

    /**
     * Actually import. Respects the user's alias limit; stops early and
     * returns counts when the limit is reached.
     *
     * @return array{imported:int, skipped_over_limit:int, errors:array<string>}
     */
    public function import(User $user, string $token): array;

    /**
     * Short machine-readable identifier for this importer: "simplelogin", "addy", etc.
     */
    public function slug(): string;

    /**
     * Human-readable label: "SimpleLogin", "Addy.io".
     */
    public function name(): string;
}
