<?php

namespace App\Http\Controllers;

use App\Models\Row;

class RowsController extends Controller
{
    public function groupByDate()
    {
        $rows = Row::all()->groupBy('date');

        return response()->json(['data' => $rows]);
    }
}
