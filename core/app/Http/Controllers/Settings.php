<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingModel;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use Yajra\Datatables\Datatables;
use DB;
use App;
use Auth;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class Settings extends Controller
{
    use TraitSettings;
    use TraitAuditTrail;

    protected function officialFooterPath()
    {
        return resource_path('views/component/Munti_IssuanceForm_AMS/Munti_IssuanceForm_AMS/CGM FOOTER.png');
    }

    protected function drawOfficialFooter($pdf, $height = 18)
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

	public function __construct() {
		$data = $this->getapplications();
		$lang = $data->language;
		App::setLocale($lang);
		$this->middleware( 'auth' );
	}


	 //return settings
	 public function index() {
		return view( 'setting.index' );
    }

    public function audittrail() {
        $this->ensureAuditTrailTable();
        return view('setting.audittrail');
    }

    protected function auditTrailBaseQuery(Request $request = null)
    {
        $this->ensureAuditTrailTable();
        $this->backfillAuditTrailValues();

        $data = DB::table('audit_trail')
            ->select(['audit_trail.*']);

        if ($this->auditTrailHasColumn('user_name')) {
            $data->whereNotNull('user_name')
                ->where('user_name', '!=', '')
                ->where('user_name', '!=', 'System');
        }

        if ($request && $this->auditTrailHasColumn('created_at')) {
            $dateFrom = trim((string) $request->input('datefrom', ''));
            $dateTo = trim((string) $request->input('dateto', ''));

            if ($dateFrom !== '') {
                $parsedFrom = date('Y-m-d', strtotime($dateFrom));
                if ($parsedFrom && $parsedFrom !== '1970-01-01') {
                    $data->whereDate('created_at', '>=', $parsedFrom);
                }
            }

            if ($dateTo !== '') {
                $parsedTo = date('Y-m-d', strtotime($dateTo));
                if ($parsedTo && $parsedTo !== '1970-01-01') {
                    $data->whereDate('created_at', '<=', $parsedTo);
                }
            }
        }

        return $data->orderBy($this->auditTrailHasColumn('created_at') ? 'created_at' : 'id', 'desc');
    }

    public function audittraildata(Request $request) {
        $data = $this->auditTrailBaseQuery($request);

        return Datatables::of($data)
            ->addColumn('log_number', function ($single) {
                return $single->id;
            })
            ->addColumn('user', function ($single) {
                return property_exists($single, 'user_name') ? $single->user_name : '-';
            })
            ->addColumn('action_badge', function ($single) {
                $rawAction = property_exists($single, 'action') ? $single->action : '';
                $action = ucfirst($rawAction);
                $class = 'badge-secondary';
                if (strtolower($rawAction) == 'login') {
                    $class = 'badge-info';
                } elseif (strtolower($rawAction) == 'logout') {
                    $class = 'badge-secondary';
                } elseif (strtolower($rawAction) == 'create') {
                    $class = 'badge-success';
                } elseif (strtolower($rawAction) == 'update') {
                    $class = 'badge-warning';
                } elseif (strtolower($rawAction) == 'delete') {
                    $class = 'badge-danger';
                } elseif (strtolower($rawAction) == 'scan') {
                    $class = 'badge-primary';
                } elseif (strtolower($rawAction) == 'borrowed') {
                    $class = 'badge-warning';
                } elseif (strtolower($rawAction) == 'returned' || strtolower($rawAction) == 'return') {
                    $class = 'badge-info';
                } elseif (strtolower($rawAction) == 'serviceable') {
                    $class = 'badge-success';
                } elseif (strtolower($rawAction) == 'unserviceable') {
                    $class = 'badge-danger';
                }
                return '<span class="badge '.$class.'">'.$action.'</span>';
            })
            ->addColumn('details_link', function ($single) {
                return '<a href="#" class="audit-details" data-id="'.$single->id.'">View Details</a>';
            })
            ->addColumn('date_time', function ($single) {
                if (!property_exists($single, 'created_at') || empty($single->created_at)) {
                    return '-';
                }
                return date('M d, Y h:i:s A', strtotime($single->created_at));
            })
            ->rawColumns(['action_badge', 'details_link'])
            ->make(true);
    }

    protected function auditTrailPdfFit($pdf, $value, $width)
    {
        $text = trim((string) $value);
        if ($text === '') {
            return '-';
        }

        if ($pdf->GetStringWidth($text) <= ($width - 2)) {
            return $text;
        }

        while (strlen($text) > 0 && $pdf->GetStringWidth($text . '...') > ($width - 2)) {
            $text = substr($text, 0, -1);
        }

        return rtrim($text) . '...';
    }

    protected function drawAuditTrailPdfHeader($pdf, $title, $withLetterhead = true)
    {
        $pdf->AddPage();
        $this->drawOfficialFooter($pdf);
        $pageWidth = $pdf->GetPageWidth();

        if (!$withLetterhead) {
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetFillColor(37, 150, 190);
            $pdf->SetXY(6, 12);
            $pdf->Cell(12, 7, 'Log #', 1, 0, 'C', true);
            $pdf->Cell(38, 7, 'User', 1, 0, 'C', true);
            $pdf->Cell(34, 7, 'Module', 1, 0, 'C', true);
            $pdf->Cell(26, 7, 'Action', 1, 0, 'C', true);
            $pdf->Cell(118, 7, 'Details', 1, 0, 'C', true);
            $pdf->Cell(38, 7, 'Date & Time', 1, 1, 'C', true);
            return 19;
        }

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
        $pdf->SetX(0);
        $pdf->Cell($pageWidth, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(0);
        $pdf->Cell($pageWidth, 4, 'City of Muntinlupa', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(0, 25);
        $pdf->Cell($pageWidth, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(0);
        $pdf->Cell($pageWidth, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
        $pdf->SetX(0);
        $pdf->Cell($pageWidth, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
        $pdf->SetX(0);
        $pdf->Cell($pageWidth, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

        $pdf->SetLineWidth(0.5);
        $pdf->Line(12, 43, $pageWidth - 12, 43);
        $pdf->Line(12, 45, $pageWidth - 12, 45);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($pageWidth - 72, 50);
        $pdf->Cell(14, 5, 'DATE:', 0, 0, 'R');
        $pdf->Cell(30, 5, date('Y-m-d'), 'B', 0, 'C');

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetXY(0, 63);
        $pdf->Cell($pageWidth, 6, strtoupper($title), 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetFillColor(37, 150, 190);
        $pdf->SetXY(6, 78);
        $pdf->Cell(12, 7, 'Log #', 1, 0, 'C', true);
        $pdf->Cell(38, 7, 'User', 1, 0, 'C', true);
        $pdf->Cell(34, 7, 'Module', 1, 0, 'C', true);
        $pdf->Cell(26, 7, 'Action', 1, 0, 'C', true);
        $pdf->Cell(118, 7, 'Details', 1, 0, 'C', true);
        $pdf->Cell(38, 7, 'Date & Time', 1, 1, 'C', true);

        return 85;
    }

    public function printaudittrail(Request $request)
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $rows = $this->auditTrailBaseQuery($request)->get();

        $pdf = new \FPDF('L', 'mm', 'LETTER');
        $pdf->SetAutoPageBreak(false);
        $y = $this->drawAuditTrailPdfHeader($pdf, 'Audit Trail');

        foreach ($rows as $index => $row) {
            if ($y + 8 > ($pdf->GetPageHeight() - 12)) {
                $y = $this->drawAuditTrailPdfHeader($pdf, 'Audit Trail', false);
            }

            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY(6, $y);
            $pdf->Cell(12, 8, $row->id ?? ($index + 1), 1, 0, 'C');
            $pdf->Cell(38, 8, $this->auditTrailPdfFit($pdf, $row->user_name ?? '-', 38), 1, 0, 'L');
            $pdf->Cell(34, 8, $this->auditTrailPdfFit($pdf, $row->module ?? '-', 34), 1, 0, 'L');
            $pdf->Cell(26, 8, $this->auditTrailPdfFit($pdf, ucfirst($row->action ?? '-'), 26), 1, 0, 'C');
            $pdf->Cell(118, 8, $this->auditTrailPdfFit($pdf, $row->details ?? '-', 118), 1, 0, 'L');
            $pdf->Cell(38, 8, $this->auditTrailPdfFit($pdf, !empty($row->created_at) ? date('M d, Y h:i:s A', strtotime($row->created_at)) : '-', 38), 1, 1, 'C');
            $y += 8;
        }

        if ($rows->isEmpty()) {
            $pdf->SetXY(6, $y);
            $pdf->Cell(266, 8, 'No records found.', 1, 1, 'C');
        }

        $this->auditTrail('Settings', 'Print', 'Printed audit trail report.', 'Audit Trail', null, null, null);

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="audit_trail.pdf"');
    }

    public function audittrailbyid(Request $request) {
        $this->ensureAuditTrailTable();
        $this->backfillAuditTrailValues();
        $data = DB::table('audit_trail')->where('id', $request->input('id'))->first();

        if ($data) {
            foreach (['user_name', 'module', 'action', 'entity_type', 'entity_id', 'details', 'old_values', 'new_values', 'created_at'] as $column) {
                if (!property_exists($data, $column)) {
                    $data->{$column} = null;
                }
            }

            if ((empty($data->old_values) || $data->old_values === 'null' || $data->old_values === '{}') &&
                (empty($data->new_values) || $data->new_values === 'null' || $data->new_values === '{}') &&
                !empty($data->details)) {
                $parsed = $this->parseDetailsToValues($data->details);
                if (!empty($parsed['old']) || !empty($parsed['new'])) {
                    $data->old_values = json_encode($parsed['old']);
                    $data->new_values = json_encode($parsed['new']);
                }
            }

            $data->date_time = $data->created_at ? date('M d, Y h:i:s A', strtotime($data->created_at)) : '-';
            return response([
                'success' => 'success',
                'message' => $data
            ]);
        }

        return response(['success' => 'failed']);
    }

    /**
	 * get application settings
	 *
	 * @return object
	 */
	public function getdata() {
		$data = DB::table('settings')->where('id', '1')->first();
		if ($data) {

			$res['success'] = true;
			$res['data']  = $data;
			$res['logo']  = url('/').'/upload/'.$data->logo;
			$res['message'] = 'success';
			return response($res);
		}
    }


    /**
	 * update application settings to database
	 *
	 * @param string  $company
	 * @param string  $phone
	 * @param string  $city
	 * @param string  $website
	 * @param string  $address
	 * @param string  $currency
	 * @param string  $language
	 * @param string  $dateformat
	 * @return object
	 */
	public function update(Request $request){
        $company      = $request->input('company');
        $address      = $request->input('address');
		$email       = $request->input('email');
		$phonenumber = $request->input('phonenumber');
        $country    = $request->input('country');
        $logoname2  = $request->file('logo');
		$currency   = $request->input('currency');
		$language   = $request->input('language');
		$formatdate = $request->input('formatdate');

		$oldSettings = DB::table('settings')->where('id', '1')->first();


		$message = ['logo.mimes'=>trans('lang.type_image')];

		if ($request->hasFile('logo')) {
			$this->validate($request, [
				'logo' => 'image|mimes:jpeg,png,jpg|max:2048'
				],$message);
			$logoname  = $request->file('logo')->getClientOriginalName();
			$request->file('logo')->move(public_path("/upload"), $logoname);
			$update = DB::table('settings')->where('id', '1')
			->update(
				[
				'company'       =>$company,
				'address'       =>$address,
				'email'         =>$email,
				'phonenumber'   =>$phonenumber,
				'country'   	=>$country,
                'currency'      =>$currency,
                'language'      =>$language,
				'formatdate'    =>$formatdate,
				'logo'          =>$logoname
				]
			);
		} else{

			$update = DB::table('settings')->where('id', '1')->update(
				[
                    'company'       =>$company,
                    'address'       =>$address,
                    'email'         =>$email,
                    'phonenumber'   =>$phonenumber,
                    'country'   	=>$country,
                    'currency'      =>$currency,
                    'language'      =>$language,
                    'formatdate'    =>$formatdate
				]
			);
		}

		if ( $update ) {
			$res['message'] = 'success';
            $labelMap = [
                'company' => 'Company Name',
                'address' => 'Address',
                'email' => 'Email',
                'phonenumber' => 'Phone Number',
                'country' => 'Country',
                'currency' => 'Currency',
                'language' => 'Language',
                'formatdate' => 'Date Format',
                'logo' => 'Logo'
            ];
            $newData = [
                'company' => $company,
                'address' => $address,
                'email' => $email,
                'phonenumber' => $phonenumber,
                'country' => $country,
                'currency' => $currency,
                'language' => $language,
                'formatdate' => $formatdate
            ];
            if (isset($logoname)) {
                $newData['logo'] = $logoname;
            }
            $diff = $this->auditCalculateDiff($oldSettings, $newData, $labelMap);
            $detailsText = 'Updated application settings' . ($diff['details'] ? ":\n" . $diff['details'] : '');
            $this->auditTrail('Settings', 'Update', $detailsText, 'Settings', 1, $diff['old'], $diff['new']);

        } else{
            $res['message'] = 'failed';
        }

        return response( $res );


	}

}
