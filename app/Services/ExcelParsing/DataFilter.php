<?php

namespace App\Services\ExcelParsing;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class DataFilter implements IReadFilter
{
    public function __construct(
        private readonly int $startRow,
        private readonly int $batchSize,
    )
    {}

    public function readCell($columnAddress, $row, $worksheetName = '') {
        return $row >= $this->startRow && $row <= $this->startRow + $this->batchSize;
    }
}
