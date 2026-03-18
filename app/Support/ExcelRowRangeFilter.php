<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ExcelRowRangeFilter implements IReadFilter
{
    public function __construct(
        private int $startRow,
        private int $endRow,
    ) {}

    public function readCell($columnAddress, $row, $worksheetName = ''): bool
    {
        return $row === 1 || ($row >= $this->startRow && $row <= $this->endRow);
    }
}
