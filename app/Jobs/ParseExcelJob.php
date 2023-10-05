<?php

namespace App\Jobs;

use App\Services\ExcelParsing\ExcelParsingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ParseExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $filePath,
        private readonly int $batchSize
    )
    {}

    /**
     * @throws \RedisException
     */
    public function handle(ExcelParsingService $parsingService): void
    {
        $progressKey = 'excel_parsing_'.$this->filePath;
        $parsedRows = Redis::get($progressKey);

        if ($parsedRows) {
            $startRow = $parsedRows;
        } else {
            $startRow = 1;
        }

        $parsingCompleted = $parsingService->parseAndSaveData(
            Storage::disk('public')->path($this->filePath),
            $startRow,
            $this->batchSize
        );

        if (!$parsingCompleted) {
            Redis::set($progressKey, $startRow + $this->batchSize);
            self::dispatch($this->filePath, $this->batchSize);
        } else {
            Redis::del($progressKey);
        }
    }
}
