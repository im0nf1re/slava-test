<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Jobs\ParseExcelJob;

class ExcelController extends Controller
{
    public function upload(UploadRequest $request)
    {
        $file = $request->file('file');
        $filePath = $file->store('uploads', 'public');

        ParseExcelJob::dispatch($filePath, 1000);

        return response()->json(['message' => 'File uploaded and parsing started.']);
    }
}
