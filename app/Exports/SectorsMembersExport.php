<?php

namespace App\Exports;

use App\Models\Sector;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SectorsMembersExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(protected array $ids = []) {}

    public function sheets(): array
    {
        $query = Sector::active()
            ->with(['members' => fn($q) => $q->with('paymentInfo')->orderByRaw('CAST(dossier_number AS UNSIGNED)')])
            ->orderBy('name');

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        return $query->get()->map(fn($s) => new SectorSheetExport($s))->all();
    }
}
