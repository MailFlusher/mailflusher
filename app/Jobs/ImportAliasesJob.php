<?php

namespace App\Jobs;

use App\Models\Alias;
use App\Models\User;
use App\Notifications\Alias\AliasesImportedNotification;
use App\Rules\Alias\ValidAliasLocalPart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Ramsey\Uuid\Uuid;
use Throwable;

class ImportAliasesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    protected string $storagePath;

    protected Collection $domains;

    protected Collection $verifiedRecipientEmailAndIds;

    protected int $totalRows = 0;

    /** @var array<int, array{row: int, errors: array<int, string>}> */
    protected array $failures = [];

    /** @var array<int, array{row: int, message: string}> */
    protected array $errors = [];

    public function __construct(User $user, string $storagePath)
    {
        $this->user = $user;
        $this->storagePath = $storagePath;

        $this->domains = $user->domains()->select(['id', 'domain'])->get();

        $this->verifiedRecipientEmailAndIds = $user
            ->verifiedRecipients()
            ->select(['email', 'id'])
            ->get()
            ->mapWithKeys(function ($recipient) {
                return [$recipient->email => $recipient->id];
            });
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->user->id))->releaseAfter(180)->expireAfter(600)];
    }

    public function handle(): void
    {
        try {
            $this->process();
            $this->notifyUser();
        } catch (Throwable $e) {
            Log::info('AliasesImport Failure:', ['exception' => $e]);
            throw $e;
        } finally {
            Storage::disk('local')->delete($this->storagePath);
        }
    }

    public function failed(Throwable $e): void
    {
        Log::info('AliasesImport Failure:', ['exception' => $e]);
        Storage::disk('local')->delete($this->storagePath);
    }

    protected function process(): void
    {
        $absolutePath = Storage::disk('local')->path($this->storagePath);

        $handle = @fopen($absolutePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException('Could not open import file: '.$this->storagePath);
        }

        try {
            $rawHeader = fgetcsv($handle, escape: '\\');
            if ($rawHeader === false) {
                throw new \RuntimeException('Import file is empty');
            }

            $header = array_map(fn ($cell) => Str::slug((string) $cell, '_'), $rawHeader);
            $header = array_slice($header, 0, 3);
            $header = array_pad($header, 3, '');

            $rowNumber = 1;

            while (($row = fgetcsv($handle, escape: '\\')) !== false) {
                $rowNumber++;

                if ($this->totalRows >= 1000) {
                    break;
                }

                if (collect($row)->filter(fn ($v) => $v !== null && $v !== '')->isEmpty()) {
                    continue;
                }

                $this->totalRows++;

                $row = array_slice($row, 0, 3);
                $row = array_pad($row, 3, null);
                $data = array_combine($header, $row);

                $prepared = $this->prepareRow($data);

                $validator = Validator::make($prepared, $this->rules());

                if ($validator->fails()) {
                    $this->failures[] = [
                        'row' => $rowNumber,
                        'errors' => $validator->errors()->all(),
                    ];

                    continue;
                }

                try {
                    $this->createAlias($prepared);
                } catch (Throwable $e) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'message' => $e->getMessage(),
                    ];
                }
            }
        } finally {
            fclose($handle);
        }
    }

    protected function prepareRow(array $data): array
    {
        $alias = isset($data['alias']) ? trim(strtolower((string) $data['alias'])) : '';
        $domain = Str::afterLast($alias, '@');
        $localPart = Str::beforeLast($alias, '@');

        $extension = null;
        if (Str::contains($localPart, '+')) {
            $extension = Str::after($localPart, '+');
            $localPart = Str::before($localPart, '+');
        }

        $description = $data['description'] ?? null;
        if (! is_null($description)) {
            $description = (string) $description;
        }

        $recipientIds = null;
        if (! empty($data['recipients'])) {
            foreach (explode(',', (string) $data['recipients']) as $recipient) {
                $recipient = trim($recipient);
                if (isset($this->verifiedRecipientEmailAndIds[$recipient])) {
                    $recipientIds[] = $this->verifiedRecipientEmailAndIds[$recipient];
                }
            }
        }

        return [
            'alias' => $alias,
            'email' => $localPart.'@'.$domain,
            'local_part' => $localPart,
            'extension' => $extension,
            'domain' => $domain,
            'description' => $description,
            'recipient_ids' => $recipientIds,
        ];
    }

    protected function rules(): array
    {
        return [
            'alias' => ['bail', 'required', 'email', 'max:254', 'string'],
            'email' => ['bail', 'required', 'max:254', 'string'],
            'local_part' => ['bail', 'required', 'max:64', 'string', new ValidAliasLocalPart],
            'domain' => ['bail', 'required', 'string', Rule::in($this->domains->pluck('domain') ?? [])],
            'description' => ['bail', 'nullable', 'string', 'max:200'],
            'recipient_ids' => ['bail', 'nullable', 'array', 'max:10'],
            'recipient_ids.*' => ['uuid'],
        ];
    }

    protected function createAlias(array $row): void
    {
        $aliasable = $this->domains->firstWhere('domain', $row['domain']);
        if (! $aliasable) {
            return;
        }

        $alias = new Alias([
            'id' => Uuid::uuid4(),
            'user_id' => $this->user->id,
            'email' => $row['local_part'].'@'.$row['domain'],
            'local_part' => $row['local_part'],
            'extension' => $row['extension'],
            'domain' => $row['domain'],
            'description' => $row['description'],
            'aliasable_id' => $aliasable->id,
            'aliasable_type' => 'App\\Models\\Domain',
        ]);

        $alias->save();

        if ($row['recipient_ids']) {
            $alias->recipients()->sync($row['recipient_ids']);
        }
    }

    protected function notifyUser(): void
    {
        $totalFailures = collect($this->failures)->groupBy('row')->count();
        $totalErrors = count($this->errors);
        $totalNotImported = $totalFailures + $totalErrors;
        $totalImported = $this->totalRows - $totalNotImported;

        $this->user->notify(new AliasesImportedNotification(
            $this->totalRows,
            $totalImported,
            $totalNotImported,
            $totalFailures,
            $totalErrors
        ));
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getDomains(): Collection
    {
        return $this->domains;
    }

    public function getRecipientIds(): array
    {
        return $this->verifiedRecipientEmailAndIds->values()->all();
    }
}
