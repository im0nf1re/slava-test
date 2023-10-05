<?php

namespace App\Services\ExcelParsing;

use App\Models\Row;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelParsingService
{
    public string $fileType = 'Xlsx';

    /**
     * @throws Exception
     */
    public function parseAndSaveData(string $filePath, int $startRow, int $batchSize): bool
    {
        $reader = IOFactory::createReader($this->fileType);
        $reader->setReadFilter(new DataFilter($startRow, $batchSize));
        $spreadsheet = $reader->load($filePath);

        $worksheet = $spreadsheet->getActiveSheet();

        $parsingCompleted = false;
        for ($i = $startRow + 1; $i <= $startRow + $batchSize; $i++) {
            $externalId = $worksheet->getCell('A' . $i)->getCalculatedValue();
            $name = $worksheet->getCell('B' . $i)->getCalculatedValue();
            $date = $worksheet->getCell('C' . $i)->getFormattedValue();

            if ($externalId === 'NULL' || $name === 'NULL' || $date === '') {
                $parsingCompleted = true;
                break;
            }

            Row::updateOrCreate(
                ['external_id' => $externalId],
                ['name' => $name, 'date' => Carbon::createFromFormat('d.m.y', $date)]
            );
        }

        return $parsingCompleted;
    }
}
