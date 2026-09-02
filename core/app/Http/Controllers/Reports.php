<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\GoalModel;
use App\SettingModel;
use App\Http\Controllers\TraitSettings;
use DB;
use Auth;
use App;

class Reports extends Controller
{

	use TraitSettings;

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

    private function reportSearchValue(Request $request)
    {
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = isset($search['value']) ? $search['value'] : '';
        }

        return trim((string) $search);
    }

    private function vehicleActivityBorrowerName($remarks, $fallback = '-')
    {
        $payload = json_decode((string) $remarks, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            return $fallback ?: '-';
        }

        $trip = [];
        if (isset($payload['vehicle_trip_ticket']) && is_array($payload['vehicle_trip_ticket'])) {
            $trip = $payload['vehicle_trip_ticket'];
        } elseif (isset($payload['borrower_signature_name'])) {
            $trip = $payload;
        }

        $borrower = $trip['borrower_signature_name'] ?? null;
        if (!$borrower && isset($payload['vehicle_return_ticket']['returning_signature_name'])) {
            $borrower = $payload['vehicle_return_ticket']['returning_signature_name'];
        }

        return trim((string) $borrower) !== '' ? $borrower : ($fallback ?: '-');
    }

	public function __construct() {
		$data = $this->getapplications();
		$lang = $data->language;
		App::setLocale($lang);
        $this->middleware('auth');
	}

	public function assetactivity() {
        return view( 'reports.assetactivitys' );
	}

	public function componentactivity() {
        return view( 'reports.componentactivity' );
	}

	public function maintenance() {
        return view( 'reports.maintenance' );
	}

	public function bytype() {
        return view( 'reports.bytype' );
	}

	public function bysupplier() {
        return view( 'reports.bysupplier' );
	}

	public function bylocation() {
        return view( 'reports.bylocation' );
	}

	public function bystatus() {
        return view( 'reports.bystatus' );
	}

	//show all report report view
	public function allreports(){
		return view('reports.reports');
	}


	/**
	 * get data asset from database
	 * @return object
	 */
    private function assetActivityQuery(Request $request)
    {
        $query = DB::table('asset_history')
        ->select('asset_history.*', 'assets.assettag as tag', 'assets.status as itemstatus', 'assets.name as asset', DB::raw("COALESCE(NULLIF(employees.fullname, ''), '-') as employees"), 'asset_type.name as type', 'location.name as location', DB::raw("COALESCE(NULLIF(users.fullname, ''), NULLIF(receiver.fullname, ''), '-') as fullname"))
        ->leftJoin('assets', 'assets.id', '=', 'asset_history.assetid')
        ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
        ->leftJoin('employees', 'employees.id', '=', 'asset_history.employeeid')
        ->leftJoin('location', 'location.id', '=', 'assets.locationid')
        ->leftJoin('users', 'users.id', '=', 'asset_history.created_by')
        ->leftJoin('receiver', 'receiver.id', '=', 'asset_history.created_by');

        if ($request->filled('status')) {
            $query->where('asset_history.status', $request->get('status'));
        }

        if ($request->get('asset_scope') === 'assets') {
            $query->where('assets.typeid', '!=', 7);
        } elseif ($request->get('asset_scope') === 'vehicle') {
            $query->where('assets.typeid', 7);
        }

        $search = $this->reportSearchValue($request);
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $like = '%' . $search . '%';
                $subQuery->where('assets.name', 'like', $like)
                    ->orWhere('assets.assettag', 'like', $like)
                    ->orWhere('location.name', 'like', $like)
                    ->orWhere('users.fullname', 'like', $like)
                    ->orWhere('receiver.fullname', 'like', $like)
                    ->orWhere('asset_history.remarks', 'like', $like);
            });
        }

        return $query
            ->orderBy('asset_history.updated_at', 'desc')
            ->orderBy('asset_history.created_at', 'desc');
    }

    public function getassetactivityreport(Request $request){
        $data = $this->assetActivityQuery($request)->get();

        $data->transform(function ($row) {
            $fallbackName = $row->employees ?? '-';
            if ($fallbackName === '-' || trim((string) $fallbackName) === '') {
                $fallbackName = $row->fullname ?? '-';
            }
            $row->employees = $this->vehicleActivityBorrowerName($row->remarks ?? '', $fallbackName);
            return $row;
        });

        return Datatables::of($data)
        ->addColumn( 'historystatus', function ( $accountsingle ) {
            return $accountsingle->status;
        } )
        
        ->addColumn( 'status', function ( $accountsingle ) {
           

            if($accountsingle->status==4){
                    $status = '<span class="badge badge-data text-white background-red">'.trans('lang.unserviceable').'</span>';

            } elseif($accountsingle->status==3){
                    $status = '<span class="badge badge-data text-white background-green">'.trans('lang.serviceable').'</span>';

            } elseif($accountsingle->status==2){
                    
                    $status = '<span class="badge badge-data text-white background-blue">'.trans('lang.checkin').'</span>';
                
            }else{
                    $status = '<span class="badge badge-data text-white background-yellow">'.trans('lang.checkout').'</span>';
            }

            return  $status;
           
        } )->rawColumns(['status'])
        ->make(true);		
    }

    public function printassetactivityreport(Request $request)
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $rows = $this->assetActivityQuery($request)->get();

        $rows->transform(function ($row) {
            $fallbackName = $row->employees ?? '-';
            if ($fallbackName === '-' || trim((string) $fallbackName) === '') {
                $fallbackName = $row->fullname ?? '-';
            }
            $row->employees = $this->vehicleActivityBorrowerName($row->remarks ?? '', $fallbackName);
            return $row;
        });

        $pdf = new \FPDF('P', 'mm', 'LETTER');
        $pdf->SetAutoPageBreak(false);
        $scopeLabel = 'All';
        if ($request->get('asset_scope') === 'assets') {
            $scopeLabel = 'Assets';
        } elseif ($request->get('asset_scope') === 'vehicle') {
            $scopeLabel = 'Assets Vehicle';
        }

        $drawHeader = function ($withLetterhead = true) use ($pdf, $scopeLabel) {
            $pdf->AddPage();
            $this->drawOfficialFooter($pdf);
            if (!$withLetterhead) {
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(4, 12);
                $pdf->SetFillColor(37, 150, 190);
                $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
                $pdf->Cell(32, 6, 'Asset', 1, 0, 'C', true);
                $pdf->Cell(24, 6, 'Asset Tag No.', 1, 0, 'C', true);
                $pdf->Cell(26, 6, 'Name', 1, 0, 'C', true);
                $pdf->Cell(18, 6, 'Status', 1, 0, 'C', true);
                $pdf->Cell(26, 6, 'Item Status', 1, 0, 'C', true);
                $pdf->Cell(18, 6, 'Office', 1, 0, 'C', true);
                $pdf->Cell(34, 6, 'Logistic Custodian', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Date', 1, 1, 'C', true);
                return;
            }
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(0, 7);
            $pdf->Cell(216, 4, 'Republic of the Philippines', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'City of Muntinlupa', 0, 1, 'C');

            $muntiLogo = app_path('fpdf/muntilogo.png');
            $ddrmLogo = app_path('fpdf/drlogo.png');
            if (file_exists($muntiLogo)) {
                $pdf->Image($muntiLogo, 17, 5, 25, 25);
            }
            if (file_exists($ddrmLogo)) {
                $pdf->Image($ddrmLogo, 175, 5, 24, 24);
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(0, 25);
            $pdf->Cell(216, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
            $pdf->SetXY(0, 35);
            $pdf->Cell(216, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

            $pdf->SetXY(12, 43.5);
            $pdf->SetFillColor(33, 19, 13);
            $pdf->Cell(192, 0.5, '', 1, 1, 'C', true);
            $pdf->SetXY(12, 45);
            $pdf->Cell(192, 0.2, '', 1, 1, 'C', true);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(160, 50);
            $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
            $pdf->Cell(24, 4, date('Y-m-d'), 'B', 0, 'C');

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(0, 65);
            $pdf->Cell(216, 4, 'ASSETS ACTIVITY REPORT', 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(0, 70);
            $pdf->Cell(216, 5, '(' . strtoupper($scopeLabel) . ')', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(4, 80);
            $pdf->SetFillColor(37, 150, 190);
            $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
            $pdf->Cell(32, 6, 'Asset', 1, 0, 'C', true);
            $pdf->Cell(24, 6, 'Asset Tag No.', 1, 0, 'C', true);
            $pdf->Cell(26, 6, 'Name', 1, 0, 'C', true);
            $pdf->Cell(18, 6, 'Status', 1, 0, 'C', true);
            $pdf->Cell(26, 6, 'Item Status', 1, 0, 'C', true);
            $pdf->Cell(18, 6, 'Office', 1, 0, 'C', true);
            $pdf->Cell(34, 6, 'Logistic Custodian', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Date', 1, 1, 'C', true);
        };

        $fit = function ($value, $width) use ($pdf) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
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

        $historyStatusText = function ($status) {
            if ((int) $status === 4) {
                return 'Unserviceable';
            }
            if ((int) $status === 3) {
                return 'Serviceable';
            }
            if ((int) $status === 2) {
                return 'Returned';
            }
            return 'Borrowed';
        };

        $itemStatusText = function ($itemstatus, $historystatus) {
            if ((int) $historystatus === 4) {
                return 'Unserviceable';
            }
            if ((int) $historystatus === 1) {
                return 'Serviceable';
            }
            if (in_array((int) $historystatus, [2, 3], true)) {
                return 'Ready to Deploy';
            }

            switch ((int) $itemstatus) {
                case 1:
                    return 'Ready to Deploy';
                case 2:
                    return 'Pending';
                case 3:
                    return 'Archived';
                case 4:
                    return 'Broken';
                case 5:
                    return 'Lost';
                case 6:
                    return 'Unserviceable';
                default:
                    return 'Undefined';
            }
        };

        $drawHeader();
        $y = 86;

        foreach ($rows as $index => $row) {
            if ($y + 7 > 265) {
                $drawHeader(false);
                $y = 18;
            }

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY(4, $y);
            $pdf->Cell(8, 7, ($index + 1) . '.', 1, 0, 'C');
            $pdf->Cell(32, 7, $fit($row->asset, 32), 1, 0, 'C');
            $pdf->Cell(24, 7, $fit($row->tag, 24), 1, 0, 'C');
            $pdf->Cell(26, 7, $fit($row->employees, 26), 1, 0, 'C');
            $pdf->Cell(18, 7, $fit($historyStatusText($row->historystatus ?? $row->status), 18), 1, 0, 'C');
            $pdf->Cell(26, 7, $fit($itemStatusText($row->itemstatus, $row->historystatus ?? $row->status), 26), 1, 0, 'C');
            $pdf->Cell(18, 7, $fit($row->location, 18), 1, 0, 'C');
            $pdf->Cell(34, 7, $fit($row->fullname, 34), 1, 0, 'C');
            $pdf->Cell(20, 7, $fit($row->date, 20), 1, 1, 'C');
            $y += 7;
        }

        if ($rows->isEmpty()) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(4, $y);
            $pdf->Cell(204, 8, 'No records found.', 1, 1, 'C');
            $y += 8;
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="asset_activity_report.pdf"');
    }

    /**
	 * get data  component from database
	 * @return object
	 */
    public function getcomponentactivityreport(){
        $data = DB::table('component_assets')
        ->select('component_assets.*', 'component.name as component','assets.name as asset', 'asset_type.name as type', 'location.name as location')
        ->leftJoin('assets', 'assets.id', '=', 'component_assets.assetid')
        ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
        ->leftJoin('location', 'location.id', '=', 'assets.locationid')
        ->leftJoin('component', 'component.id', '=', 'component_assets.componentid')
        ->offset(0)->limit(10)
		->orderBy('component_assets.updated_at', 'desc')
		->orderBy('component_assets.created_at', 'desc')
		->get();

        return Datatables::of($data)
        
        ->addColumn( 'status', function ( $accountsingle ) {
           

            if($accountsingle->status==2){
                    
                    $status = '<span class="badge badge-data text-white background-blue">'.trans('lang.checkin').'</span>';
                
            }else{
                    $status = '<span class="badge badge-data text-white background-yellow">'.trans('lang.checkout').'</span>';
            }

            return  $status;
           
        } )->rawColumns(['status'])
        ->make(true);		
    }

    private function issuanceActivityQuery(Request $request = null)
    {
        $query = DB::table('component_assets')
            ->select(
                'component_assets.*',
                DB::raw("COALESCE(NULLIF(component.serial, ''), '-') as cserial"),
                DB::raw("COALESCE(receiver.fullname, component_assets.created_by) as rfullname"),
                'component_assets.control_number as ccontrolno',
                DB::raw("COALESCE(NULLIF(component.name, ''), NULLIF(assets.name, ''), '-') as component"),
                'employees.fullname as employees',
                'employees.mobile_number',
                DB::raw("COALESCE(issue_department.name, issue_department.description, employee_department.name, employee_department.description, component_assets.department) as departmentname"),
                'assets.name as asset',
                'asset_type.name as type',
                'location.name as location'
            )
            ->leftJoin('assets', 'assets.id', '=', 'component_assets.assetid')
            ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('employees', 'employees.id', '=', 'component_assets.employeeid')
            ->leftJoin('department as employee_department', 'employee_department.id', '=', 'employees.departmentid')
            ->leftJoin('department as issue_department', 'issue_department.id', '=', 'component_assets.department')
            ->leftJoin('location', 'location.id', '=', 'assets.locationid')
            ->leftJoin('component', 'component.id', '=', 'component_assets.componentid')
            ->leftJoin('receiver', 'receiver.id', '=', 'component_assets.created_by')
            ->orderBy('component_assets.updated_at', 'desc')
            ->orderBy('component_assets.created_at', 'desc');

        if ($request) {
            $search = $this->reportSearchValue($request);
            if ($search !== '') {
                $query->where(function ($subQuery) use ($search) {
                    $like = '%' . $search . '%';
                    $subQuery->where('component.name', 'like', $like)
                        ->orWhere('component.serial', 'like', $like)
                        ->orWhere('component_assets.control_number', 'like', $like)
                        ->orWhere('component_assets.quantity', 'like', $like)
                        ->orWhere('employees.fullname', 'like', $like)
                        ->orWhere('employees.mobile_number', 'like', $like)
                        ->orWhere('issue_department.name', 'like', $like)
                        ->orWhere('issue_department.description', 'like', $like)
                        ->orWhere('employee_department.name', 'like', $like)
                        ->orWhere('employee_department.description', 'like', $like)
                        ->orWhere('receiver.fullname', 'like', $like)
                        ->orWhere('component_assets.created_by', 'like', $like);
                });
            }
        }

        return $query;
    }

    public function printissuanceactivityreport(Request $request)
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $rows = $this->issuanceActivityQuery($request)->get();
        $pdf = new \FPDF('P', 'mm', 'LETTER');
        $pdf->SetAutoPageBreak(false);

        $drawHeader = function ($withLetterhead = true) use ($pdf) {
            $pdf->AddPage();
            $this->drawOfficialFooter($pdf);
            if (!$withLetterhead) {
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(4, 12);
                $pdf->SetFillColor(37, 150, 190);
                $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
                $pdf->Cell(22, 6, 'Equipment Name', 1, 0, 'C', true);
                $pdf->Cell(22, 6, 'Serial', 1, 0, 'C', true);
                $pdf->Cell(22, 6, 'Control Number', 1, 0, 'C', true);
                $pdf->Cell(10, 6, 'Qty', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Issued to', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Mobile No.', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Office', 1, 0, 'C', true);
                $pdf->Cell(24, 6, 'Issuance Type', 1, 0, 'C', true);
                $pdf->Cell(22, 6, 'Logistic Custodian', 1, 0, 'C', true);
                $pdf->Cell(18, 6, 'Date', 1, 1, 'C', true);
                return;
            }
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(0, 7);
            $pdf->Cell(216, 4, 'Republic of the Philippines', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'City of Muntinlupa', 0, 1, 'C');

            $muntiLogo = app_path('fpdf/muntilogo.png');
            $ddrmLogo = app_path('fpdf/drlogo.png');
            if (file_exists($muntiLogo)) {
                $pdf->Image($muntiLogo, 17, 5, 25, 25);
            }
            if (file_exists($ddrmLogo)) {
                $pdf->Image($ddrmLogo, 175, 5, 24, 24);
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(0, 25);
            $pdf->Cell(216, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
            $pdf->SetXY(0, 35);
            $pdf->Cell(216, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

            $pdf->SetXY(12, 43.5);
            $pdf->SetFillColor(33, 19, 13);
            $pdf->Cell(192, 0.5, '', 1, 1, 'C', true);
            $pdf->SetXY(12, 45);
            $pdf->Cell(192, 0.2, '', 1, 1, 'C', true);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(160, 50);
            $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
            $pdf->Cell(24, 4, date('Y-m-d'), 'B', 0, 'C');

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(0, 65);
            $pdf->Cell(216, 4, 'ISSUANCE ACTIVITY REPORT', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(4, 80);
            $pdf->SetFillColor(37, 150, 190);
            $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
            $pdf->Cell(22, 6, 'Equipment Name', 1, 0, 'C', true);
            $pdf->Cell(22, 6, 'Serial', 1, 0, 'C', true);
            $pdf->Cell(22, 6, 'Control Number', 1, 0, 'C', true);
            $pdf->Cell(10, 6, 'Qty', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Issued to', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Mobile No.', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Office', 1, 0, 'C', true);
            $pdf->Cell(24, 6, 'Issuance Type', 1, 0, 'C', true);
            $pdf->Cell(22, 6, 'Logistic Custodian', 1, 0, 'C', true);
            $pdf->Cell(18, 6, 'Date', 1, 1, 'C', true);
        };

        $fit = function ($value, $width) use ($pdf) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
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

        $issuanceTypeText = function ($type) {
            if ((int) $type === 1) {
                return trans('lang.mr');
            }
            if ((int) $type === 2) {
                return trans('lang.dod');
            }
            if ((int) $type === 3) {
                return trans('lang.issuanceform');
            }
            return '';
        };

        $drawHeader();
        $y = 86;

        foreach ($rows as $index => $row) {
            if ($y + 7 > 265) {
                $drawHeader(false);
                $y = 18;
            }

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY(4, $y);
            $pdf->Cell(8, 7, ($index + 1) . '.', 1, 0, 'C');
            $pdf->Cell(22, 7, $fit($row->component, 22), 1, 0, 'C');
            $pdf->Cell(22, 7, $fit($row->cserial, 22), 1, 0, 'C');
            $pdf->Cell(22, 7, $fit($row->ccontrolno, 22), 1, 0, 'C');
            $pdf->Cell(10, 7, $fit($row->quantity, 10), 1, 0, 'C');
            $pdf->Cell(20, 7, $fit($row->employees, 20), 1, 0, 'C');
            $pdf->Cell(20, 7, $fit($row->mobile_number, 20), 1, 0, 'C');
            $pdf->Cell(20, 7, $fit($row->departmentname, 20), 1, 0, 'C');
            $pdf->Cell(24, 7, $fit($issuanceTypeText($row->issuancetype), 24), 1, 0, 'C');
            $pdf->Cell(22, 7, $fit($row->rfullname, 22), 1, 0, 'C');
            $pdf->Cell(18, 7, $fit($row->date, 18), 1, 1, 'C');
            $y += 7;
        }

        if ($rows->isEmpty()) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(4, $y);
            $pdf->Cell(200, 8, 'No records found.', 1, 1, 'C');
            $y += 8;
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="issuance_activity_report.pdf"');
    }

    private function maintenanceReportRows()
    {
        $loggedInFullname = DB::getPdo()->quote(Auth::user()->fullname ?: '-');

        $query = DB::table('maintenance')
            ->leftJoin('supplier', 'maintenance.supplierid', '=', 'supplier.id')
            ->leftJoin('assets', 'maintenance.assetid', '=', 'assets.id')
            ->leftJoin('receiver', 'maintenance.created_by', '=', 'receiver.id')
            ->leftJoin('employees', 'maintenance.created_by', '=', 'employees.id')
            ->leftJoin('users', 'maintenance.created_by', '=', 'users.id')
            ->select(
                'maintenance.*',
                'supplier.name as supplier',
                'assets.name as asset',
                'assets.assettag',
                DB::raw("COALESCE(NULLIF(users.fullname, ''), NULLIF(receiver.fullname, ''), NULLIF(employees.fullname, ''), CASE WHEN maintenance.created_by is null OR maintenance.created_by = '' OR maintenance.created_by = '0' THEN $loggedInFullname ELSE NULLIF(maintenance.created_by, '') END, '-') as fullname")
            )
            ->where('maintenance.type', '!=', 'Operational');

        if (DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete')) {
            $query->where('maintenance.is_delete', 0);
        }

        return $query->orderBy('maintenance.created_at', 'desc')->get();
    }

    public function printmaintenancereport()
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $rows = $this->maintenanceReportRows();
        $pdf = new \FPDF('P', 'mm', 'LETTER');
        $pdf->SetAutoPageBreak(false);

        $drawHeader = function ($withLetterhead = true) use ($pdf) {
            $pdf->AddPage();
            $this->drawOfficialFooter($pdf);
            if (!$withLetterhead) {
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(6, 12);
                $pdf->SetFillColor(37, 150, 190);
                $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
                $pdf->Cell(28, 6, 'Asset Tag No.', 1, 0, 'C', true);
                $pdf->Cell(28, 6, 'Asset', 1, 0, 'C', true);
                $pdf->Cell(38, 6, 'Supplier', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Type', 1, 0, 'C', true);
                $pdf->Cell(25, 6, 'Start Date', 1, 0, 'C', true);
                $pdf->Cell(25, 6, 'End Date', 1, 0, 'C', true);
                $pdf->Cell(24, 6, 'Completion Day(s)', 1, 1, 'C', true);
                return;
            }
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(0, 7);
            $pdf->Cell(216, 4, 'Republic of the Philippines', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'City of Muntinlupa', 0, 1, 'C');

            $muntiLogo = app_path('fpdf/muntilogo.png');
            $ddrmLogo = app_path('fpdf/drlogo.png');
            if (file_exists($muntiLogo)) {
                $pdf->Image($muntiLogo, 17, 5, 25, 25);
            }
            if (file_exists($ddrmLogo)) {
                $pdf->Image($ddrmLogo, 175, 5, 24, 24);
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(0, 25);
            $pdf->Cell(216, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
            $pdf->SetXY(0, 35);
            $pdf->Cell(216, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

            $pdf->SetXY(12, 43.5);
            $pdf->SetFillColor(33, 19, 13);
            $pdf->Cell(192, 0.5, '', 1, 1, 'C', true);
            $pdf->SetXY(12, 45);
            $pdf->Cell(192, 0.2, '', 1, 1, 'C', true);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(160, 50);
            $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
            $pdf->Cell(24, 4, date('Y-m-d'), 'B', 0, 'C');

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(0, 65);
            $pdf->Cell(216, 4, 'MAINTENANCE REPORT', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(6, 80);
            $pdf->SetFillColor(37, 150, 190);
            $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
            $pdf->Cell(28, 6, 'Asset Tag No.', 1, 0, 'C', true);
            $pdf->Cell(28, 6, 'Asset', 1, 0, 'C', true);
            $pdf->Cell(38, 6, 'Supplier', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Type', 1, 0, 'C', true);
            $pdf->Cell(25, 6, 'Start Date', 1, 0, 'C', true);
            $pdf->Cell(25, 6, 'End Date', 1, 0, 'C', true);
            $pdf->Cell(24, 6, 'Completion Day(s)', 1, 1, 'C', true);
        };

        $fit = function ($value, $width) use ($pdf) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
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

        $completionDays = function ($startdate, $enddate) {
            if (empty($startdate) || empty($enddate)) {
                return '';
            }

            $start = strtotime($startdate);
            $end = strtotime($enddate);
            if (!$start || !$end) {
                return '';
            }

            return (string) floor(abs($end - $start) / (60 * 60 * 24));
        };

        $drawHeader();
        $y = 86;

        foreach ($rows as $index => $row) {
            if ($index > 0 && $index % 20 === 0) {
                $drawHeader(false);
                $y = 18;
            }

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY(6, $y);
            $pdf->Cell(8, 7, ($index + 1) . '.', 1, 0, 'C');
            $pdf->Cell(28, 7, $fit($row->assettag, 28), 1, 0, 'C');
            $pdf->Cell(28, 7, $fit($row->asset, 28), 1, 0, 'C');
            $pdf->Cell(38, 7, $fit($row->supplier, 38), 1, 0, 'C');
            $pdf->Cell(20, 7, $fit($row->type, 20), 1, 0, 'C');
            $pdf->Cell(25, 7, $fit($row->startdate, 25), 1, 0, 'C');
            $pdf->Cell(25, 7, $fit($row->enddate, 25), 1, 0, 'C');
            $pdf->Cell(24, 7, $fit($completionDays($row->startdate, $row->enddate), 24), 1, 1, 'C');
            $y += 7;
        }

        if ($rows->isEmpty()) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(6, $y);
            $pdf->Cell(196, 8, 'No records found.', 1, 1, 'C');
            $y += 8;
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="maintenance_report.pdf"');
    }

    public function printmaintenancelist()
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $rows = $this->maintenanceReportRows();
        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);

        $drawHeader = function ($withLetterhead = true) use ($pdf) {
            $pdf->AddPage();
            $this->drawOfficialFooter($pdf);
            if (!$withLetterhead) {
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(10, 12);
                $pdf->SetFillColor(37, 150, 190);
                $pdf->Cell(10, 7, 'No.', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'Asset Tag No.', 1, 0, 'C', true);
                $pdf->Cell(34, 7, 'Equipment Name', 1, 0, 'C', true);
                $pdf->Cell(36, 7, 'Maintenance Description', 1, 0, 'C', true);
                $pdf->Cell(32, 7, 'Start Date', 1, 0, 'C', true);
                $pdf->Cell(32, 7, 'End Date', 1, 0, 'C', true);
                $pdf->Cell(70, 7, 'Remarks', 1, 0, 'C', true);
                $pdf->Cell(33, 7, 'Maintained By', 1, 1, 'C', true);
                return;
            }
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(0, 7);
            $pdf->Cell(297, 4, 'Republic of the Philippines', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(0);
            $pdf->Cell(297, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(297, 4, 'City of Muntinlupa', 0, 1, 'C');

            $muntiLogo = app_path('fpdf/muntilogo.png');
            $ddrmLogo = app_path('fpdf/drlogo.png');
            if (file_exists($muntiLogo)) {
                $pdf->Image($muntiLogo, 28, 5, 25, 25);
            }
            if (file_exists($ddrmLogo)) {
                $pdf->Image($ddrmLogo, 244, 5, 24, 24);
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(0, 25);
            $pdf->Cell(297, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(297, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
            $pdf->SetXY(0, 35);
            $pdf->Cell(297, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
            $pdf->SetX(0);
            $pdf->Cell(297, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

            $pdf->SetXY(14, 43.5);
            $pdf->SetFillColor(33, 19, 13);
            $pdf->Cell(269, 0.5, '', 1, 1, 'C', true);
            $pdf->SetXY(14, 45);
            $pdf->Cell(269, 0.2, '', 1, 1, 'C', true);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(226, 50);
            $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
            $pdf->Cell(28, 4, date('Y-m-d'), 'B', 0, 'C');

            $pdf->SetFont('Arial', 'B', 13);
            $pdf->SetXY(0, 65);
            $pdf->Cell(297, 5, 'MAINTENANCE LIST', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(10, 80);
            $pdf->SetFillColor(37, 150, 190);
            $pdf->Cell(10, 7, 'No.', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'Asset Tag No.', 1, 0, 'C', true);
            $pdf->Cell(34, 7, 'Equipment Name', 1, 0, 'C', true);
            $pdf->Cell(36, 7, 'Maintenance Description', 1, 0, 'C', true);
            $pdf->Cell(32, 7, 'Start Date', 1, 0, 'C', true);
            $pdf->Cell(32, 7, 'End Date', 1, 0, 'C', true);
            $pdf->Cell(70, 7, 'Remarks', 1, 0, 'C', true);
            $pdf->Cell(33, 7, 'Maintained By', 1, 1, 'C', true);
        };

        $fit = function ($value, $width) use ($pdf) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
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

        $drawHeader();
        $y = 87;

        foreach ($rows as $index => $row) {
            if ($index > 0 && $index % 16 === 0) {
                $drawHeader(false);
                $y = 19;
            }

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY(10, $y);
            $pdf->Cell(10, 8, ($index + 1) . '.', 1, 0, 'C');
            $pdf->Cell(30, 8, $fit($row->assettag, 30), 1, 0, 'C');
            $pdf->Cell(34, 8, $fit($row->asset, 34), 1, 0, 'C');
            $pdf->Cell(36, 8, $fit($row->type, 36), 1, 0, 'C');
            $pdf->Cell(32, 8, $fit($row->startdate, 32), 1, 0, 'C');
            $pdf->Cell(32, 8, $fit($row->enddate, 32), 1, 0, 'C');
            $pdf->Cell(70, 8, $fit($row->reason_remarks, 70), 1, 0, 'L');
            $pdf->Cell(33, 8, $fit($row->fullname, 33), 1, 1, 'C');
            $y += 8;
        }

        if ($rows->isEmpty()) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(10, $y);
            $pdf->Cell(277, 8, 'No records found.', 1, 1, 'C');
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="maintenance_list.pdf"');
    }


    /**
	 * get asset data by type from database
	 * @return object
	 */
    public function getdatabytypereport(Request $request){
    	$data = DB::table('assets')
		->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
		->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
		->leftjoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
		->leftJoin('location', 'assets.locationid', '=', 'location.id')
		->select(['assets.*', 'supplier.name as supplier', 'brand.name as brand','brand.id as brandid', 'asset_type.name as type' , 'location.name as location']);
		
       return Datatables::of($data)
        ->addColumn('pictures',function($single){
			return '<img src="'.url('/').'/upload/assets/'.$single->picture.'" style="width:90px"/>';
        })
        ->filter(function ($query) use ($request) {
				if ($request->has('assettype')) {

					$query->where('assets.typeid', 'like', "%{$request->get('assettype')}%");
				} 
			})
        ->rawColumns(['pictures'])
        ->make(true);		
    }

    public function printbytypereport(Request $request)
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $assettype = trim((string) $request->get('assettype', ''));
        $typeLabel = 'All';

        $query = DB::table('assets')
            ->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
            ->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
            ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('location', 'assets.locationid', '=', 'location.id')
            ->select([
                'assets.*',
                'brand.name as brand',
                'asset_type.name as type',
                'location.name as location',
            ]);

        if ($assettype !== '') {
            $query->where('assets.typeid', $assettype);
            $typeLabel = DB::table('asset_type')->where('id', $assettype)->value('name') ?: 'All';
        }

        $rows = $query->orderBy('asset_type.name')->orderBy('assets.name')->get();

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);

        $drawHeader = function ($withLetterhead = true) use ($pdf, $typeLabel) {
            $pdf->AddPage();
            $this->drawOfficialFooter($pdf);
            if (!$withLetterhead) {
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(4, 12);
                $pdf->SetFillColor(37, 150, 190);
                $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Picture', 1, 0, 'C', true);
                $pdf->Cell(28, 6, 'Asset Tag No.', 1, 0, 'C', true);
                $pdf->Cell(18, 6, 'Purchase Date', 1, 0, 'C', true);
                $pdf->Cell(15, 6, 'Cost', 1, 0, 'C', true);
                $pdf->Cell(31, 6, 'Description', 1, 0, 'C', true);
                $pdf->Cell(29, 6, 'Equipment Name', 1, 0, 'C', true);
                $pdf->Cell(14, 6, 'Type', 1, 0, 'C', true);
                $pdf->Cell(18, 6, 'Brand', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Office', 1, 1, 'C', true);
                return;
            }
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(0, 7);
            $pdf->Cell(216, 4, 'Republic of the Philippines', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'City of Muntinlupa', 0, 1, 'C');

            $muntiLogo = app_path('fpdf/muntilogo.png');
            $ddrmLogo = app_path('fpdf/drlogo.png');
            if (file_exists($muntiLogo)) {
                $pdf->Image($muntiLogo, 17, 5, 25, 25);
            }
            if (file_exists($ddrmLogo)) {
                $pdf->Image($ddrmLogo, 175, 5, 24, 24);
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(0, 25);
            $pdf->Cell(216, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
            $pdf->SetXY(0, 35);
            $pdf->Cell(216, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

            $pdf->SetXY(12, 43.5);
            $pdf->SetFillColor(33, 19, 13);
            $pdf->Cell(192, 0.5, '', 1, 1, 'C', true);
            $pdf->SetXY(12, 45);
            $pdf->Cell(192, 0.2, '', 1, 1, 'C', true);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(160, 50);
            $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
            $pdf->Cell(24, 4, date('Y-m-d'), 'B', 0, 'C');

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(0, 65);
            $pdf->Cell(216, 4, 'REPORT BY TYPE', 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(0, 70);
            $pdf->Cell(216, 5, '(' . strtoupper($typeLabel) . ')', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(4, 80);
            $pdf->SetFillColor(37, 150, 190);
            $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Picture', 1, 0, 'C', true);
            $pdf->Cell(28, 6, 'Asset Tag No.', 1, 0, 'C', true);
            $pdf->Cell(18, 6, 'Purchase Date', 1, 0, 'C', true);
            $pdf->Cell(15, 6, 'Cost', 1, 0, 'C', true);
            $pdf->Cell(31, 6, 'Description', 1, 0, 'C', true);
            $pdf->Cell(29, 6, 'Equipment Name', 1, 0, 'C', true);
            $pdf->Cell(14, 6, 'Type', 1, 0, 'C', true);
            $pdf->Cell(18, 6, 'Brand', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Office', 1, 1, 'C', true);
        };

        $fit = function ($value, $width) use ($pdf) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
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

        $isPrintableImage = function ($path) {
            if (!file_exists($path)) {
                return false;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'gif'], true)) {
                return true;
            }

            if ($extension !== 'png') {
                return false;
            }

            $handle = fopen($path, 'rb');
            if (!$handle) {
                return false;
            }

            $header = fread($handle, 29);
            fclose($handle);

            return strlen($header) >= 29 && substr($header, 0, 8) === "\x89PNG\x0d\x0a\x1a\x0a" && ord($header[28]) === 0;
        };

        $drawPicture = function ($path, $x, $y, $cellWidth, $cellHeight) use ($pdf, $isPrintableImage) {
            if (!$isPrintableImage($path)) {
                return;
            }

            $size = getimagesize($path);
            if (!$size || empty($size[0]) || empty($size[1])) {
                return;
            }

            $maxWidth = $cellWidth - 1;
            $maxHeight = $cellHeight - 1;
            $scale = min($maxWidth / $size[0], $maxHeight / $size[1]);
            $imageWidth = $size[0] * $scale;
            $imageHeight = $size[1] * $scale;
            $imageX = $x + (($cellWidth - $imageWidth) / 2);
            $imageY = $y + (($cellHeight - $imageHeight) / 2);

            $pdf->Image($path, $imageX, $imageY, $imageWidth, $imageHeight);
        };

        $drawHeader();
        $y = 86;
        $rowHeight = 10;
        $pageBottom = 287;

        foreach ($rows as $index => $row) {
            if ($index > 0 && ($y + $rowHeight) > $pageBottom) {
                $drawHeader(false);
                $y = 18;
            }

            $pdf->SetFont('Arial', '', 6.8);
            $pdf->SetXY(4, $y);
            $pdf->Cell(8, 10, ($index + 1) . '.', 1, 0, 'C');

            $x = $pdf->GetX();
            $pdf->Cell(20, 10, '', 1, 0, 'C');
            $picture = base_path('../upload/assets/' . $row->picture);
            $drawPicture($picture, $x, $y, 20, 10);

            $pdf->Cell(28, 10, $fit($row->assettag, 28), 1, 0, 'C');
            $pdf->Cell(18, 10, $fit($row->purchasedate, 18), 1, 0, 'C');
            $pdf->Cell(15, 10, $fit(is_numeric($row->cost) ? number_format($row->cost) : $row->cost, 15), 1, 0, 'R');
            $pdf->Cell(31, 10, $fit($row->description, 31), 1, 0, 'C');
            $pdf->Cell(29, 10, $fit($row->name, 29), 1, 0, 'C');
            $pdf->Cell(14, 10, $fit($row->type, 14), 1, 0, 'C');
            $pdf->Cell(18, 10, $fit($row->brand, 18), 1, 0, 'C');
            $pdf->Cell(20, 10, $fit($row->location, 20), 1, 1, 'C');
            $y += 10;
        }

        if ($rows->isEmpty()) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(4, $y);
            $pdf->Cell(201, 8, 'No records found.', 1, 1, 'C');
            $y += 8;
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="report_by_type.pdf"');
    }

    public function printbystatusreport(Request $request)
    {
        $statustype = trim((string) $request->get('statustype', ''));
        $statusLabel = 'All';
        $statusLabels = [
            1 => trans('lang.readytodeploy'),
            2 => trans('lang.pending'),
            3 => trans('lang.archived'),
            4 => trans('lang.broken'),
            5 => trans('lang.lost'),
            6 => trans('lang.unserviceable'),
        ];

        $query = $this->assetReportBaseQuery();
        if ($statustype !== '') {
            $query->where('assets.status', $statustype);
            $statusLabel = isset($statusLabels[(int) $statustype]) ? $statusLabels[(int) $statustype] : 'All';
        }

        return $this->renderAssetFilteredReportPdf(
            $query->orderBy('assets.status')->orderBy('assets.name')->get(),
            'REPORT BY STATUS',
            $statusLabel,
            'Status',
            function ($row) use ($statusLabels) {
                return isset($statusLabels[(int) $row->status]) ? $statusLabels[(int) $row->status] : '';
            },
            'report_by_status.pdf'
        );
    }

    public function printbysupplierreport(Request $request)
    {
        $supplierid = trim((string) $request->get('supplierid', ''));
        $supplierLabel = 'All';

        $query = $this->assetReportBaseQuery();
        if ($supplierid !== '') {
            $query->where('assets.supplierid', $supplierid);
            $supplierLabel = DB::table('supplier')->where('id', $supplierid)->value('name') ?: 'All';
        }

        return $this->renderAssetFilteredReportPdf(
            $query->orderBy('supplier.name')->orderBy('assets.name')->get(),
            'REPORT BY SUPPLIER',
            $supplierLabel,
            'Supplier',
            function ($row) {
                return $row->supplier;
            },
            'report_by_supplier.pdf'
        );
    }

    public function printbylocationreport(Request $request)
    {
        $locationid = trim((string) $request->get('locationid', ''));
        $locationLabel = 'All';

        $query = $this->assetReportBaseQuery();
        if ($locationid !== '') {
            $query->where('assets.locationid', $locationid);
            $locationLabel = DB::table('location')->where('id', $locationid)->value('name') ?: 'All';
        }

        return $this->renderAssetFilteredReportPdf(
            $query->orderBy('location.name')->orderBy('assets.name')->get(),
            'REPORT BY OFFICE',
            $locationLabel,
            'Office',
            function ($row) {
                return $row->location;
            },
            'report_by_office.pdf'
        );
    }

    protected function assetReportBaseQuery()
    {
        return DB::table('assets')
            ->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
            ->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
            ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
            ->leftJoin('location', 'assets.locationid', '=', 'location.id')
            ->select([
                'assets.*',
                'supplier.name as supplier',
                'brand.name as brand',
                'asset_type.name as type',
                'location.name as location',
            ]);
    }

    protected function renderAssetFilteredReportPdf($rows, $title, $filterLabel, $filterColumnTitle, $filterValueCallback, $filename)
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);

        $drawHeader = function ($withLetterhead = true) use ($pdf, $title, $filterLabel, $filterColumnTitle) {
            $pdf->AddPage();
            $this->drawOfficialFooter($pdf);
            if (!$withLetterhead) {
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetXY(4, 12);
                $pdf->SetFillColor(37, 150, 190);
                $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Picture', 1, 0, 'C', true);
                $pdf->Cell(28, 6, 'Asset Tag No.', 1, 0, 'C', true);
                $pdf->Cell(18, 6, 'Purchase Date', 1, 0, 'C', true);
                $pdf->Cell(15, 6, 'Cost', 1, 0, 'C', true);
                $pdf->Cell(31, 6, 'Description', 1, 0, 'C', true);
                $pdf->Cell(29, 6, 'Equipment Name', 1, 0, 'C', true);
                $pdf->Cell(14, 6, $filterColumnTitle, 1, 0, 'C', true);
                $pdf->Cell(18, 6, 'Brand', 1, 0, 'C', true);
                $pdf->Cell(20, 6, 'Office', 1, 1, 'C', true);
                return;
            }
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(0, 7);
            $pdf->Cell(216, 4, 'Republic of the Philippines', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'City of Muntinlupa', 0, 1, 'C');

            $muntiLogo = app_path('fpdf/muntilogo.png');
            $ddrmLogo = app_path('fpdf/drlogo.png');
            if (file_exists($muntiLogo)) {
                $pdf->Image($muntiLogo, 17, 5, 25, 25);
            }
            if (file_exists($ddrmLogo)) {
                $pdf->Image($ddrmLogo, 175, 5, 24, 24);
            }

            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(0, 25);
            $pdf->Cell(216, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetX(0);
            $pdf->Cell(216, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
            $pdf->SetXY(0, 35);
            $pdf->Cell(216, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
            $pdf->SetX(0);
            $pdf->Cell(216, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

            $pdf->SetXY(12, 43.5);
            $pdf->SetFillColor(33, 19, 13);
            $pdf->Cell(192, 0.5, '', 1, 1, 'C', true);
            $pdf->SetXY(12, 45);
            $pdf->Cell(192, 0.2, '', 1, 1, 'C', true);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetXY(160, 50);
            $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
            $pdf->Cell(24, 4, date('Y-m-d'), 'B', 0, 'C');

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(0, 65);
            $pdf->Cell(216, 4, $title, 0, 1, 'C');
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetXY(0, 70);
            $pdf->Cell(216, 5, '(' . strtoupper($filterLabel) . ')', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(4, 80);
            $pdf->SetFillColor(37, 150, 190);
            $pdf->Cell(8, 6, 'No.', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Picture', 1, 0, 'C', true);
            $pdf->Cell(28, 6, 'Asset Tag No.', 1, 0, 'C', true);
            $pdf->Cell(18, 6, 'Purchase Date', 1, 0, 'C', true);
            $pdf->Cell(15, 6, 'Cost', 1, 0, 'C', true);
            $pdf->Cell(31, 6, 'Description', 1, 0, 'C', true);
            $pdf->Cell(29, 6, 'Equipment Name', 1, 0, 'C', true);
            $pdf->Cell(14, 6, $filterColumnTitle, 1, 0, 'C', true);
            $pdf->Cell(18, 6, 'Brand', 1, 0, 'C', true);
            $pdf->Cell(20, 6, 'Office', 1, 1, 'C', true);
        };

        $fit = function ($value, $width) use ($pdf) {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
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

        $isPrintableImage = function ($path) {
            if (!file_exists($path)) {
                return false;
            }

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'gif'], true)) {
                return true;
            }

            if ($extension !== 'png') {
                return false;
            }

            $handle = fopen($path, 'rb');
            if (!$handle) {
                return false;
            }

            $header = fread($handle, 29);
            fclose($handle);

            return strlen($header) >= 29 && substr($header, 0, 8) === "\x89PNG\x0d\x0a\x1a\x0a" && ord($header[28]) === 0;
        };

        $drawPicture = function ($path, $x, $y, $cellWidth, $cellHeight) use ($pdf, $isPrintableImage) {
            if (!$isPrintableImage($path)) {
                return;
            }

            $size = getimagesize($path);
            if (!$size || empty($size[0]) || empty($size[1])) {
                return;
            }

            $maxWidth = $cellWidth - 1;
            $maxHeight = $cellHeight - 1;
            $scale = min($maxWidth / $size[0], $maxHeight / $size[1]);
            $imageWidth = $size[0] * $scale;
            $imageHeight = $size[1] * $scale;
            $imageX = $x + (($cellWidth - $imageWidth) / 2);
            $imageY = $y + (($cellHeight - $imageHeight) / 2);

            $pdf->Image($path, $imageX, $imageY, $imageWidth, $imageHeight);
        };

        $drawHeader();
        $y = 86;

        foreach ($rows as $index => $row) {
            if ($index > 0 && $index % 20 === 0) {
                $drawHeader(false);
                $y = 18;
            }

            $pdf->SetFont('Arial', '', 6.8);
            $pdf->SetXY(4, $y);
            $pdf->Cell(8, 10, ($index + 1) . '.', 1, 0, 'C');

            $x = $pdf->GetX();
            $pdf->Cell(20, 10, '', 1, 0, 'C');
            $picture = base_path('../upload/assets/' . $row->picture);
            $drawPicture($picture, $x, $y, 20, 10);

            $pdf->Cell(28, 10, $fit($row->assettag, 28), 1, 0, 'C');
            $pdf->Cell(18, 10, $fit($row->purchasedate, 18), 1, 0, 'C');
            $pdf->Cell(15, 10, $fit(is_numeric($row->cost) ? number_format($row->cost) : $row->cost, 15), 1, 0, 'R');
            $pdf->Cell(31, 10, $fit($row->description, 31), 1, 0, 'C');
            $pdf->Cell(29, 10, $fit($row->name, 29), 1, 0, 'C');
            $pdf->Cell(14, 10, $fit($filterValueCallback($row), 14), 1, 0, 'C');
            $pdf->Cell(18, 10, $fit($row->brand, 18), 1, 0, 'C');
            $pdf->Cell(20, 10, $fit($row->location, 20), 1, 1, 'C');
            $y += 10;
        }

        if ($rows->isEmpty()) {
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(4, $y);
            $pdf->Cell(201, 8, 'No records found.', 1, 1, 'C');
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    private function printableAssetImage($picture)
    {
        $picture = trim((string) $picture);
        if ($picture === '') {
            return null;
        }

        $path = public_path('upload/assets/' . $picture);
        if (!file_exists($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return null;
        }

        if ($extension === 'png') {
            $bytes = @file_get_contents($path, false, null, 0, 29);
            if ($bytes === false || strlen($bytes) < 29 || substr($bytes, 1, 3) !== 'PNG' || ord($bytes[28]) !== 0) {
                return null;
            }
        }

        return $path;
    }

    private function shortPdfText($value, $limit = 45)
    {
        $value = preg_replace('/\s+/', ' ', trim((string) $value));
        if ($value === '') {
            return '-';
        }

        return strlen($value) > $limit ? substr($value, 0, $limit - 3) . '...' : $value;
    }

    private function drawOfficialListHeader($pdf, $title, $subtitle = '')
    {
        $pdf->AddPage();
        $this->drawOfficialFooter($pdf);
        $pdf->SetFont('Arial', 'B', 8);
        $pageWidth = $pdf->GetPageWidth();
        $rightLogoX = max(10, $pageWidth - 42);
        $lineEnd = $pageWidth - 12;

        $muntiLogo = app_path('fpdf/muntilogo.png');
        $ddrmLogo = app_path('fpdf/drlogo.png');
        if (file_exists($muntiLogo)) {
            $pdf->Image($muntiLogo, 18, 5, 24, 24);
        }
        if (file_exists($ddrmLogo)) {
            $pdf->Image($ddrmLogo, $rightLogoX, 5, 24, 24);
        }

        $pdf->SetXY(0, 8);
        $pdf->Cell($pageWidth, 4, 'Republic of the Philippines', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 9);
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
        $pdf->Line(12, 36, $lineEnd, 36);
        $pdf->Line(12, 38, $lineEnd, 38);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($pageWidth - 71, 45);
        $pdf->Cell(15, 5, 'DATE:', 0, 0, 'R');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(28, 5, date('Y-m-d'), 'B', 0, 'C');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetXY(0, 60);
        $pdf->Cell($pageWidth, 6, strtoupper($title), 0, 1, 'C');
        if ($subtitle !== '') {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell($pageWidth, 5, '(' . strtoupper($subtitle) . ')', 0, 1, 'C');
        }

        return 75;
    }

    public function printassetlist()
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $assetDeleteFilter = DB::getSchemaBuilder()->hasColumn('assets', 'is_delete') ? 'AND assets.is_delete = 0' : '';
        $rows = DB::select("
            SELECT
                assets.assettag,
                assets.name,
                assets.picture,
                assets.purchasedate,
                assets.description,
                brand.name as brand,
                asset_type.name as type,
                location.name as location
            FROM assets
            LEFT JOIN brand ON assets.brandid = brand.id
            LEFT JOIN asset_type ON assets.typeid = asset_type.id
            LEFT JOIN location ON assets.locationid = location.id
            WHERE assets.typeid != 7
            $assetDeleteFilter
            ORDER BY assets.name, assets.assettag
        ");

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $y = $this->drawOfficialListHeader($pdf, 'Asset List');
        $widths = [9, 20, 34, 40, 25, 22, 22, 16];

        $drawTableHeader = function () use ($pdf, $widths, &$y) {
            $pdf->SetFillColor(41, 158, 190);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY(10, $y);
            foreach (['No.', 'Picture', 'Asset Tag', 'Asset Name', 'Description', 'Brand', 'Office', 'Type'] as $i => $header) {
                $pdf->Cell($widths[$i], 8, $header, 1, 0, 'C', true);
            }
            $pdf->Ln();
            $y += 8;
        };

        $drawTableHeader();
        $pdf->SetFont('Arial', '', 7);

        foreach ($rows as $index => $row) {
            if ($y > 260) {
                $pdf->AddPage();
                $this->drawOfficialFooter($pdf);
                $y = 12;
                $drawTableHeader();
                $pdf->SetFont('Arial', '', 7);
            }

            $pdf->SetXY(10, $y);
            $pdf->Cell($widths[0], 14, ($index + 1) . '.', 1, 0, 'C');
            $x = $pdf->GetX();
            $pdf->Cell($widths[1], 14, '', 1, 0, 'C');
            if ($image = $this->printableAssetImage($row->picture ?? '')) {
                $pdf->Image($image, $x + 2, $y + 2, 18, 10);
            }
            $pdf->Cell($widths[2], 14, $this->shortPdfText($row->assettag ?? '', 22), 1, 0, 'L');
            $pdf->Cell($widths[3], 14, $this->shortPdfText($row->name ?? '', 28), 1, 0, 'L');
            $pdf->Cell($widths[4], 14, $this->shortPdfText($row->description ?? '', 18), 1, 0, 'L');
            $pdf->Cell($widths[5], 14, $this->shortPdfText($row->brand ?? '', 16), 1, 0, 'L');
            $pdf->Cell($widths[6], 14, $this->shortPdfText($row->location ?? '', 16), 1, 0, 'L');
            $pdf->Cell($widths[7], 14, $this->shortPdfText($row->type ?? '', 10), 1, 0, 'L');
            $y += 14;
        }

        if (empty($rows)) {
            $pdf->SetXY(10, $y);
            $pdf->Cell(array_sum($widths), 10, 'No records found.', 1, 1, 'C');
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="asset_list.pdf"');
    }

    public function printassetvehiclelist()
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $assetDeleteFilter = DB::getSchemaBuilder()->hasColumn('assets', 'is_delete') ? 'AND assets.is_delete = 0' : '';
        $rows = DB::select("
            SELECT assets.*, brand.name as brand, location.name as location, ah.status as historystatus
            FROM assets
            LEFT JOIN brand ON assets.brandid = brand.id
            LEFT JOIN location ON assets.locationid = location.id
            LEFT JOIN (
                SELECT h1.*
                FROM asset_history h1
                INNER JOIN (
                    SELECT assetid, MAX(id) as max_id
                    FROM asset_history
                    GROUP BY assetid
            ) h2 ON h1.assetid = h2.assetid AND h1.id = h2.max_id
            ) ah ON ah.assetid = assets.id
            WHERE assets.typeid = 7
            $assetDeleteFilter
            ORDER BY assets.created_at DESC
        ");

        $pdf = new \FPDF('L', 'mm', 'LEGAL');
        $pdf->SetAutoPageBreak(false);
        $y = $this->drawOfficialListHeader($pdf, 'Asset Vehicle List');
        $y = 68;
        $widths = [8, 18, 22, 36, 18, 22, 16, 16, 32, 26, 16, 20, 20, 22, 22];
        $headers = ['No.', 'Picture', 'Plate', 'Vehicle Name', 'Brand', 'Category', 'Year', 'Acquired', 'Chassis No.', 'Engine No.', 'Fuel', 'Trans.', 'Office', 'Status', 'History'];
        $tableX = max(8, ($pdf->GetPageWidth() - array_sum($widths)) / 2);

        $drawTableHeader = function () use ($pdf, $widths, $headers, $tableX, &$y) {
            $pdf->SetFillColor(41, 158, 190);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 6.5);
            $pdf->SetXY($tableX, $y);
            foreach ($headers as $i => $header) {
                $pdf->Cell($widths[$i], 8, $header, 1, 0, 'C', true);
            }
            $pdf->Ln();
            $y += 8;
        };

        $statusText = function ($status, $checkstatus) {
            if ((string) $status === '6') {
                return 'Unserviceable';
            }
            if ((string) $checkstatus === '2') {
                return 'Serviceable';
            }
            $labels = [
                '1' => 'Ready to Deploy',
                '2' => 'Pending',
                '3' => 'Archived',
                '4' => 'Broken',
                '5' => 'Lost'
            ];
            return $labels[(string) $status] ?? 'Ready to Deploy';
        };

        $historyText = function ($historyStatus, $checkstatus, $status) {
            $labels = [
                '1' => 'Borrowed',
                '2' => 'Returned',
                '3' => 'Serviceable',
                '4' => 'Unserviceable'
            ];
            if (isset($labels[(string) $historyStatus])) {
                return $labels[(string) $historyStatus];
            }
            if ((string) $checkstatus === '2') {
                return 'Borrowed';
            }
            if ((string) $status === '6') {
                return 'Unserviceable';
            }
            return 'Returned';
        };

        $drawTableHeader();
        $pdf->SetFont('Arial', '', 6.5);

        foreach ($rows as $index => $row) {
            if ($y + 16 > 205) {
                $pdf->AddPage();
                $this->drawOfficialFooter($pdf);
                $y = 12;
                $drawTableHeader();
                $pdf->SetFont('Arial', '', 6.5);
            }

            $values = [
                ($index + 1) . '.',
                '',
                $this->shortPdfText($row->assettag ?? '', 16),
                $this->shortPdfText($row->name ?? '', 22),
                $this->shortPdfText($row->brand ?? '', 12),
                $this->shortPdfText($row->vehiclecategory ?? '', 14),
                $this->shortPdfText($row->yearmodel ?? '', 8),
                $this->shortPdfText($row->yearacquired ?? '', 8),
                $this->shortPdfText($row->chassis ?? '', 22),
                $this->shortPdfText($row->engineno ?? '', 16),
                $this->shortPdfText($row->fueltype ?? '', 10),
                $this->shortPdfText($row->transmission ?? '', 12),
                $this->shortPdfText($row->location ?? '', 12),
                $statusText($row->status ?? '', $row->checkstatus ?? ''),
                $historyText($row->historystatus ?? '', $row->checkstatus ?? '', $row->status ?? '')
            ];

            $pdf->SetXY($tableX, $y);
            foreach ($values as $i => $value) {
                $x = $pdf->GetX();
                $pdf->Cell($widths[$i], 16, $value, 1, 0, $i === 0 || $i === 1 ? 'C' : 'L');
                if ($i === 1 && ($image = $this->printableAssetImage($row->picture ?? ''))) {
                    $pdf->Image($image, $x + 2, $y + 2, 14, 11);
                }
            }
            $y += 16;
        }

        if (empty($rows)) {
            $pdf->SetXY($tableX, $y);
            $pdf->Cell(array_sum($widths), 10, 'No records found.', 1, 1, 'C');
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="asset_vehicle_list.pdf"');
    }


    /**
	 * get asset data by status from database
	 * @return object
	 */
    public function getdatabystatusreport(Request $request){
    	$data = DB::table('assets')
		->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
		->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
		->leftjoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
		->leftJoin('location', 'assets.locationid', '=', 'location.id')
		->select(['assets.*', 'supplier.name as supplier', 'brand.name as brand','brand.id as brandid', 'asset_type.name as type' , 'location.name as location']);
		

       return Datatables::of($data)
        ->addColumn('pictures',function($single){
			return '<img src="'.url('/').'/upload/assets/'.$single->picture.'" style="width:90px"/>';
        })
        ->addColumn('status2', function($single){
        	//set status
            $status = trans('lang.undefined');
            if($single->status=='1'){
                $status = trans('lang.readytodeploy');
            }
            if($single->status=='2'){
                $status = trans('lang.pending');
            }
            if($single->status=='3'){
                $status = trans('lang.archived');
            }
            if($single->status=='4'){
                $status = trans('lang.broken');
            }
            if($single->status=='5'){
                $status = trans('lang.lost');
            }
            if($single->status=='6'){
                $status = trans('lang.unserviceable');
            }

            return $status;
        })
        ->filter(function ($query) use ($request) {
				if ($request->has('statustype')) {

					$query->where('assets.status', 'like', "%{$request->get('statustype')}%");
				} 
			})
        ->rawColumns(['pictures','status2'])
        ->make(true);		
    }


    /**
	 * get asset data by supplier from database
	 * @return object
	 */
    public function getdatabysupplierreport(Request $request){
    	$data = DB::table('assets')
		->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
		->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
		->leftjoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
		->leftJoin('location', 'assets.locationid', '=', 'location.id')
		->select(['assets.*', 'supplier.name as supplier', 'brand.name as brand','brand.id as brandid', 'asset_type.name as type' , 'location.name as location']);
		
       return Datatables::of($data)
        ->addColumn('pictures',function($single){
			return '<img src="'.url('/').'/upload/assets/'.$single->picture.'" style="width:90px"/>';
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
    public function getdatabylocationreport(Request $request){
    	$data = DB::table('assets')
		->leftJoin('brand', 'assets.brandid', '=', 'brand.id')
		->leftJoin('supplier', 'assets.supplierid', '=', 'supplier.id')
		->leftjoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
		->leftJoin('location', 'assets.locationid', '=', 'location.id')
		->select(['assets.*', 'supplier.name as supplier', 'brand.name as brand','brand.id as brandid', 'asset_type.name as type' , 'location.name as location']);
		
       return Datatables::of($data)
        ->addColumn('pictures',function($single){
			return '<img src="'.url('/').'/upload/assets/'.$single->picture.'" style="width:90px"/>';
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
