<?php

namespace App\Jobs;

use App\Imports\MembersImport;
use App\Models\ImportResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportMembersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 0;
    public int $tries   = 1;

    public function __construct(
        private string $storedPath,
        private int    $userId,
        private int    $importResultId,
    ) {}

    public function handle(): void
    {
        ini_set('memory_limit', '-1');

        $import = new MembersImport($this->userId);
        Excel::import($import, $this->storedPath, 'local');

        ImportResult::find($this->importResultId)?->update([
            'status'   => 'done',
            'imported' => $import->imported,
            'skipped'  => $import->skipped,
            'errors'   => $import->errors,
        ]);

        Storage::disk('local')->delete($this->storedPath);
    }

    public function failed(\Throwable $e): void
    {
        ImportResult::find($this->importResultId)?->update([
            'status' => 'failed',
            'errors' => [['message' => $e->getMessage()]],
        ]);
    }
}
