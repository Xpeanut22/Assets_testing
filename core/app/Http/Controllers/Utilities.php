<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\GoalModel;
use App\SettingModel;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use DB;
use Auth;
use App;


class Utilities extends Controller
{

    use TraitSettings;
    use TraitAuditTrail;

    private function officialFooterPath()
    {
        return resource_path('views/component/Munti_IssuanceForm_AMS/Munti_IssuanceForm_AMS/CGM FOOTER.png');
    }

    private function drawOfficialFooter($pdf, $height = 18)
    {
        $footer = $this->officialFooterPath();
        if (!file_exists($footer)) {
            return;
        }

        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();
        $y = max(0, $pageHeight - $height);
        $pdf->Image($footer, 0, $y, $pageWidth, $height);
    }

    public function __construct()
    {
        $data = $this->getapplications();
        $lang = $data->language;
        App::setLocale($lang);
        $this->middleware('auth');
    }

    public function assetactivity()
    {
        return view('reports.assetactivitys');
    }

    public function componentactivity()
    {
        return view('reports.componentactivity');
    }

    public function maintenance()
    {
        return view('reports.maintenance');
    }

    public function bytype()
    {
        return view('reports.bytype');
    }

    public function bysupplier()
    {
        return view('reports.bysupplier');
    }

    public function bylocation()
    {
        return view('reports.bylocation');
    }

    public function bystatus()
    {
        return view('reports.bystatus');
    }

    //show all report report view
    public function allutilities()
    {
        return view('utilities.utilities');
    }

    private function utilityPrintConfigs()
    {
        return [
            'assettype' => [
                'title' => 'Asset Type List',
                'table' => 'asset_type',
                'columns' => [
                    'Asset Type' => 'name',
                    'Description' => 'description',
                ],
            ],
            'brand' => [
                'title' => 'Brand List',
                'table' => 'brand',
                'columns' => [
                    'Brand Name' => 'name',
                    'Description' => 'description',
                    'Brand Type' => 'type',
                ],
            ],
            'supplier' => [
                'title' => 'Supplier List',
                'table' => 'supplier',
                'columns' => [
                    'Supplier Name' => 'name',
                    'Contact Person' => 'contact_person',
                    'Phone' => 'phone',
                    'Email' => 'email',
                    'Address' => 'address',
                ],
            ],
            'receiver' => [
                'title' => 'Receiver List',
                'table' => 'receiver',
                'columns' => [
                    'Full Name' => 'fullname',
                    'Mobile No.' => 'mobile_number',
                    'Email' => 'email',
                    'Address' => 'address',
                    'Description' => 'description',
                ],
            ],
            'used' => [
                'title' => 'Use of Equipment List',
                'table' => 'used',
                'columns' => [
                    'Name' => 'name',
                    'Description' => 'description',
                ],
            ],
            'typeofid' => [
                'title' => 'Type of I.D List',
                'table' => 'typeofid',
                'columns' => [
                    'I.D Name' => 'name',
                    'Description' => 'description',
                ],
            ],
            'location' => [
                'title' => 'Location List',
                'table' => 'location',
                'columns' => [
                    'Location' => 'name',
                    'Description' => 'description',
                ],
            ],
            'department' => [
                'title' => 'Department List',
                'table' => 'department',
                'columns' => [
                    'Department' => 'name',
                    'Description' => 'description',
                ],
            ],
            'unit' => [
                'title' => 'Unit List',
                'table' => 'unit',
                'columns' => [
                    'Unit' => 'name',
                    'Description' => 'description',
                ],
            ],
            'category' => [
                'title' => 'Category List',
                'table' => 'category',
                'columns' => [
                    'Category' => 'category',
                    'Description' => 'description',
                ],
            ],
            'employee' => [
                'title' => 'Client List',
                'table' => 'employees',
                'columns' => [
                    'Full Name' => 'fullname',
                    'Email' => 'email',
                    'Mobile No.' => 'mobile_number',
                    'Job Role' => 'jobrole',
                    'Department' => 'departmentname',
                    'City' => 'city',
                ],
                'joins' => function ($query) {
                    return $query->leftJoin('department', 'department.id', '=', 'employees.departmentid')
                        ->addSelect('department.name as departmentname');
                },
            ],
        ];
    }

    private function utilityPdfFit($pdf, $value, $width)
    {
        $value = preg_replace('/\s+/', ' ', trim((string) $value));
        if ($value === '') {
            return '-';
        }

        $maxWidth = max(1, $width - 2);
        if ($pdf->GetStringWidth($value) <= $maxWidth) {
            return $value;
        }

        while (strlen($value) > 0 && $pdf->GetStringWidth($value . '...') > $maxWidth) {
            $value = substr($value, 0, -1);
        }

        return trim($value) . '...';
    }

    private function drawUtilityPdfHeader($pdf, $title)
    {
        $pdf->AddPage();
        $this->drawOfficialFooter($pdf);
        $pageWidth = $pdf->GetPageWidth();
        $muntiLogo = app_path('fpdf/muntilogo.png');
        $ddrmLogo = app_path('fpdf/drlogo.png');

        if (file_exists($muntiLogo)) {
            $pdf->Image($muntiLogo, 16, 5, 22, 22);
        }
        if (file_exists($ddrmLogo)) {
            $pdf->Image($ddrmLogo, $pageWidth - 38, 5, 22, 22);
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(0, 7);
        $pdf->Cell($pageWidth, 4, 'Republic of the Philippines', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($pageWidth, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($pageWidth, 4, 'City of Muntinlupa', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($pageWidth, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell($pageWidth, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
        $pdf->Cell($pageWidth, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
        $pdf->Cell($pageWidth, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

        $pdf->SetLineWidth(0.5);
        $pdf->Line(12, 38, $pageWidth - 12, 38);
        $pdf->Line(12, 40, $pageWidth - 12, 40);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($pageWidth - 72, 47);
        $pdf->Cell(14, 5, 'DATE:', 0, 0, 'R');
        $pdf->Cell(30, 5, date('Y-m-d'), 'B', 0, 'C');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetXY(0, 60);
        $pdf->Cell($pageWidth, 6, strtoupper($title), 0, 1, 'C');

        return 76;
    }

    public function printutility($type)
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $configs = $this->utilityPrintConfigs();
        if (!isset($configs[$type])) {
            abort(404);
        }

        $config = $configs[$type];
        $this->auditTrail('Utilities', 'Print', 'Printed utility list: '.$config['title'].'.', 'Utilities', $type);
        $columns = $config['columns'];
        $orientation = count($columns) > 4 ? 'L' : 'P';
        $pdf = new \FPDF($orientation, 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);

        $table = $config['table'];
        $select = [$table . '.*'];
        $query = DB::table($table)->select($select);
        if (isset($config['joins']) && is_callable($config['joins'])) {
            $query = $config['joins']($query);
        }

        if (DB::getSchemaBuilder()->hasColumn($table, 'is_delete')) {
            $query->where(function ($query) use ($table) {
                $query->where($table . '.is_delete', 0)->orWhereNull($table . '.is_delete');
            });
        }

        if (DB::getSchemaBuilder()->hasColumn($table, 'created_at')) {
            $query->orderBy($table . '.created_at', 'desc');
        } elseif (DB::getSchemaBuilder()->hasColumn($table, 'name')) {
            $query->orderBy($table . '.name');
        }

        $rows = $query->get();
        $y = $this->drawUtilityPdfHeader($pdf, $config['title']);
        $pageWidth = $pdf->GetPageWidth();
        $usableWidth = $pageWidth - 20;
        $noWidth = 12;
        $dataWidth = ($usableWidth - $noWidth) / max(1, count($columns));

        $drawTableHeader = function () use ($pdf, $columns, $noWidth, $dataWidth, &$y) {
            $pdf->SetFillColor(41, 158, 190);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(10, $y);
            $pdf->Cell($noWidth, 8, 'No.', 1, 0, 'C', true);
            foreach (array_keys($columns) as $header) {
                $pdf->Cell($dataWidth, 8, $header, 1, 0, 'C', true);
            }
            $pdf->Ln();
            $y += 8;
        };

        $drawTableHeader();
        foreach ($rows as $index => $row) {
            if ($y + 8 > ($pdf->GetPageHeight() - 12)) {
                $pdf->AddPage();
                $y = 12;
                $drawTableHeader();
            }

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(10, $y);
            $pdf->Cell($noWidth, 8, ($index + 1) . '.', 1, 0, 'C');
            foreach ($columns as $field) {
                $value = isset($row->{$field}) ? $row->{$field} : '-';
                $pdf->Cell($dataWidth, 8, $this->utilityPdfFit($pdf, $value, $dataWidth), 1, 0, 'L');
            }
            $pdf->Ln();
            $y += 8;
        }

        if ($rows->isEmpty()) {
            $pdf->SetXY(10, $y);
            $pdf->Cell($usableWidth, 8, 'No records found.', 1, 1, 'C');
        }

        $filename = strtolower(str_replace(' ', '_', $config['title'])) . '.pdf';
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }


    /**
     * get data asset from database
     * @return object
     */
    public function getassetactivityreport()
    {
        $data = DB::table('asset_history')
            ->select('asset_history.*', 'assets.name as asset', 'employees.fullname as employees', 'asset_type.name as type', 'location.name as location')
            ->leftJoin('assets', 'assets.id', '=', 'asset_history.assetid')
            ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('employees', 'employees.id', '=', 'asset_history.employeeid')
            ->leftJoin('location', 'location.id', '=', 'assets.locationid')
            ->orderBy('asset_history.updated_at', 'desc')
            ->orderBy('asset_history.created_at', 'desc')
            ->get();

        return Datatables::of($data)

            ->addColumn('status', function ($accountsingle) {


                if ($accountsingle->status == 4) {
                    $status = '<span class="badge badge-data text-white background-red">' . trans('lang.unserviceable') . '</span>';
                } elseif ($accountsingle->status == 3) {
                    $status = '<span class="badge badge-data text-white background-green">' . trans('lang.serviceable') . '</span>';
                } elseif ($accountsingle->status == 2) {

                    $status = '<span class="badge badge-data text-white background-blue">' . trans('lang.checkin') . '</span>';
                } else {
                    $status = '<span class="badge badge-data text-white background-yellow">' . trans('lang.checkout') . '</span>';
                }

                return  $status;
            })->rawColumns(['status'])
            ->make(true);
    }

    /**
     * get data  component from database
     * @return object
     */
    public function getcomponentactivityreport()
    {
        $data = DB::table('component_assets')
            ->select('component_assets.*', 'component.name as component', 'assets.name as asset', 'asset_type.name as type', 'location.name as location')
            ->leftJoin('assets', 'assets.id', '=', 'component_assets.assetid')
            ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('location', 'location.id', '=', 'assets.locationid')
            ->leftJoin('component', 'component.id', '=', 'component_assets.componentid')
            ->offset(0)->limit(10)
            ->orderBy('component_assets.updated_at', 'desc')
            ->orderBy('component_assets.created_at', 'desc')
            ->get();

        return Datatables::of($data)

            ->addColumn('status', function ($accountsingle) {


                if ($accountsingle->status == 2) {

                    $status = '<span class="badge badge-data text-white background-blue">' . trans('lang.checkin') . '</span>';
                } else {
                    $status = '<span class="badge badge-data text-white background-yellow">' . trans('lang.checkout') . '</span>';
                }

                return  $status;
            })->rawColumns(['status'])
            ->make(true);
    }


    /**
     * get asset data by type from database
     * @return object
     */
    public function getdatabytypereport(Request $request)
    {
        $data = DB::table('assets')
            ->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
            ->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
            ->leftjoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('location', 'assets.locationid', '=', 'location.id')
            ->select(['assets.*', 'supplier.name as supplier', 'brand.name as brand', 'brand.id as brandid', 'asset_type.name as type', 'location.name as location']);

        return Datatables::of($data)
            ->addColumn('pictures', function ($single) {
                return '<img src="' . url('/') . '/upload/assets/' . $single->picture . '" style="width:90px"/>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('assettype')) {

                    $query->where('assets.typeid', 'like', "%{$request->get('assettype')}%");
                }
            })
            ->rawColumns(['pictures'])
            ->make(true);
    }


    /**
     * get asset data by status from database
     * @return object
     */
    public function getdatabystatusreport(Request $request)
    {
        $data = DB::table('assets')
            ->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
            ->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
            ->leftjoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('location', 'assets.locationid', '=', 'location.id')
            ->select(['assets.*', 'supplier.name as supplier', 'brand.name as brand', 'brand.id as brandid', 'asset_type.name as type', 'location.name as location']);


        return Datatables::of($data)
            ->addColumn('pictures', function ($single) {
                return '<img src="' . url('/') . '/upload/assets/' . $single->picture . '" style="width:90px"/>';
            })
            ->addColumn('status2', function ($single) {
                //set status
                if ($single->status == '1') {
                    $status = trans('lang.readytodeploy');
                }
                if ($single->status == '2') {
                    $status = trans('lang.pending');
                }
                if ($single->status == '3') {
                    $status = trans('lang.archived');
                }
                if ($single->status == '4') {
                    $status = trans('lang.broken');
                }
                if ($single->status == '5') {
                    $status = trans('lang.lost');
                }
                if ($single->status == '6') {
                    $status = trans('lang.unserviceable');
                }

                return $status;
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('statustype')) {

                    $query->where('assets.status', 'like', "%{$request->get('statustype')}%");
                }
            })
            ->rawColumns(['pictures', 'status2'])
            ->make(true);
    }


    /**
     * get asset data by supplier from database
     * @return object
     */
    public function getdatabysupplierreport(Request $request)
    {
        $data = DB::table('assets')
            ->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
            ->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
            ->leftjoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('location', 'assets.locationid', '=', 'location.id')
            ->select(['assets.*', 'supplier.name as supplier', 'brand.name as brand', 'brand.id as brandid', 'asset_type.name as type', 'location.name as location']);

        return Datatables::of($data)
            ->addColumn('pictures', function ($single) {
                return '<img src="' . url('/') . '/upload/assets/' . $single->picture . '" style="width:90px"/>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('supplierid')) {

                    $query->where('assets.supplierid', 'like', "%{$request->get('supplierid')}%");
                }
            })
            ->rawColumns(['pictures'])
            ->make(true);
    }


    /**
     * get asset data by location from database
     * @return object
     */
    public function getdatabylocationreport(Request $request)
    {
        $data = DB::table('assets')
            ->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
            ->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
            ->leftjoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('location', 'assets.locationid', '=', 'location.id')
            ->select(['assets.*', 'supplier.name as supplier', 'brand.name as brand', 'brand.id as brandid', 'asset_type.name as type', 'location.name as location']);

        return Datatables::of($data)
            ->addColumn('pictures', function ($single) {
                return '<img src="' . url('/') . '/upload/assets/' . $single->picture . '" style="width:90px"/>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('locationid')) {

                    $query->where('assets.locationid', 'like', "%{$request->get('locationid')}%");
                }
            })
            ->rawColumns(['pictures'])
            ->make(true);
    }

  

}
