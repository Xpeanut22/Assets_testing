<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AssetModel;
use Illuminate\Support\Facades\File;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;
use Milon\Barcode\DNS2D;


class Scan extends Controller
{
    public function logScan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'No data received or invalid data'], 400);
        }

        $barcode_data = $request->input('barcode_data');

        try {
            DB::table('scans')->insert([
                'barcode_data' => $barcode_data,
                'scanned_at' => now(),
            ]);

            return response()->json(['success' => 'Data logged successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error logging data: ' . $e->getMessage()], 500);
        }
    }
}
