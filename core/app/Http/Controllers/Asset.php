<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AssetModel;
use Illuminate\Support\Facades\File;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use DB;
use App\User;
use App;
use Auth;
use Milon\Barcode\DNS2D;
use Carbon\Carbon;



require(app_path('fpdf\fpdf.php'));

class Asset extends Controller
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

    //return page
    public function index()
    {
        return view('asset.index');
    }


    /**
     * get  detail page
     * @return object
     */
    public function detail($id)
    {
        return view('asset.detail', compact('id'));
    }


    /**
     * get print label page
     * @return object
     */
    public function generatelabel($id)
    {
        return view('asset.generate')->with('id', $id);
    }


    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $assetDeleteFilter = DB::getSchemaBuilder()->hasColumn('assets', 'is_delete') ? 'AND assets.is_delete = 0' : '';

        $data = DB::select("
    SELECT
        assets.*,
        ah.depid as depid,
        supplier.name as supplier,
        brand.name as brand,
        asset_type.name as type,
        location.name as location,
        category.category as categoryname
    FROM assets
    LEFT JOIN supplier ON assets.supplierid = supplier.id
    LEFT JOIN brand ON assets.brandid = brand.id
    LEFT JOIN asset_type ON assets.typeid = asset_type.id
    LEFT JOIN location ON assets.locationid = location.id
    LEFT JOIN category ON assets.category = category.id
    LEFT JOIN (
        SELECT a1.*
        FROM asset_history a1
        INNER JOIN (
            SELECT assetid, MAX(id) as max_id
            FROM asset_history
            GROUP BY assetid
        ) a2 ON a1.assetid = a2.assetid AND a1.id = a2.max_id
    ) ah ON ah.assetid = assets.id
    WHERE assets.typeid != 7
    $assetDeleteFilter
    ORDER BY assets.created_at DESC
");
        return Datatables::of($data)
            ->addColumn('pictures', function ($single) {
                return '<img src="' . url('/') . '/upload/assets/' . $single->picture . '" style="width:90px"/>';
            })
            ->addColumn('action', function ($accountsingle) {
                //for checkout 2 button, checkin or checkout depand the record
                //$checkout = '  <a class="dropdown-item" href="#" id="btncheckout" customdata='.$accountsingle->id.'  data-toggle="modal" data-target="#checkout"><i class="fa fa-check"></i> '. trans('lang.checkout').'</a>';

                // if ($accountsingle->checkstatus === 2) {

                //     $checkout = '<a class="dropdown-item btnbb" href="#" id="btncheckin" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#checkin"><i class="fa fa-check"></i> ' . trans('lang.checkin') . '</a>';
                // } else {
                //     $checkout = '<a class="dropdown-item btnbb" href="#" id="btncheckout" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#checkout"><i class="fa fa-check"></i> ' . trans('lang.checkout') . '</a>';
                // }

                return '
                <div class="btn-group">
                <button class="btn btnconfirm btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                </button>
                <div class="dropdown-menu actionmenu">

                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="' . url('/') . '/assetlist/detail/' . $accountsingle->id . '"id="btndetail" customdata=' . $accountsingle->id . '  ><i class="fa fa-file-text"></i> ' . trans('lang.detail') . '</a>
                <a class="dropdown-item" href="#" id="btnedit" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i> ' . trans('lang.edit') . '</a>
                <a class="dropdown-item" href="#" id="btnedit" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i> ' . trans('lang.delete') . '</a>

                </div>
            </div>';
            })->rawColumns(['pictures', 'action'])
            ->make(true);
    }


    /**
     * get single data by assets id for history
     * @param integer $id
     * @return object
     */

    public function getGroupedAssets()
    {
        $hasAssetDeleteColumn = DB::getSchemaBuilder()->hasColumn('assets', 'is_delete');
        $assetAliasDeleteFilter = $hasAssetDeleteColumn ? 'AND a.is_delete = 0' : '';

        $data = DB::select("
        SELECT
            a.name,
            COUNT(*) as total,
            MIN(NULLIF(a.picture, '')) as picture,
            GROUP_CONCAT(a.assettag SEPARATOR ',') as all_tags
        FROM assets a
        WHERE a.typeid != 7
        $assetAliasDeleteFilter
        GROUP BY a.name
        ORDER BY a.name
    ");

        return Datatables::of($data)
            ->addColumn('pictures', function ($row) {
                $url = $row->picture ? url('/upload/assets/' . $row->picture) : url('/upload/assets/default.png');
                return '<img src="' . $url . '" style="width:90px"/>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-info btn-show" data-name="' . $row->name . '">View Items</button>';
            })
            ->addColumn('all_tags', function ($row) {
                return $row->all_tags; // 👈 important
            })
            ->rawColumns(['pictures', 'action'])
            ->make(true);
    }

    public function getAssetsByName($name)
    {
        $assetDeleteFilter = DB::getSchemaBuilder()->hasColumn('assets', 'is_delete') ? 'AND assets.is_delete = 0' : '';

        $data = DB::select("
        SELECT
            assets.*,
            brand.name as brand,
            asset_type.name as type,
            category.category as categoryname,
            location.name as location,
            ah.depid as depid,
            ah.status as hstatus,
            DATEDIFF(CURDATE(), assets.purchasedate) as number_of_days
        FROM assets
        LEFT JOIN brand ON assets.brandid = brand.id
        LEFT JOIN asset_type ON assets.typeid = asset_type.id
        LEFT JOIN category ON assets.category = category.id
        LEFT JOIN location ON assets.locationid = location.id
        LEFT JOIN (
            SELECT a1.*
            FROM asset_history a1
            INNER JOIN (
                SELECT assetid, MAX(id) as max_id
                FROM asset_history
                GROUP BY assetid
            ) a2 ON a1.assetid = a2.assetid AND a1.id = a2.max_id
        ) ah ON ah.assetid = assets.id
        WHERE assets.name = ?
        $assetDeleteFilter
    ", [$name]);

        foreach ($data as $row) {

            // // Add picture
            // $row->pictures = $row->picture
            //     ? '<img src="' . url('/') . '/upload/assets/' . $row->picture . '" style="width:60px"/>'
            //     : '-';

            // Add action dropdown
            $row->action = '
        <div class="btn-group">
            <button class="btn btnconfirm btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                <i class="fa fa-ellipsis-h"></i>
            </button>
            <div class="dropdown-menu actionmenu">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="' . url('/') . '/assetlist/detail/' . $row->id . '">
                    <i class="fa fa-file-text"></i> Detail
                </a>
                <a class="dropdown-item" href="#" customdata=' . $row->id . ' data-toggle="modal" data-target="#edit">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <a class="dropdown-item" href="#" customdata=' . $row->id . ' data-toggle="modal" data-target="#delete">
                    <i class="fa fa-trash"></i> Delete
                </a>
            </div>
        </div>';
        }

        return response()->json($data);
    }


    public function historyassetbyid(Request $request)
    {
        $id            = $request->input('assetid');

        $data = DB::select("select asset_history.*, assets.name as assetname,  IFNULL(employees.fullname, '-') as employeename, users.fullname, department.name as office

        from asset_history left join assets
        on asset_history.assetid = assets.id
        left join employees
        on asset_history.employeeid = employees.id left join users
        on users.id = asset_history.created_by left join department
        on employees.departmentid = department.id
        where asset_history.assetid = '$id'
        order by asset_history.created_at desc");
        return Datatables::of($data)

            ->addColumn('status', function ($single) {
                $status = trans('lang.undefined');
                if ($single->status == '1') {
                    $status = trans('lang.checkout');
                }
                if ($single->status == '2') {
                    $status = trans('lang.checkin');
                }
                if ($single->status == '3') {
                    $status = trans('lang.serviceable');
                }
                if ($single->status == '4') {
                    $status = trans('lang.unserviceable');
                }
                return $status;
            })

            ->addColumn('date', function ($single) {

                $setting = DB::table('settings')->where('id', '1')->first();
                return date($setting->formatdate, strtotime($single->date));
            })
            ->rawColumns(['status', 'date'])
            ->make(true);
    }

    public function printassethistoryreport(Request $request, $id)
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $search = trim((string) $request->get('search', ''));

        $query = DB::table('asset_history')
            ->leftJoin('assets', 'asset_history.assetid', '=', 'assets.id')
            ->leftJoin('employees', 'asset_history.employeeid', '=', 'employees.id')
            ->leftJoin('users', 'users.id', '=', 'asset_history.created_by')
            ->leftJoin('department', 'employees.departmentid', '=', 'department.id')
            ->select(
                'asset_history.*',
                'assets.name as assetname',
                'assets.assettag',
                DB::raw("IFNULL(employees.fullname, '-') as employeename"),
                'users.fullname',
                'department.name as office'
            )
            ->where('asset_history.assetid', $id);

        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($subQuery) use ($like) {
                $subQuery->where('assets.name', 'like', $like)
                    ->orWhere('assets.assettag', 'like', $like)
                    ->orWhere('employees.fullname', 'like', $like)
                    ->orWhere('users.fullname', 'like', $like)
                    ->orWhere('department.name', 'like', $like)
                    ->orWhere('asset_history.remarks', 'like', $like)
                    ->orWhere('asset_history.created_at', 'like', $like)
                    ->orWhere('asset_history.date', 'like', $like)
                    ->orWhere('asset_history.status', 'like', $like);
            });
        }

        $rows = $query->orderBy('asset_history.created_at', 'desc')->get();
        $first = $rows->first();

        if (!$first) {
            abort(404);
        }

        $pdf = new \FPDF('L', 'mm', 'LETTER');
        $pdf->SetAutoPageBreak(false);

        $statusText = function ($status) {
            if ((string) $status === '1') {
                return trans('lang.checkout');
            }
            if ((string) $status === '2') {
                return trans('lang.checkin');
            }
            if ((string) $status === '3') {
                return trans('lang.serviceable');
            }
            if ((string) $status === '4') {
                return trans('lang.unserviceable');
            }
            return trans('lang.undefined');
        };

        $fit = function ($value, $width) use ($pdf) {
            $value = trim((string) $value);
            if ($value === '') {
                return '-';
            }

            $maxWidth = max(1, $width - 1.5);
            if ($pdf->GetStringWidth($value) <= $maxWidth) {
                return $value;
            }

            while (strlen($value) > 0 && $pdf->GetStringWidth($value . '...') > $maxWidth) {
                $value = substr($value, 0, -1);
            }

            return trim($value) . '...';
        };

        $drawHeader = function ($withLetterhead = true) use ($pdf, $first) {
            $pdf->AddPage();
            $this->drawOfficialFooter($pdf);

            if (!$withLetterhead) {
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(5, 12);
                $pdf->SetFillColor(37, 150, 190);
                $pdf->Cell(10, 7, 'No.', 1, 0, 'C', true);
                $pdf->Cell(28, 7, 'Action', 1, 0, 'C', true);
                $pdf->Cell(36, 7, 'Date & Time', 1, 0, 'C', true);
                $pdf->Cell(42, 7, 'Employee', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'Office', 1, 0, 'C', true);
                $pdf->Cell(38, 7, 'Created By', 1, 0, 'C', true);
                $pdf->Cell(88, 7, 'Remarks', 1, 1, 'C', true);
                return 19;
            }

            $pageWidth = $pdf->GetPageWidth();
            $muntiLogo = app_path('fpdf/muntilogo.png');
            $ddrmLogo = app_path('fpdf/drlogo.png');

            if (file_exists($muntiLogo)) {
                $pdf->Image($muntiLogo, 12, 5, 22, 22);
            }
            if (file_exists($ddrmLogo)) {
                $pdf->Image($ddrmLogo, $pageWidth - 34, 5, 20, 20);
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
            $pdf->Line(5, 43, $pageWidth - 5, 43);
            $pdf->Line(5, 45, $pageWidth - 5, 45);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY($pageWidth - 65, 48);
            $pdf->Cell(14, 5, 'DATE:', 0, 0, 'R');
            $pdf->Cell(30, 5, date('Y-m-d'), 'B', 0, 'C');

            $pdf->SetFont('Arial', 'B', 14);
            $pdf->SetXY(0, 56);
            $pdf->Cell($pageWidth, 6, 'ASSET HISTORY REPORT', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(0, 63);
            $pdf->Cell($pageWidth, 5, strtoupper(($first->assetname ?: 'ASSET') . ' (' . ($first->assettag ?: '-') . ')'), 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(5, 72);
            $pdf->SetFillColor(37, 150, 190);
            $pdf->Cell(10, 7, 'No.', 1, 0, 'C', true);
            $pdf->Cell(28, 7, 'Action', 1, 0, 'C', true);
            $pdf->Cell(36, 7, 'Date & Time', 1, 0, 'C', true);
            $pdf->Cell(42, 7, 'Employee', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'Office', 1, 0, 'C', true);
            $pdf->Cell(38, 7, 'Created By', 1, 0, 'C', true);
            $pdf->Cell(88, 7, 'Remarks', 1, 1, 'C', true);
            return 78;
        };

        $y = $drawHeader(true);
        $rowHeight = 8;

        foreach ($rows as $index => $row) {
            if ($y + $rowHeight > 190) {
                $y = $drawHeader(false);
            }

            $createdAt = !empty($row->created_at) ? date('M d, Y h:i:s A', strtotime($row->created_at)) : '-';
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY(5, $y);
            $pdf->Cell(10, $rowHeight, ($index + 1) . '.', 1, 0, 'C');
            $pdf->Cell(28, $rowHeight, $fit($statusText($row->status), 28), 1, 0, 'C');
            $pdf->Cell(36, $rowHeight, $fit($createdAt, 36), 1, 0, 'C');
            $pdf->Cell(42, $rowHeight, $fit($row->employeename, 42), 1, 0, 'L');
            $pdf->Cell(30, $rowHeight, $fit($row->office, 30), 1, 0, 'L');
            $pdf->Cell(38, $rowHeight, $fit($row->fullname, 38), 1, 0, 'L');
            $pdf->Cell(88, $rowHeight, $fit($row->remarks, 88), 1, 1, 'L');
            $y += $rowHeight;
        }

        if ($rows->isEmpty()) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(8, $y);
            $pdf->Cell(272, 8, 'No records found.', 1, 1, 'C');
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="asset_history_report_' . $id . '.pdf"');
    }

    /**
     * get single data
     * @param integer $id
     * @return object
     */

    public function byid(Request $request)
    {
        $id            = $request->input('id');

        $data = DB::table('assets')->select('assets.*', 'assets.name as assetname', 'assets.created_at as assetcreated_at', 'assets.updated_at as assetupdated_at', 'assets.description as description', 'brand.*', 'brand.name as brand', 'asset_type.name as type', 'supplier.name as supplier', 'location.name as location', 'category.category as categoryname')
            ->leftJoin('brand', 'brand.id', '=', 'assets.brandid')
            ->leftJoin('asset_type', 'asset_type.id', '=', 'assets.typeid')
            ->leftJoin('supplier', 'supplier.id', '=', 'assets.supplierid')
            ->leftJoin('location', 'location.id', '=', 'assets.locationid')
            ->leftJoin('asset_history', 'asset_history.assetid', '=', 'assets.id')
            ->leftJoin('category', 'assets.category', '=', 'category.id')
            ->where('assets.id', $id)
            ->first();

        if ($data) {
            $data->last_control_number = DB::table('asset_history')
                ->where('assetid', $id)
                ->where('status', '1')
                ->orderBy('created_at', 'desc')
                ->value('control_number');

            //set status
            if ($data->status == '1') {
                $status = trans('lang.readytodeploy');
            }
            if ($data->status == '2') {
                $status = trans('lang.pending');
            }
            if ($data->status == '3') {
                $status = trans('lang.archived');
            }
            if ($data->status == '4') {
                $status = trans('lang.broken');
            }
            if ($data->status == '5') {
                $status = trans('lang.lost');
            }
            if ($data->status == '6') {
                $status = trans('lang.outofrepair');
            }

            // set history status
            // if($data->hstatus == "1") {
            //     $hstatus = trans('lang.checkin');
            // } else {
            //     $hstatus = trans('lang.checkout');
            // }

            //get date format setting
            $setting = DB::table('settings')->where('id', '1')->first();


            //for warranty
            $nextexpired = '';
            if ($data->purchasedate && $data->warranty) {
                $prchasedate = strtotime($data->purchasedate);
                $nextexpired = date($setting->formatdate, strtotime("+" . $data->warranty . " month", $prchasedate));
            }
            // $prchasedate = strtotime($data->purchasedate);
            // $nextexpired = date($setting->formatdate, strtotime($data->warranty . ' month', $prchasedate));

            $res['success'] = 'success';
            $res['message'] = $data;
            $res['assetcreated_at'] = date($setting->formatdate, strtotime($data->assetcreated_at));
            $res['assetupdated_at'] = date($setting->formatdate, strtotime($data->updated_at));
            $res['assetpurchasedate'] = date($setting->formatdate, strtotime($data->purchasedate));
            $res['assetcost'] = $setting->currency . $data->cost;
            $res['assetwarranty'] = $data->warranty . ' ' . trans('lang.month') . ' - (' . $nextexpired . ')';
            // $res['assetstatus'] = $status;
            $status = $status ?? 'unknown';
            $res['assetbarcode'] = '<img src="data:image/png;base64,' . DNS2D::getBarcodePNG($data->assettag, 'QRCODE') . '" alt="barcode" width="70"  />';

            $res['assetimage']  = url('/') . '/upload/assets/' . $data->picture;
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }

    public function byassettag(Request $request)
    {
        $id = $request->input('searchValue');
        $receivedby = $request->input('receivedby');

        $data = DB::table('assets')->select('assets.*', 'assets.id as assetid', 'assets.name as assetname', 'assets.description as assetdescription', 'assets.created_at as assetcreated_at', 'assets.updated_at as assetupdated_at', 'assets.description as description', 'brand.*', 'brand.name as brand', 'asset_type.name as type', 'supplier.name as supplier', 'location.name as location', 'asset_history.employeeid as ahemployeeid', 'employees.fullname as efullname', 'category.category as categoryname')
            ->leftJoin('brand', 'brand.id', '=', 'assets.brandid')
            ->leftJoin('asset_type', 'asset_type.id', '=', 'assets.typeid')
            ->leftJoin('supplier', 'supplier.id', '=', 'assets.supplierid')
            ->leftJoin('location', 'location.id', '=', 'assets.locationid')
            ->leftJoin('asset_history', 'asset_history.assetid', '=', 'assets.id')
            ->leftJoin('employees', 'asset_history.employeeid', '=', 'employees.id')
            ->leftJoin('category', 'assets.category', '=', 'category.id')
            ->where('assets.assettag', $id)
            ->first();

        if ($data) {
            $data->last_control_number = DB::table('asset_history')
                ->where('assetid', $data->assetid)
                ->where('status', '1')
                ->orderBy('created_at', 'desc')
                ->value('control_number');

            //set status
            if ($data->status == '1') {
                // $status = trans('lang.readytodeploy');
                $setting = DB::table('settings')->where('id', '1')->first();

                //for warranty
                $prchasedate = strtotime($data->purchasedate);
                $nextexpired = date($setting->formatdate, strtotime($data->warranty . ' month', $prchasedate));

                $res['success'] = 'success';
                $res['message'] = $data;
                $res['assetcreated_at'] = date($setting->formatdate, strtotime($data->assetcreated_at));
                $res['assetupdated_at'] = date($setting->formatdate, strtotime($data->updated_at));
                $res['assetpurchasedate'] = date($setting->formatdate, strtotime($data->purchasedate));
                $res['assetcost'] = $setting->currency . $data->cost;
                $res['assetwarranty'] = $data->warranty . ' ' . trans('lang.month') . ' - (' . $nextexpired . ')';
                // $res['assetstatus'] = $status;
                $res['assetbarcode'] = '<img src="data:image/png;base64,' . DNS2D::getBarcodePNG($data->assettag, 'QRCODE') . '" alt="barcode" width="70"  />';

                $res['assetimage']  = url('/') . '/upload/assets/' . $data->picture;
            } else {
                $res['success'] = 'failed';
                $res['assetstatus'] = $data->status;
            }
            // set history status
            // if($data->hstatus == "1") {
            //     $hstatus = trans('lang.checkin');
            // } else {
            //     $hstatus = trans('lang.checkout');
            // }
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }


    /**
     * get single data where is not id
     * @return object
     */

    public function isnotbyid()
    {

        $data = DB::table("assets")->select('*')->whereNotIn('id', function ($query) {
            $query->select('assetid')->from('depreciation')->whereNotNull('assetid');
        })->get();

        if ($data) {
            $res['success'] = 'success';
            $res['message'] = $data;
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }


    /**
     * insert data  to database
     *
     * @param integer  $supplierid
     * @param integer  $typeid
     * @param integer  $brandid
     * @param string  $assettag
     * @param string  $name
     * @param string  $serial
     * @param string  $quantity
     * @param string  $purchasedate
     * @param string  $cost
     * @param string  $warranty
     * @param string  $status
     * @param string  $picture
     * @param string  $description
     * @return object
     */
    public function save(Request $request)
    {
        $supplierid         = $request->input('supplierid');
        $locationid         = $request->input('locationid');
        $typeid             = $request->input('typeid');
        $brandid             = $request->input('brandid');
        $assettag           = $request->input('assettag');
        $name               = $request->input('name');
        $unit             = $request->input('unit');
        $category             = $request->input('category');
        $quantity           = 1;
        $purchasedate       = $request->input('purchasedate');
        $cost               = $request->input('cost');
        $warranty           = $request->input('warranty');
        $status             = $request->input('status');
        $checkstatus        = 0;
        $picture            = $request->file('picture');
        $description        = $request->input('description');
        $defaultimage       = 'pic.png';
        $created_at         = date("Y-m-d H:i:s");
        $updated_at         = date("Y-m-d H:i:s");
        $message = ['picture.mimes' => trans('lang.upload_error')];

        $emailcheck = DB::table('assets')
            ->where('assettag', '=', $assettag)
            ->where('is_delete', 0)
            ->first();

        if ($emailcheck) {
            $res['message'] = 'exist';
        } else {

            if ($request->hasFile('picture')) {
                $this->validate($request, ['picture' => 'mimes:jpeg,png,jpg|max:2048'], $message);
                $picturename  = date('mdYHis') . uniqid() . $request->file('picture')->getClientOriginalName();
                $request->file('picture')->move(public_path("/upload/assets"), $picturename);
                $data       = array(
                    'name' => $name,
                    'locationid' => $locationid,
                    'supplierid' => $supplierid,
                    'brandid' => $brandid,
                    'typeid' => $typeid,
                    'assettag' => $assettag,
                    'unit' => $unit,
                    'category' => $category,
                    'quantity' => $quantity,
                    'purchasedate' => $purchasedate,
                    'checkstatus' => 0,
                    'cost' => $cost,
                    'warranty' => $warranty,
                    'status' => $status,
                    'picture' => $picturename,
                    'description' => $description,
                    'is_delete' => 0,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                );
                $insert     = DB::table('assets')->insert($data);
            } else {
                $data       = array(
                    'name' => $name,
                    'locationid' => $locationid,
                    'supplierid' => $supplierid,
                    'typeid' => $typeid,
                    'brandid' => $brandid,
                    'assettag' => $assettag,
                    'unit' => $unit,
                    'category' => $category,
                    'quantity' => $quantity,
                    'purchasedate' => $purchasedate,
                    'cost' => $cost,
                    'checkstatus' => 0,
                    'warranty' => $warranty,
                    'status' => $status,
                    'picture' => $defaultimage,
                    'description' => $description,
                    'is_delete' => 0,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                );

                $insert     = DB::table('assets')->insert($data);
            }

            if ($insert) {
                $res['message'] = 'success';
                $this->auditTrail('Asset', 'Create', 'Created asset: '.$name.' ('.$assettag.').');
            } else {
                $res['message'] = 'failed';
            }
        }
        return response($res);
    }

    /**
     * update data  to database
     *
     * @param string  $fullname
     * @param string  $email
     * @param string  $picture
     * @param string  $gender
     * @param string  $city
     * @param string  $country
     * @param string  $phone
     * @return object
     */
    public function update(Request $request)
    {
        $id             = $request->input('id');
        $locationid     = $request->input('locationid');
        $supplierid     = $request->input('supplierid');
        $typeid         = $request->input('typeid');
        $brandid        = $request->input('brandid');
        $assettag       = $request->input('assettag');
        $name           = $request->input('name');
        $unit         = $request->input('unit');
        $category             = $request->input('category');

        $quantity       = 1;
        $purchasedate   = $request->input('purchasedate');
        $cost           = $request->input('cost');
        $warranty       = $request->input('warranty');
        $status         = $request->input('status');
        $picture        = $request->file('picture');
        $description    = $request->input('description');
        $created_at     = date("Y-m-d H:i:s");
        $oldAsset       = DB::table('assets')->where('id', $id)->first();
        $updated_at     = date("Y-m-d H:i:s");
        $message = ['picture.mimes' => trans('lang.upload_error')];

        $tagcheck = DB::table('assets')
            ->where('assettag', '=', $assettag)
            ->where('id', '!=', $id)
            ->where('is_delete', 0)
            ->first();

        if ($tagcheck) {
            $res['message'] = 'exist';
        } else {

            if ($request->hasFile('picture')) {
                $this->validate($request, ['picture' => 'mimes:jpeg,png,jpg|max:2048'], $message);
                $picturename  = date('mdYHis') . uniqid() . $request->file('picture')->getClientOriginalName();
                $request->file('picture')->move(public_path("/upload/assets"), $picturename);

                $update = DB::table('assets')->where('id', $id)->where('is_delete', 0)
                    ->update(
                        [
                            'name'                => $name,
                            'locationid'          => $locationid,
                            'supplierid'          => $supplierid,
                            'brandid'             => $brandid,
                            'typeid'              => $typeid,
                            'assettag'            => $assettag,
                            'unit'              => $unit,
                            'category'              => $category,

                            'quantity'            => $quantity,
                            'purchasedate'        => $purchasedate,
                            'cost'                => $cost,
                            'warranty'            => $warranty,
                            'status'              => $status,
                            'description'         => $description,
                            'picture'             => $picturename,
                            'updated_at'          => $updated_at
                        ]
                    );
            } else {
                $update = DB::table('assets')->where('id', $id)->where('is_delete', 0)
                    ->update(
                        [
                            'name'                => $name,
                            'locationid'          => $locationid,
                            'supplierid'          => $supplierid,
                            'brandid'             => $brandid,
                            'typeid'              => $typeid,
                            'assettag'            => $assettag,
                            'unit'              => $unit,
                            'category'              => $category,

                            'quantity'            => $quantity,
                            'purchasedate'        => $purchasedate,
                            'cost'                => $cost,
                            'warranty'            => $warranty,
                            'status'              => $status,
                            'description'         => $description,
                            'updated_at'          => $updated_at
                        ]
                    );
            }

            // If status is 7, save to maintenance
            if ($status == 7) {
                $maintenanceData = [
                    'assetid'        => $id,
                    'type'           => $request->input('maintenance_type', 'repair'), // default or from request
                    'reason_remarks' => $request->input('maintenance_reason', 'Auto maintenance entry from asset update'),
                    'startdate'      => $request->input('maintenance_startdate', date('Y-m-d')),
                    'enddate'        => $request->input('maintenance_enddate', null),
                    'created_by'     => Auth::id(),
                    'created_at'     => $created_at,
                    'updated_at'     => $updated_at
                ];
                DB::table('maintenance')->insert($maintenanceData);
            }

            if ($update) {
                $res['message'] = 'success';
                $labelMap = [
                    'name' => 'Name',
                    'assettag' => 'Asset Tag',
                    'locationid' => 'Location ID',
                    'supplierid' => 'Supplier ID',
                    'brandid' => 'Brand ID',
                    'typeid' => 'Type ID',
                    'unit' => 'Unit',
                    'quantity' => 'Quantity',
                    'purchasedate' => 'Purchase Date',
                    'cost' => 'Cost',
                    'warranty' => 'Warranty',
                    'description' => 'Description'
                ];
                $newData = [
                    'name' => $name,
                    'assettag' => $assettag,
                    'locationid' => $locationid,
                    'supplierid' => $supplierid,
                    'brandid' => $brandid,
                    'typeid' => $typeid,
                    'unit' => $unit,
                    'quantity' => $quantity,
                    'purchasedate' => $purchasedate,
                    'cost' => $cost,
                    'warranty' => $warranty,
                    'description' => $description
                ];
                $diff = $this->auditCalculateDiff($oldAsset, $newData, $labelMap);
                $detailsText = 'Updated asset: '.$name.' ('.$assettag.')' . ($diff['details'] ? ":\n" . $diff['details'] : '');
                $this->auditTrail('Asset', 'Update', $detailsText, 'Asset', $id, $diff['old'], $diff['new']);
            } else {
                $res['message'] = 'failed';
            }
        }
        return response($res);
    }

    /**
     * insert checkout data  to database
     *
     * @param integer  $assetid
     * @param integer  $employeeid
     * @param string  $date
     * @param integer  $status
     * @return object
     */
    public function savecheckout(Request $request)
    {
        $assetid        = $request->input('assetid');
        $employeeid     = $request->input('employeeid');
        $date           = $request->input('checkoutdate');
        $status         = '1'; //checkout = 1
        $checkstatus    = '2';
        // $receiverby           = $request->input('receivedby');
        $receiverby     = Auth::id();
        $controlno      = trim($request->input('controlno23'));
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $remarks        = $request->input('remarks1');

        if (!$controlno) {
            return response(['success' => 'failed', 'message' => 'Control number is required.']);
        }

        $alreadyUsed = DB::table('asset_history')
            ->where('control_number', $controlno)
            ->exists();

        if ($alreadyUsed) {
            return response(['success' => 'failed', 'message' => 'Control number already exists. Please generate a new one.']);
        }

        $data = array('assetid' => $assetid, 'status' => $status, 'employeeid' => $employeeid, 'date' => $date, 'created_by' => $receiverby, 'control_number' => $controlno, 'created_at' => $created_at, 'updated_at' => $updated_at, 'remarks' => $remarks);
        $insertId = DB::table('asset_history')->insertGetId($data);

        if ($insertId) {

            //set status in table asset
            $update = DB::table('assets')->where('id', $assetid)
                ->update(
                    [
                        'checkstatus'         => $checkstatus,
                        'updated_at'          => $updated_at,

                    ]
                );

            $res['success'] = 'success';
            $res['id'] = $insertId;
            $res['print_url'] = url('assetprintform/' . $insertId);
            $asset = DB::table('assets')->where('id', $assetid)->first();
            $this->auditTrail('Asset', 'Borrowed', 'Borrowed asset: '.($asset ? $asset->name.' ('.$asset->assettag.')' : 'asset ID '.$assetid).'.');
        } else {
            $res['success'] = 'failed';
        }

        return response($res);
    }

    public function savescan(Request $request)
    {
        $assetid        = $request->input('assetnumber');
        // var_dump( $assetid );
        $employeeid     = $request->input('checkoutemployeeid1');
        $date           = $request->input('checkindate');
        $typeofid           = $request->input('typeofid');
        // $idno           = $request->input('idno');
        $depid           = $request->input('depid');
        $core           = $request->input('core');

        $currentCheckStatus = DB::table('assets')->where('id', $assetid)->value('checkstatus');
        // If the current checkstatus is '2', change it to '1'
        if ($currentCheckStatus == '2') {
            $checkstatus = '0'; // checkout status
            $status = '2';
        } else {
            $checkstatus = '2';
            $status = '1';
        }
        // $checkstatus    = '2';
        // $receiverby           = $request->input('receivedby');
        $receiverby     = Auth::id();
        $controlno = $request->input('controlno');
        if ($currentCheckStatus == '2') {
            $controlno = DB::table('asset_history')
                ->where('assetid', $assetid)
                ->where('status', '1')
                ->orderBy('created_at', 'desc')
                ->value('control_number');
        }
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $remarks        = $request->input('remarks');
        $data           = array('assetid' => $assetid, 'status' => $status, 'employeeid' => $employeeid, 'date' => $date, 'typeofid' => $typeofid, 'depid' => $depid, 'condition' => $core, 'created_by' => $receiverby, 'control_number' => $controlno, 'created_at' => $created_at, 'updated_at' => $updated_at, 'remarks' => $remarks);
        // $insert         = DB::table('asset_history')->insert($data);
        $insertId = DB::table('asset_history')->insertGetId($data);
        // dd($insertId);



        if ($insertId) {

            //set status in table asset
            $update = DB::table('assets')->where('id', $assetid)
                ->update(
                    [
                        'checkstatus'         => $checkstatus,
                        'updated_at'          => $updated_at,
                        'condition'           => $core,
                    ]
                );

            $res['success'] = 'success';
            $asset = DB::table('assets')->where('id', $assetid)->first();
            $this->auditTrail('Asset', 'Scan', 'Scanned asset: '.($asset ? $asset->name.' ('.$asset->assettag.')' : 'asset ID '.$assetid).'.');
        } else {
            $res['success'] = 'failed';
        }

        // return response($res);
        return response()->json(['success' => true, 'id' => $insertId]);
    }

    // public function savescanbatch(Request $request)
    public function savescanbatch(Request $request)
    {
        $assets = $request->input('assets');

        if (!is_array($assets) || empty($assets)) {
            return response()->json([
                'success' => false,
                'message' => 'No assets data received or data is not an array.',
                'received' => $assets
            ], 400);
        }

        $firstAsset = reset($assets);
        $batchControlNo = '';
        $date = $firstAsset['checkindate'] ?? null;
        // $typeofid = $firstAsset['typeofid'] ?? null;
        // $idno = $firstAsset['idno'] ?? null;
        $depid = $firstAsset['depid'] ?? null;
        $condition = $firstAsset['condition'] ?? null;
        $used = $firstAsset['used'] ?? null;
        $remarks = $firstAsset['remarks'] ?? null;
        $employeeid = $firstAsset['employeeid'] ?? null;

        $hasCheckoutItem = false;
        foreach ($assets as $asset) {
            if (!isset($asset['assetid'])) {
                continue;
            }
            $currentCheckStatus = DB::table('assets')->where('id', $asset['assetid'])->value('checkstatus');
            if ($currentCheckStatus != '2') {
                $hasCheckoutItem = true;
                $batchControlNo = trim($asset['controlno'] ?? '');
                break;
            }
        }

        if ($hasCheckoutItem && !$batchControlNo) {
            return response()->json(['success' => false, 'message' => 'Control number is required for checkout items.'], 400);
        }

        if ($hasCheckoutItem && DB::table('asset_history')->where('control_number', $batchControlNo)->exists()) {
            return response()->json(['success' => false, 'message' => 'Control number already exists. Please generate a new number for this borrower form.'], 400);
        }

        $receiverby = Auth::id();
        $timestamp = now();
        $successSaves = 0;
        $scannedNames = [];

        $lastGroupId = DB::table('asset_history')->max('groupid');
        $groupid = $lastGroupId ? $lastGroupId + 1 : 1;

        foreach ($assets as $asset) {
            if (!isset($asset['assetid'])) {
                continue; // skip if no assetid
            }



            $currentCheckStatus = DB::table('assets')->where('id', $asset['assetid'])->value('checkstatus');

            $checkstatus = ($currentCheckStatus == '2') ? '0' : '2';
            $status = ($currentCheckStatus == '2') ? '2' : '1';

            if ($currentCheckStatus == '2') {
                $controlNumber = DB::table('asset_history')
                    ->where('assetid', $asset['assetid'])
                    ->where('status', '1')
                    ->orderBy('created_at', 'desc')
                    ->value('control_number');
            } else {
                $controlNumber = trim($asset['controlno'] ?? '') ?: $batchControlNo;
            }

            $data = [
                'assetid'        => $asset['assetid'],
                'status'         => $status,
                'employeeid'     => $employeeid,
                'date'           => $date,
                // 'typeofid'       => $typeofid,
                // 'idno'           => $idno,
                'depid'          => $depid,
                'condition'      => $condition,
                'used'           => $used,
                'created_by'     => $receiverby,
                'control_number' => $controlNumber,
                'created_at'     => $timestamp,
                'updated_at'     => $timestamp,
                'remarks'        => $remarks,
                'groupid'        => $groupid

            ];

            $insertId = DB::table('asset_history')->insertGetId($data);

            if ($insertId) {
                DB::table('assets')->where('id', $asset['assetid'])->update([
                    'checkstatus' => $checkstatus,
                    'updated_at'  => $timestamp,
                    'condition'   => $asset['condition'] ?? null
                ]);
                $assetRecord = DB::table('assets')->where('id', $asset['assetid'])->first();
                if ($assetRecord) {
                    $scannedNames[] = $assetRecord->name . ' (' . $assetRecord->assettag . ')';
                } else {
                    $scannedNames[] = 'asset ID ' . $asset['assetid'];
                }
                $successSaves++;
            }
        }

        if ($successSaves > 0) {
            $this->auditTrail('Asset', 'Scan', 'Scanned assets: ' . implode(', ', $scannedNames) . '.');
        }

        // return response()->json([
        //     'success' => true,
        //     'saved' => $successSaves,
        //     'total' => count($assets)
        // ]);
        return response()->json(['success' => true, 'id' => $groupid]);
    }




    public function assetprintform(Request $request)
    {
        $userName = Auth::user()->fullname;


        $id = $request->route('id') ?: $request->input('id');
        $printByHistoryId = (bool) $request->route('id');
        // dd($id);
        $historyQuery = DB::table('asset_history')
            ->leftJoin('assets', 'asset_history.assetid', '=', 'assets.id')
            ->leftJoin('employees', 'asset_history.employeeid', '=', 'employees.id')
            ->leftJoin('users', 'users.id', '=', 'asset_history.created_by')
            ->leftJoin('department as representing_department', 'asset_history.depid', '=', 'representing_department.id')
            ->leftJoin('department as employee_department', 'employees.departmentid', '=', 'employee_department.id')
            ->select(
                'asset_history.*',
                'assets.name as assetname',
                'assets.cost as cost',
                'assets.assettag as assettag',
                DB::raw("IFNULL(employees.fullname, '-') as employeename"),
                'users.fullname',
                DB::raw("COALESCE(representing_department.name, employee_department.name) as office"),
                'employees.mobile_number as contact_no'
            );

        if ($printByHistoryId) {
            $historyQuery->where('asset_history.id', $id);
        } else {
            $historyQuery->where('asset_history.groupid', $id);
        }

        $data = $historyQuery->orderBy('asset_history.created_at', 'desc')->get();
        // var_dump($data);
        $first = $data->first();

        if (!$first) {
            abort(404);
        }

        $pdf = new \FPDF('P', 'mm', 'LETTER');
        // $pdf->setTitle($data->id);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(FALSE);
        $this->drawOfficialFooter($pdf);

        // $pdf->AddFont('Arial', '', 'Arial-Regular.php');
        // $pdf->AddFont('Arial', 'B', 'Arial-Bold.php');
        // $pdf->AddFont('Arial', 'BI', 'Arial-Bold-Italic.php');
        // $pdf->AddFont('Arial', 'I', 'Arial-Italic.php');

        $pdf->SetFont('Arial', '', 10);



        //Header
        $pdf->SetXY(0, 7);
        $pdf->cell(216, 4, 'Republic ofthe Philippines', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetX(0);
        $pdf->cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(0);
        $pdf->cell(216, 4, 'City of Muntinlupa', 0, 0, 'C');

        $pdf->Image(public_path('muntilogo.png'), 17, 5, 25, 25);
        $pdf->Image(public_path('drlogo.png'), 175, 5, 24, 24);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(0, 25);
        $pdf->cell(216, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetX(0);
        $pdf->cell(216, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');

        $pdf->SetXY(0, 35);
        $pdf->cell(216, 4, 'Hall of Justice Compound, Resilince Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');

        $pdf->SetX(0);
        $pdf->cell(216, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

        //LINE
        $pdf->SetXY(12, 43.5);
        $pdf->SetFillColor(33, 19, 13);
        $pdf->cell(192, 0.5, '', 1, 1, 'C', true);

        $pdf->SetXY(12, 45);
        $pdf->cell(192, 0.2, '', 1, 1, 'C', true);


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(20, 50);
        $pdf->cell(12, 5, 'DATE:', 0, 0, 'L');
        $pdf->cell(20, 4, ($first->date), 'B', 0, 'C');

        $pdf->cell(110, 4, '', 0, 0, 'C'); //SPACING LANG

        $pdf->cell(20, 4, 'CGM-OP-MCDRRM-01F1', 0, 0, 'C');

        //disregard
        $pdf->SetXY(20, 55);
        $pdf->cell(12, 5, '', 0, 0, 'L'); //spacing
        $pdf->cell(20, 4, '', 0, 0, 'C'); //spacing
        $pdf->cell(101, 4, '', 0, 0, 'C'); //SPACING LANG

        $pdf->cell(22, 4, 'Control No.', 0, 0, 'L');
        $pdf->cell(20, 3, utf8_decode($first->control_number), 'B', 0, 'C'); //Control No.

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(0, 65);

        if ($first->status == 1) {
            $title = "MATERIALS & EQUIPMENT BORROWER'S FORM";
        } else {
            $title = "MATERIALS & EQUIPMENT RETURN FORM";
        }

        $pdf->Cell(216, 4, $title, 0, 1, 'C');
        // $pdf->cell(216, 4, 'MATERIALS & EQUIPMENT BORROWER\'S FORM', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(20, 75);
        $pdf->cell(12, 5, 'Name:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->cell(100, 4, utf8_decode($first->employeename), 'B', 1, 'L');

        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(23, 5.5, 'Department:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->cell(85, 4, utf8_decode($first->office), 'B', 1, 'L');

        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->cell(30, 5.5, 'Contact Number:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->cell(78.5, 4, utf8_decode($first->contact_no), 'B', 1, 'L');

        //         $text = "I, _____________________________________, hereby claim total responsibility for the proper use and deployment of the
        // equipment and also it must be kept in good condition and must be kept clean at all times. I understand that if this piece
        // of equipment is lost, stolen, damaged etc. I am responsible for its replacement or repair and also I must submit an incident
        // report outlining what occured during the incident.";

        //         $pdf->SetFont('Arial', 'B', 10);
        //         $pdf->SetXY(20, 93);
        //         $pdf->MultiCell(192, 4, $text, 0, 'J');

        //         //Name
        //         $pdf->SetXY(24, 92.5);
        //         $pdf->cell(65, 4, utf8_decode($first->employeename), 0, 1, 'C');

        //         $text = "The City Government of Muntinlupa, specially the DDRM, is not responsible for any legal violation may I committed during
        // the time of borrowing involving or using the described equipment.";

        //         $pdf->SetFont('Arial', 'B', 10);
        //         $pdf->SetXY(20, 115);
        //         $pdf->MultiCell(186, 4, $text, 0, 'J');


        $pdf->SetXY(0, 95);
        $pdf->Cell(216, 4, 'Material/Equipment Requested', 0, 1, 'C');

        $pdf->SetXY(3, 105);
        $pdf->SetFillColor(164, 172, 124);

        // Header row
        $pdf->Cell(8, 5, 'NO.', 1, 0, 'C', true);
        $pdf->Cell(50, 5, 'ITEM(S) DESCRIPTION', 1, 0, 'C', true);
        $pdf->Cell(28, 5, 'SERIAL NO.', 1, 0, 'C', true);
        $pdf->Cell(15, 5, 'QTY.', 1, 0, 'C', true);
        $pdf->Cell(25, 5, 'COST', 1, 0, 'C', true);
        $pdf->Cell(42, 5, 'PURPOSE', 1, 0, 'C', true);
        $pdf->Cell(42, 5, 'REMARKS', 1, 1, 'C', true);

        // =========================
// TABLE + PAGINATION (Fixed 5 rows, max 10 on page 1)
// =========================
$tableX = 3;
$firstPageStartY = 105;     // header row Y (same as yours)
$nextPageStartY  = 40;      // adjust if page 2 has header images; 40 is common
$headerH = 5;
$rowH = 6;

$fixedRowsP1 = 5;
$maxRowsP1   = 10;

// Column widths (same as yours)
$wNo=8; $wDesc=50; $wSerial=28; $wQty=15; $wCost=25; $wPurpose=42; $wRemarks=42;

$drawHeader = function() use ($pdf,$tableX,$headerH,$wNo,$wDesc,$wSerial,$wQty,$wCost,$wPurpose,$wRemarks) {
    $pdf->SetX($tableX);
    $pdf->SetFillColor(164, 172, 124);
    $pdf->SetFont('Arial', 'B', 10);

    $pdf->Cell($wNo, $headerH, 'NO.', 1, 0, 'C', true);
    $pdf->Cell($wDesc, $headerH, 'ITEM(S) DESCRIPTION', 1, 0, 'C', true);
    $pdf->Cell($wSerial, $headerH, 'SERIAL NO.', 1, 0, 'C', true);
    $pdf->Cell($wQty, $headerH, 'QTY.', 1, 0, 'C', true);
    $pdf->Cell($wCost, $headerH, 'COST', 1, 0, 'C', true);
    $pdf->Cell($wPurpose, $headerH, 'PURPOSE', 1, 0, 'C', true);
    $pdf->Cell($wRemarks, $headerH, 'REMARKS', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);
};

$drawRow = function($rowNo, $item) use ($pdf,$tableX,$rowH,$wNo,$wDesc,$wSerial,$wQty,$wCost,$wPurpose,$wRemarks) {
    $pdf->SetX($tableX);
    $pdf->Cell($wNo, $rowH, $rowNo.'.', 1, 0, 'C');

    // DESCRIPTION
    $assetname = utf8_decode($item->assetname ?? '');
    $pdf->SetFont('Arial', '', (mb_strlen($assetname) > 17) ? 8 : 10);
    $pdf->Cell($wDesc, $rowH, $assetname, 1, 0, 'C');
    $pdf->SetFont('Arial', '', 10);

    // SERIAL
    $assettag = utf8_decode($item->assettag ?? '');
    $pdf->SetFont('Arial', '', (mb_strlen($assettag) > 12) ? 8 : 10);
    $pdf->Cell($wSerial, $rowH, $assettag, 1, 0, 'C');
    $pdf->SetFont('Arial', '', 10);

    // QTY
    $pdf->Cell($wQty, $rowH, '1', 1, 0, 'C');

    // COST
    $cost = utf8_decode($item->cost ?? '');
    $pdf->Cell($wCost, $rowH, $cost, 1, 0, 'C');

    // PURPOSE
    $purpose = utf8_decode($item->used ?? '');
    $pdf->SetFont('Arial', '', (mb_strlen($purpose) > 17) ? 8 : 10);
    $pdf->Cell($wPurpose, $rowH, $purpose, 1, 0, 'C');
    $pdf->SetFont('Arial', '', 10);

    // REMARKS
    $remarks = utf8_decode($item->remarks ?? '');
    $pdf->SetFont('Arial', '', (mb_strlen($remarks) > 17) ? 7 : 10);
    $pdf->Cell($wRemarks, $rowH, $remarks, 1, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
};

$drawBlankRow = function($rowNo) use ($pdf,$tableX,$rowH,$wNo,$wDesc,$wSerial,$wQty,$wCost,$wPurpose,$wRemarks) {
    $pdf->SetX($tableX);
    $pdf->Cell($wNo, $rowH, $rowNo.'.', 1, 0, 'C');
    $pdf->Cell($wDesc, $rowH, '', 1, 0, 'C');
    $pdf->Cell($wSerial, $rowH, '', 1, 0, 'C');
    $pdf->Cell($wQty, $rowH, '', 1, 0, 'C');
    $pdf->Cell($wCost, $rowH, '', 1, 0, 'C');
    $pdf->Cell($wPurpose, $rowH, '', 1, 0, 'C');
    $pdf->Cell($wRemarks, $rowH, '', 1, 1, 'C');
};

$printFooter = function() use ($pdf) {
    $pdf->SetXY(160, 345);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 6, 'CGM-OP-MCDRRM-01F1', 0, 0, 'C');
};

$drawSignatories = function($signY) use ($pdf,$first) {

    $receivedLabel = ($first->status == 1) ? 'RECEIVED BY:' : 'RETURNED BY:';

    // Top row
    $pdf->SetXY(20, $signY);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(70, 6, $receivedLabel, 0, 0, 'L');
    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(70, 6, 'CHECKED BY:', 0, 1, 'L');

    $pdf->SetX(20);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 6, utf8_decode($first->employeename), 'B', 0, 'C');
    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(70, 6, 'ALMOND G. GREGORIO', 0, 1, 'C');

    $pdf->SetX(20);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(70, 6, 'Signature Over Printed Name', 0, 0, 'C');
    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(70, 6, 'Section Head - Logistic', 0, 1, 'C');

    // Bottom row
    $pdf->Ln(10);
    $pdf->SetX(20);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(70, 6, 'ISSUED BY:', 0, 0, 'L');
    $pdf->Cell(40, 6, '', 0, 0, 'C');

    // ✅ Your rule: on RETURN remove NOTED BY
    if ($first->status == 1) {
        $pdf->Cell(70, 6, 'NOTED BY:', 0, 1, 'L');
    } else {
        $pdf->Cell(70, 6, '', 0, 1, 'L');
    }

    $pdf->SetX(20);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(70, 6, utf8_decode($first->fullname), 'B', 0, 'C');
    $pdf->Cell(40, 6, '', 0, 0, 'C');

    if ($first->status == 1) {
        $pdf->Cell(70, 6, 'ERWIN O. ALFONSO', 0, 1, 'C');
    } else {
        $pdf->Cell(70, 6, '', 0, 1, 'C');
    }

    $pdf->SetX(20);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(70, 6, 'Signature Over Printed Name', 0, 0, 'C');
    $pdf->Cell(40, 6, '', 0, 0, 'C');

    if ($first->status == 1) {
        $pdf->Cell(70, 6, 'Department Head - DDRM', 0, 1, 'C');
    } else {
        $pdf->Cell(70, 6, '', 0, 1, 'C');
    }
};

// -------------------------
// Print page 1 rows
// -------------------------
$total = count($data);
$index = 0;

// how many rows to draw on page 1
$dataP1 = min($total, $maxRowsP1);
$rowsToDrawP1 = max($fixedRowsP1, $dataP1);

// Print data rows (up to 10), then blanks to reach fixed/needed rows
$rowNo = 1;
for ($i = 0; $i < $rowsToDrawP1; $i++) {
    if ($i < $dataP1) {
        $drawRow($rowNo, $data[$index]);
        $index++;
    } else {
        $drawBlankRow($rowNo);
    }
    $rowNo++;
}

// Always footer on page 1
$printFooter();

// -------------------------
// If more data, continue on next pages (no max 10 limit)
// -------------------------
        if ($index < $total) {

    while ($index < $total) {
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(FALSE);
        $this->drawOfficialFooter($pdf);

        // Table header on new page
        $pdf->SetXY($tableX, $nextPageStartY);
        $drawHeader();

        // Start printing rows on page 2+
        // Compute how many rows fit before the footer/sign area.
        // Keep this safe value; adjust if you want more rows per page.
        $maxRowsThisPage = 24;

        $printedThisPage = 0;
        while ($index < $total && $printedThisPage < $maxRowsThisPage) {
            $drawRow($rowNo, $data[$index]);
            $index++;
            $rowNo++;
            $printedThisPage++;
        }

        // Footer on every page
        $printFooter();

        // If this was the last page, put signatories under last printed row
        if ($index >= $total) {
            $tableBottomY = $nextPageStartY + $headerH + ($printedThisPage * $rowH);
            $signY = $tableBottomY + 8;
            $drawSignatories($signY);
        }
    }

} else {
    // No extra pages; signatories below page 1 table
    $tableBottomY = $firstPageStartY + $headerH + ($rowsToDrawP1 * $rowH);
    $signY = $tableBottomY + 8;
    $drawSignatories($signY);
}

$baseLabel = ($first->status == 1) ? 'borrowed_form' : 'return_form';
$assetLabel = trim((string) ($first->assetname ?? 'asset'));
$assetTagLabel = trim((string) ($first->assettag ?? $id));
$safeAssetLabel = preg_replace('/[^A-Za-z0-9\-_]+/', '_', $assetLabel);
$safeAssetTagLabel = preg_replace('/[^A-Za-z0-9\-_]+/', '_', $assetTagLabel);
$filename = $baseLabel . '_' . trim($safeAssetLabel . '_' . $safeAssetTagLabel, '_') . '.pdf';
$pdf->SetTitle(ucwords(str_replace('_', ' ', $baseLabel)) . ' - ' . $assetLabel . ' (' . $assetTagLabel . ')');

return response($pdf->Output('S'), 200)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'inline; filename="'.$filename.'"');
}

    /**
     * insert checkin data  to database
     *
     * @param integer  $assetid
     * @param integer  $employeeid
     * @param string  $date
     * @param integer  $status
     * @return object
     */
    public function savecheckin(Request $request)
    {
        $assetid        = $request->input('assetid');
        $employeeid     = $request->input('employeeid1');
        $date           = $request->input('checkindate');
        $status         = '2'; //checkout = 1
        $checkstatus    = '0';
        $receiverby           = Auth::id();
        $controlno           = $request->input('controlno1');
        if (!$controlno) {
            $controlno = DB::table('asset_history')
                ->where('assetid', $assetid)
                ->where('status', '1')
                ->orderBy('created_at', 'desc')
                ->value('control_number');
        }
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $remarks        = $request->input('remarks');
        $data           = array('assetid' => $assetid, 'status' => $status, 'employeeid' => $employeeid, 'date' => $date, 'created_by' => $receiverby, 'control_number' => $controlno, 'created_at' => $created_at, 'updated_at' => $updated_at, 'remarks' => $remarks);
        $insertId       = DB::table('asset_history')->insertGetId($data);

        if ($insertId) {
            //set status in table asset
            $update = DB::table('assets')->where('id', $assetid)
                ->update(
                    [
                        'checkstatus'         => $checkstatus,
                        'updated_at'          => $updated_at,
                    ]
                );
            $res['success'] = 'success';
            $res['id'] = $insertId;
            $res['print_url'] = url('assetprintform/' . $insertId);
            $asset = DB::table('assets')->where('id', $assetid)->first();
            $this->auditTrail('Asset', 'Returned', 'Returned asset: '.($asset ? $asset->name.' ('.$asset->assettag.')' : 'asset ID '.$assetid).'.');
        } else {
            $res['success'] = 'failed';
        }

        return response($res);
    }

    /**
     * get all  from database
     * @return object
     */
    public function getrows()
    {
        $query = DB::table('assets');
        if (DB::getSchemaBuilder()->hasColumn('assets', 'is_delete')) {
            $query->where('is_delete', 0);
        }
        $data = $query->get();
        if ($data) {
            $res['success'] = true;
            $res['message'] = $data;
        }
        return response($res);
    }

    public function generateproductcode()
    {
        $lastid = DB::table('assets')->orderBy('id', 'desc')->first();

        if ($lastid) {
            $res['success'] = 'success';
            $res['message'] = 'AST' . date('ymd') . $lastid->id;
        } else {
            $res['message'] = 'AST' . date('ymd') . '1';
        }

        return response($res);
    }


    /**
     * delete to database
     *
     * @param integer $id
     * @return object
     */

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $delete = DB::table('assets')->where('id', $id)->where('is_delete', 0)
            ->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")]);

        if ($delete) {
            $res['success'] = 'success';
            $this->auditTrail('Asset', 'Delete', 'Deleted asset ID: '.$id.'.');
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }


    /**
     * Generate Product Code
     *
     * @return object
     */
    public function generateControlNumber(Request $request)
    {
        $prefix = strtoupper($request->get('prefix', 'BF'));
        $year = date('y');
        $pattern = $prefix . '-' . $year . '-%';

        return DB::transaction(function () use ($prefix, $year, $pattern) {
            $existingControlNumbers = DB::table('asset_history')
                ->where('control_number', 'like', $pattern)
                ->lockForUpdate()
                ->pluck('control_number');

            $maxSequence = 0;
            foreach ($existingControlNumbers as $controlNumber) {
                $parts = explode('-', $controlNumber);
                if (count($parts) < 3) {
                    continue;
                }

                $sequence = end($parts);
                if (ctype_digit((string) $sequence)) {
                    $maxSequence = max($maxSequence, (int) $sequence);
                }
            }

            $nextNumber = $maxSequence + 1;
            $controlNumber = $prefix . '-' . $year . '-' . $nextNumber;

            return response()->json([
                'success' => true,
                'message' => $controlNumber
            ]);
        });
    }



    // public function generateControlNumber($prefix)
    // {
    //     $year = Carbon::now()->format('y'); // e.g., "25"
    //     $baseFormat = "$prefix-$year-";

    //     // Get max control number from asset_history
    //     $latestAsset = DB::table('asset_history')
    //         ->where('control_number', 'like', "$baseFormat%")
    //         ->select(DB::raw("MAX(CAST(SUBSTRING(control_number, -4) AS UNSIGNED)) AS max_seq"))
    //         ->first();

    //     // Get max control number from component_assets
    //     // $latestComponent = DB::table('component_assets')
    //     //     ->where('control_number', 'like', "$baseFormat%")
    //     //     ->select(DB::raw("MAX(CAST(SUBSTRING(control_number, -4) AS UNSIGNED)) AS max_seq"))
    //     //     ->first();

    //     $maxSeq = max($latestAsset->max_seq ?? 0, $latestComponent->max_seq ?? 0);

    //     // If no records exist, return the default starting control number
    //     if ($maxSeq === 0) {
    //         return "$baseFormat" . "0000";
    //     }

    //     $nextSeq = str_pad($maxSeq + 1, 4, '0', STR_PAD_LEFT);

    //     return "$baseFormat$nextSeq";
    // }


    // public function scannerdata(Request $request)
    // {
    //     $tag = $request->input('value');

    // $data = DB::select("select assets.*, asset_history.b

    // from assets left join assets_history
    // on asset_history.assetid = assets.id
    // where asset.assettag = '$tag'");
    // return Datatables::of($data)

    //         ->make(true);
    // }

    public function scannerdata(Request $request)
    {
        $input = $request->input('input');
        $assetDeleteFilter = DB::getSchemaBuilder()->hasColumn('assets', 'is_delete') ? 'and a.is_delete = 0' : '';

        $isValid = DB::select("select a.*,b.*
        from assets as a left join asset_history as b
        on b.assetid = a.id
        where a.assettag = '$input'
        $assetDeleteFilter");

        return response()->json(['isValid' => $isValid]);
    }
}
