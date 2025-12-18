<?php


namespace App\Http\Controllers;

require 'vendor/autoload.php';

use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Controllers\DateTime;


class ExcelPrint extends Controller
{

    // public function exportexcel($from, $to) {
    //     // Fetch the data
    //     $data = DB::table('inventory')->whereBetween('created_at', [$from, $to])->get();

    //     // Create a new Spreadsheet object
    //     echo $data;

    // }

    public function exportexcel()
    {
     

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');
    }
}
