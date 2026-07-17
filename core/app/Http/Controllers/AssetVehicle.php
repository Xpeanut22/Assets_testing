<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AssetsVehicleModel;
use Illuminate\Support\Facades\File;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;
use Milon\Barcode\DNS2D;


class AssetVehicle extends Controller
{
    use TraitSettings;

    public function __construct()
    {

        $data = $this->getapplications();
        $lang = $data->language;
        App::setLocale($lang);
        $this->middleware('auth');
    }

    private function syncUnserviceableMaintenance($assetid, $createdBy, $date, $updated_at, $remarks = null)
    {
        $hasMaintenanceDeleteColumn = DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete');
        $maintenanceQuery = DB::table('maintenance')
            ->where('assetid', $assetid)
            ->where('type', '!=', 'Operational')
            ->whereNull('enddate');

        if ($hasMaintenanceDeleteColumn) {
            $maintenanceQuery->where('is_delete', 0);
        }

        $maintenance = $maintenanceQuery->orderBy('id', 'desc')->first();
        $remarks = trim((string) $remarks);
        $hasCustomRemarks = $remarks !== '';
        $remarks = $hasCustomRemarks ? $remarks : 'Marked as unserviceable from Asset Vehicle. Edit the problem details here.';
        $maintenanceData = [
            'assetid' => $assetid,
            'type' => 'Unserviceable',
            'reason_remarks' => $remarks,
            'startdate' => $date ?: $updated_at,
            'enddate' => null,
            'created_by' => $createdBy,
            'updated_at' => $updated_at
        ];

        if ($maintenance) {
            if (!$hasCustomRemarks && !empty($maintenance->reason_remarks) && $maintenance->reason_remarks !== $maintenanceData['reason_remarks']) {
                unset($maintenanceData['reason_remarks']);
            }

            DB::table('maintenance')->where('id', $maintenance->id)->update($maintenanceData);
            return;
        }

        $maintenanceData['created_at'] = $updated_at;
        if ($hasMaintenanceDeleteColumn) {
            $maintenanceData['is_delete'] = 0;
        }

        DB::table('maintenance')->insert($maintenanceData);
    }

    private function closeUnserviceableMaintenance($assetid, $date, $updated_at)
    {
        $hasMaintenanceDeleteColumn = DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete');
        $maintenanceQuery = DB::table('maintenance')
            ->where('assetid', $assetid)
            ->where('type', '!=', 'Operational');

        if ($hasMaintenanceDeleteColumn) {
            $maintenanceQuery->where('is_delete', 0);
        }

        $maintenance = (clone $maintenanceQuery)->whereNull('enddate')->orderBy('id', 'desc')->first();

        if (!$maintenance) {
            $maintenance = $maintenanceQuery->orderBy('id', 'desc')->first();
        }

        if ($maintenance) {
            DB::table('maintenance')->where('id', $maintenance->id)->update([
                'enddate' => $date ?: $updated_at,
                'updated_at' => $updated_at
            ]);
        }
    }

    //return page
    public function index()
    {
        return view('assetvehicle.index');
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

    private function assetHistoryHasColumn($column)
    {
        return DB::getSchemaBuilder()->hasColumn('asset_history', $column);
    }

    private function generateVehicleTripControlNumber()
    {
        if (!$this->assetHistoryHasColumn('control_number')) {
            return '';
        }

        $prefix = 'BF';
        $year = date('y');
        $last = DB::table('asset_history')
            ->where('control_number', 'like', $prefix . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($last && !empty($last->control_number)) {
            $parts = explode('-', $last->control_number);
            $nextNumber = isset($parts[2]) ? ((int) $parts[2]) + 1 : 1;
        }

        return $prefix . '-' . $year . '-' . $nextNumber;
    }

    private function vehicleTripTicketData(Request $request)
    {
        $divisions = json_decode((string) $request->input('divisions_json'), true);
        $passengers = json_decode((string) $request->input('passengers_json'), true);
        $divisions = is_array($divisions) ? array_slice($divisions, 0, 10) : [];
        $passengers = is_array($passengers) ? array_slice($passengers, 0, 10) : [];

        return [
            'name_of_driver' => $request->input('name_of_driver'),
            'phone' => $request->input('phone'),
            'destination' => $request->input('destination'),
            'purpose' => $request->input('purpose'),
            'date_borrowed' => $request->input('date_borrowed'),
            'time_of_departure' => $request->input('time_of_departure') ?: date('h:i A', strtotime($request->input('checkoutdate') ?: date("Y-m-d H:i:s"))),
            'departure_mileage' => $request->input('departure_mileage'),
            'division_1' => $request->input('division_1'),
            'division_2' => $request->input('division_2'),
            'division_3' => $request->input('division_3'),
            'divisions' => $divisions,
            'passenger_1' => $request->input('passenger_1'),
            'passenger_2' => $request->input('passenger_2'),
            'passenger_3' => $request->input('passenger_3'),
            'passengers' => $passengers,
            'trip_remarks' => $request->input('trip_remarks'),
            'borrower_signature_name' => $request->input('borrower_signature_name'),
            'supervising_officer' => $request->input('supervising_officer'),
            'custodian' => Auth::user()->fullname
        ];
    }

    private function vehicleReturnTripTicketData(Request $request)
    {
        return [
            'date_return' => $request->input('date_return'),
            'time_of_arrival' => $request->input('time_of_arrival') ?: date('h:i A', strtotime($request->input('checkindate') ?: date("Y-m-d H:i:s"))),
            'arrival_mileage' => $request->input('arrival_mileage'),
            'return_remarks' => $request->input('return_remarks'),
            'returning_signature_name' => $request->input('returning_signature_name'),
            'custodian' => Auth::user()->fullname
        ];
    }

    private function decodeVehicleTripTicket($remarks)
    {
        $decoded = json_decode((string) $remarks, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decoded['vehicle_trip_ticket'])) {
            return $decoded['vehicle_trip_ticket'];
        }

        $fallback = [];
        $keys = [
            'name_of_driver',
            'phone',
            'destination',
            'purpose',
            'date_borrowed',
            'time_of_departure',
            'departure_mileage',
            'division_1',
            'division_2',
            'division_3',
            'passenger_1',
            'passenger_2',
            'passenger_3',
            'trip_remarks',
            'borrower_signature_name',
            'returning_signature_name',
            'supervising_officer',
            'custodian'
        ];

        foreach ($keys as $key) {
            if (preg_match('/"' . preg_quote($key, '/') . '":"([^"]*)"/', (string) $remarks, $matches)) {
                $fallback[$key] = stripcslashes($matches[1]);
            }
        }

        return $fallback;
    }

    private function vehicleHistoryRemarks($remarks)
    {
        $decoded = json_decode((string) $remarks, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return $remarks ?: '';
        }

        if (isset($decoded['vehicle_return_ticket']) && is_array($decoded['vehicle_return_ticket'])) {
            return $decoded['vehicle_return_ticket']['return_remarks'] ?? ($decoded['remarks'] ?? '');
        }

        if (isset($decoded['vehicle_trip_ticket']) && is_array($decoded['vehicle_trip_ticket'])) {
            return $decoded['vehicle_trip_ticket']['trip_remarks'] ?? ($decoded['remarks'] ?? '');
        }

        return $decoded['trip_remarks'] ?? $decoded['return_remarks'] ?? $decoded['remarks'] ?? '';
    }

    private function vehicleHistoryName($remarks, $fallback = '-')
    {
        $decoded = json_decode((string) $remarks, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return $fallback ?: '-';
        }

        if (isset($decoded['vehicle_return_ticket']['returning_signature_name'])) {
            $name = $decoded['vehicle_return_ticket']['returning_signature_name'];
        } elseif (isset($decoded['vehicle_trip_ticket']['borrower_signature_name'])) {
            $name = $decoded['vehicle_trip_ticket']['borrower_signature_name'];
        } else {
            $name = $decoded['returning_signature_name'] ?? $decoded['borrower_signature_name'] ?? null;
        }

        return trim((string) $name) !== '' ? $name : ($fallback ?: '-');
    }

    private function getLatestBorrowedVehicleHistory($assetid, $beforeId = null)
    {
        $query = DB::table('asset_history')
            ->where('assetid', $assetid)
            ->where('status', '1');

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        return $query->orderBy('id', 'desc')->first();
    }

    private function vehicleTripPdfResponse($history, $trip, $formTitle, $dateLabel, $dateValue, $timeLabel, $timeValue, $mileageLabel, $mileageValue, $filenamePrefix, $signatureLabel = 'Borrower Name over Signature')
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $getTrip = function ($key, $fallback = '') use ($trip) {
            return isset($trip[$key]) && $trip[$key] !== null && $trip[$key] !== '' ? $trip[$key] : $fallback;
        };

        $date = $history->date ? date('Y-m-d', strtotime($history->date)) : date('Y-m-d');
        $vehicleName = $history->assetname ?: ($history->vehiclecategory ?: 'Vehicle');
        $custodian = $getTrip('custodian', $history->employeename ?: '');
        $controlNo = isset($history->control_number) ? $history->control_number : '';

        $text = function ($value) {
            return utf8_decode((string) ($value ?? ''));
        };

        $pdf = new \FPDF('P', 'mm', 'Legal');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(0, 7);
        $pdf->Cell(216, 4, 'Republic of the Philippines', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(0);
        $pdf->Cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(0);
        $pdf->Cell(216, 4, 'City of Muntinlupa', 0, 0, 'C');

        if (file_exists(public_path('muntilogo.png'))) {
            $pdf->Image(public_path('muntilogo.png'), 17, 5, 25, 25);
        }
        if (file_exists(public_path('drlogo.png'))) {
            $pdf->Image(public_path('drlogo.png'), 175, 5, 24, 24);
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

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(10, 53);
        $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
        $pdf->Cell(25, 4, $text($date), 'B', 0, 'C');
        $pdf->SetXY(155, 47);
        $pdf->Cell(12, 5, 'CGM-OP-MCDDRM-01F3', 0, 0, 'L');
        $pdf->SetXY(155, 53);
        $pdf->Cell(18, 5, 'Control #:', 0, 0, 'L');
        $pdf->Cell(30, 4, $text($controlNo), 'B', 0, 'C');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(0, 65);
        $pdf->Cell(216, 4, $formTitle, 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetXY(10, 80);
        $pdf->SetFillColor(103, 190, 217);
        $pdf->Cell(196, 8, 'VEHICLE TRIP TICKET.', 1, 1, 'C', true);

        $drawBox = function ($x, $y, $w, $label, $value, $labelH = 5, $valueH = 15) use ($pdf, $text) {
            $pdf->SetXY($x, $y);
            $pdf->SetFont('Arial', 'B', 8.5);
            $pdf->Cell($w, $labelH, $label, 'LTR', 1, 'L');
            $pdf->SetX($x);
            $pdf->SetFont('Arial', '', 8.5);
            $pdf->Cell($w, $valueH, $text($value), 'LBR', 1, 'C');
        };

        $drawBox(10, 88.6, 65, 'Name of Driver:', $getTrip('name_of_driver'));
        $drawBox(75.6, 88.6, 65, 'Destination(s):', $getTrip('destination'));
        $drawBox(141.3, 88.6, 64.5, 'Vehicle Plate Number/ Conduction Sticker:', $history->assettag);
        $drawBox(10, 109, 65, 'Phone #:', $getTrip('phone'), 5, 10);
        $drawBox(75.6, 109, 65, 'Purpose(s):', $getTrip('purpose'), 5, 10);
        $drawBox(141.3, 109, 64.4, 'Type of Vehicle:', $vehicleName, 5, 10);
        $drawBox(10, 124.5, 65, $dateLabel, $dateValue, 5, 10);
        $drawBox(75.6, 124.5, 65, $timeLabel, $timeValue, 5, 10);
        $drawBox(141.3, 124.5, 64.4, $mileageLabel, $mileageValue, 5, 10);

        $divisionItems = isset($trip['divisions']) && is_array($trip['divisions']) ? $trip['divisions'] : [];
        $passengerItems = isset($trip['passengers']) && is_array($trip['passengers']) ? $trip['passengers'] : [];
        if (!$divisionItems) {
            foreach (range(1, 10) as $i) {
                $value = $getTrip('division_' . $i);
                if ($value !== '') {
                    $divisionItems[] = $value;
                }
            }
        }
        if (!$passengerItems) {
            foreach (range(1, 10) as $i) {
                $value = $getTrip('passenger_' . $i);
                if ($value !== '') {
                    $passengerItems[] = $value;
                }
            }
        }

        $divisionItems = array_slice($divisionItems, 0, 10);
        $passengerItems = array_slice($passengerItems, 0, 10);
        $lineCount = max(3, count($divisionItems), count($passengerItems));
        $detailHeight = max(16, ($lineCount * 5) + 1);
        $detailBottomY = 145 + $detailHeight;
        $notesY = $detailBottomY + 5;
        $signY = $notesY + 24;
        $custodianY = $signY + 15;

        $pdf->Rect(9.6, 79.6, 196.6, $detailBottomY - 79.6);

        $pdf->SetXY(10, 140);
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(65, 5, 'Division:', 'LTR', 1, 'L');
        $pdf->Cell(65, $detailHeight, '', 'LBR', 1, 'C');
        $pdf->SetFont('Arial', '', 8.5);
        foreach (range(1, $lineCount) as $i) {
            $pdf->SetXY(10, 140 + ($i * 5));
            $pdf->Cell(4, 5, (string) $i, 0, 0, 'L');
            $pdf->Cell(58, 4, $text($divisionItems[$i - 1] ?? ''), 'B', 0, 'C');
        }

        $pdf->SetXY(75.5, 140);
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(65, 5, 'Name of passenger:', 'LTR', 1, 'L');
        $pdf->SetXY(75.5, 140);
        $pdf->Cell(65, 5 + $detailHeight, '', 'LBR', 1, 'C');
        $pdf->SetFont('Arial', '', 8.5);
        foreach (range(1, $lineCount) as $i) {
            $pdf->SetXY(75, 140 + ($i * 5));
            $pdf->Cell(4, 5, (string) $i, 0, 0, 'L');
            $pdf->Cell(58, 4, $text($passengerItems[$i - 1] ?? ''), 'B', 0, 'C');
        }

        $pdf->SetXY(141, 140);
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(64.8, 5, 'Remarks:', 'LTR', 1, 'L');
        $pdf->SetXY(143, 147);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(60.8, 4, $text($getTrip('trip_remarks')), 0, 'L');
        $pdf->SetXY(141, 140);
        $pdf->Cell(64.8, 5 + $detailHeight, '', 'LBR', 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(0, $notesY);
        $pdf->Cell(216, 4, '*If by accident, any damage is done to the vehicle, the borrower will be held liable for the damage and liabilities.', 0, 1, 'C');
        $pdf->SetXY(0, $notesY + 5);
        $pdf->Cell(216, 4, '*This trip ticket is applicable only during non-office hour, for non-emergency use and for all type of vehicles.', 0, 1, 'C');

        $pdf->SetXY(20, 190);
        $pdf->SetXY(20, $signY);
        $pdf->Cell(70, 6, $text($getTrip('borrower_signature_name')), 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, $text($getTrip('supervising_officer')), 0, 1, 'C');
        $yLine = $pdf->GetY() - 1;
        $pdf->Line(20, $yLine, 90, $yLine);
        $pdf->Line(130, $yLine, 200, $yLine);
        $pdf->SetX(20);
        $pdf->Cell(70, 6, $signatureLabel, 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, 'Supervising Officer', 0, 1, 'C');

        $pdf->SetXY(20, $custodianY);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(98, 6, $text($custodian), 0, 1, 'C');
        $pdf->SetX(60);
        $pdf->Cell(98, 6, 'Custodian', 0, 0, 'C');
        $yLine = $pdf->GetY() - 1;
        $pdf->Line(80, $yLine, 140, $yLine);

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filenamePrefix . '_' . $history->id . '.pdf"');
    }

    public function borrowedform($id)
    {
        if (!class_exists('\FPDF')) {
            require_once app_path('fpdf/fpdf.php');
        }

        $history = DB::table('asset_history')
            ->leftJoin('assets', 'asset_history.assetid', '=', 'assets.id')
            ->leftJoin('employees', 'asset_history.employeeid', '=', 'employees.id')
            ->leftJoin('department', 'employees.departmentid', '=', 'department.id')
            ->leftJoin('users', 'users.id', '=', 'asset_history.created_by')
            ->leftJoin('asset_type', 'asset_type.id', '=', 'assets.typeid')
            ->select(
                'asset_history.*',
                'assets.name as assetname',
                'assets.assettag',
                'assets.vehiclecategory',
                'assets.description',
                'employees.fullname as employeename',
                'employees.mobile_number as employeephone',
                'department.name as departmentname',
                'users.fullname as custodianname',
                'asset_type.name as assettype'
            )
            ->where('asset_history.id', $id)
            ->first();

        if (!$history) {
            abort(404);
        }

        $remarks = property_exists($history, 'remarks') ? $history->remarks : '';
        $trip = $this->decodeVehicleTripTicket($remarks);
        $date = $history->date ? date('Y-m-d', strtotime($history->date)) : date('Y-m-d');

        return $this->vehicleTripPdfResponse(
            $history,
            $trip,
            'Assets Vehicle Borrowed Form',
            'Date Borrowed:',
            $trip['date_borrowed'] ?? $date,
            'Time of Departure:',
            $trip['time_of_departure'] ?? '',
            'Departure Millage:',
            $trip['departure_mileage'] ?? '',
            'vehicle_borrowed_form'
        );

        $getTrip = function ($key, $fallback = '') use ($trip) {
            return isset($trip[$key]) && $trip[$key] !== null && $trip[$key] !== '' ? $trip[$key] : $fallback;
        };

        $date = $history->date ? date('Y-m-d', strtotime($history->date)) : date('Y-m-d');
        $vehicleName = $history->assetname ?: ($history->vehiclecategory ?: 'Vehicle');
        $custodian = $getTrip('custodian', $history->employeename ?: '');
        $controlNo = isset($history->control_number) ? $history->control_number : '';

        $text = function ($value) {
            return utf8_decode((string) ($value ?? ''));
        };

        $pdf = new \FPDF('P', 'mm', 'Legal');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(0, 7);
        $pdf->Cell(216, 4, 'Republic of the Philippines', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetX(0);
        $pdf->Cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(0);
        $pdf->Cell(216, 4, 'City of Muntinlupa', 0, 0, 'C');

        if (file_exists(public_path('muntilogo.png'))) {
            $pdf->Image(public_path('muntilogo.png'), 17, 5, 25, 25);
        }
        if (file_exists(public_path('drlogo.png'))) {
            $pdf->Image(public_path('drlogo.png'), 175, 5, 24, 24);
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

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(10, 53);
        $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
        $pdf->Cell(25, 4, $text($date), 'B', 0, 'C');

        $pdf->SetXY(155, 47);
        $pdf->Cell(12, 5, 'CGM-OP-MCDDRM-01F3', 0, 0, 'L');

        $pdf->SetXY(155, 53);
        $pdf->Cell(18, 5, 'Control #:', 0, 0, 'L');
        $pdf->Cell(30, 4, $text($controlNo), 'B', 0, 'C');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(0, 65);
        $pdf->Cell(216, 4, 'Borrowed Form', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetXY(10, 80);
        $pdf->SetFillColor(103, 190, 217);
        $pdf->Cell(196, 8, 'VEHICLE TRIP TICKET.', 1, 1, 'C', true);
        $pdf->SetXY(9.6, 79.6);
        $pdf->Cell(196.6, 82, '', 1, 1);

        $drawBox = function ($x, $y, $w, $label, $value, $labelH = 5, $valueH = 15) use ($pdf, $text) {
            $pdf->SetXY($x, $y);
            $pdf->SetFont('Arial', 'B', 8.5);
            $pdf->Cell($w, $labelH, $label, 'LTR', 1, 'L');
            $pdf->SetX($x);
            $pdf->SetFont('Arial', '', 8.5);
            $pdf->Cell($w, $valueH, $text($value), 'LBR', 1, 'C');
        };

        $drawBox(10, 88.6, 65, 'Name of Driver:', $getTrip('name_of_driver'));
        $drawBox(75.6, 88.6, 65, 'Destination(s):', $getTrip('destination'));
        $drawBox(141.3, 88.6, 64.5, 'Vehicle Plate Number/ Conduction Sticker:', $history->assettag);

        $drawBox(10, 109, 65, 'Phone #:', $getTrip('phone'), 5, 10);
        $drawBox(75.6, 109, 65, 'Purpose(s):', $getTrip('purpose'), 5, 10);
        $drawBox(141.3, 109, 64.4, 'Type of Vehicle:', $vehicleName, 5, 10);

        $drawBox(10, 124.5, 65, 'Date Borrowed:', $getTrip('date_borrowed', $date), 5, 10);
        $drawBox(75.6, 124.5, 65, 'Time of Departure:', $getTrip('time_of_departure'), 5, 10);
        $drawBox(141.3, 124.5, 64.4, 'Departure Millage:', $getTrip('departure_mileage'), 5, 10);

        $pdf->SetXY(10, 140);
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(65, 5, 'Division:', 'LTR', 1, 'L');
        $pdf->Cell(65, 16, '', 'LBR', 1, 'C');
        $pdf->SetFont('Arial', '', 8.5);
        foreach ([1, 2, 3] as $i) {
            $pdf->SetXY(10, 140 + ($i * 5));
            $pdf->Cell(4, 5, (string) $i, 0, 0, 'L');
            $pdf->Cell(58, 4, $text($getTrip('division_' . $i)), 'B', 0, 'C');
        }

        $pdf->SetXY(75.5, 140);
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(65, 5, 'Name of passenger:', 'LTR', 1, 'L');
        $pdf->SetXY(75.5, 140);
        $pdf->Cell(65, 21, '', 'LBR', 1, 'C');
        $pdf->SetFont('Arial', '', 8.5);
        foreach ([1, 2, 3] as $i) {
            $pdf->SetXY(75, 140 + ($i * 5));
            $pdf->Cell(4, 5, (string) $i, 0, 0, 'L');
            $pdf->Cell(58, 4, $text($getTrip('passenger_' . $i)), 'B', 0, 'C');
        }

        $pdf->SetXY(141, 140);
        $pdf->SetFont('Arial', 'B', 8.5);
        $pdf->Cell(64.8, 5, 'Remarks:', 'LTR', 1, 'L');
        $pdf->SetXY(141, 145);
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(64.8, 5, $text($getTrip('trip_remarks')), 'LR', 'C');
        $pdf->SetXY(141, 140);
        $pdf->Cell(64.8, 21, '', 'LBR', 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(0, 166);
        $pdf->Cell(216, 4, '*If by accident, any damage is done to the vehicle, the borrower will be held liable for the damage and liabilities.', 0, 1, 'C');
        $pdf->SetXY(0, 171);
        $pdf->Cell(216, 4, '*This trip ticket is applicable only during non-office hour, for non-emergency use and for all type of vehicles.', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(20, 190);
        $pdf->Cell(70, 6, $text($getTrip('borrower_signature_name')), 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, $text($getTrip('supervising_officer')), 0, 1, 'C');

        $yLine = $pdf->GetY() - 1;
        $pdf->Line(20, $yLine, 90, $yLine);
        $pdf->Line(130, $yLine, 200, $yLine);

        $pdf->SetX(20);
        $pdf->Cell(70, 6, 'Borrower Name over Signature', 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, 'Supervising Officer', 0, 1, 'C');

        $pdf->SetXY(20, 205);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(98, 6, $text($custodian), 0, 1, 'C');
        $pdf->SetX(60);
        $pdf->Cell(98, 6, 'Custodian', 0, 0, 'C');

        $yLine = $pdf->GetY() - 1;
        $pdf->Line(80, $yLine, 140, $yLine);

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="vehicle_borrowed_form_' . $id . '.pdf"');
    }

    public function returnform($id)
    {
        $history = DB::table('asset_history')
            ->leftJoin('assets', 'asset_history.assetid', '=', 'assets.id')
            ->leftJoin('employees', 'asset_history.employeeid', '=', 'employees.id')
            ->leftJoin('department', 'employees.departmentid', '=', 'department.id')
            ->leftJoin('users', 'users.id', '=', 'asset_history.created_by')
            ->leftJoin('asset_type', 'asset_type.id', '=', 'assets.typeid')
            ->select(
                'asset_history.*',
                'assets.name as assetname',
                'assets.assettag',
                'assets.vehiclecategory',
                'assets.description',
                'employees.fullname as employeename',
                'employees.mobile_number as employeephone',
                'department.name as departmentname',
                'users.fullname as custodianname',
                'asset_type.name as assettype'
            )
            ->where('asset_history.id', $id)
            ->first();

        if (!$history) {
            abort(404);
        }

        $returnPayload = json_decode((string) ($history->remarks ?? ''), true);
        $returnTrip = [];
        $savedBorrowedTrip = [];
        if (json_last_error() === JSON_ERROR_NONE && isset($returnPayload['vehicle_return_ticket'])) {
            $returnTrip = $returnPayload['vehicle_return_ticket'];
            $savedBorrowedTrip = $returnPayload['vehicle_trip_ticket'] ?? [];
        }

        $borrowedHistory = $this->getLatestBorrowedVehicleHistory($history->assetid, $history->id);
        $borrowedTrip = $borrowedHistory ? $this->decodeVehicleTripTicket($borrowedHistory->remarks ?? '') : [];

        if ($borrowedHistory && !empty($borrowedHistory->control_number)) {
            $history->control_number = $borrowedHistory->control_number;
        }

        $trip = array_merge($borrowedTrip, $savedBorrowedTrip, $returnTrip);
        $trip['trip_remarks'] = $returnTrip['return_remarks'] ?? '';
        $trip['borrower_signature_name'] = $returnTrip['returning_signature_name'] ?? '';
        $date = $history->date ? date('Y-m-d', strtotime($history->date)) : date('Y-m-d');

        return $this->vehicleTripPdfResponse(
            $history,
            $trip,
            'Assets Vehicle Return Form',
            'Date Return:',
            $returnTrip['date_return'] ?? $date,
            'Time of Arrival:',
            $returnTrip['time_of_arrival'] ?? '',
            'Arrival Millage:',
            $returnTrip['arrival_mileage'] ?? '',
            'vehicle_return_form',
            'Returning Name over Signature'
        );
    }


    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $data = DB::select("select assets.*, supplier.name as supplier, brand.name as brand, asset_type.name as type , location.name as location, ah.status as historystatus
        from assets left join supplier 
        on assets.supplierid = supplier.id
        left join brand 
        on assets.brandid = brand.id
        left join asset_type
        on assets.typeid = asset_type.id
        left join location
        on assets.locationid = location.id
        left join (
            select h1.*
            from asset_history h1
            inner join (
                select assetid, max(id) as max_id
                from asset_history
                group by assetid
            ) h2 on h1.assetid = h2.assetid and h1.id = h2.max_id
        ) ah on ah.assetid = assets.id
        where assets.typeid = 7
        order by assets.created_at desc");
        return Datatables::of($data)
            ->addColumn('pictures', function ($single) {
                return '<img src="' . url('/') . '/upload/assets/' . $single->picture . '" style="width:90px"/>';
            })
            ->addColumn('action', function ($accountsingle) {
                //for checkout 2 button, checkin or checkout depand the record
                //$checkout = '  <a class="dropdown-item" href="#" id="btncheckout" customdata='.$accountsingle->id.'  data-toggle="modal" data-target="#checkout"><i class="fa fa-check"></i> '. trans('lang.checkout').'</a>';

                if ($accountsingle->checkstatus === 2) {

                    $checkout = '<a class="dropdown-item" href="#" id="btncheckin" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#checkin"><i class="fa fa-check"></i> ' . trans('lang.checkin') . '</a>';
                } else {
                    $checkout = '<a class="dropdown-item" href="#" id="btncheckout" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#checkout"><i class="fa fa-check"></i> ' . trans('lang.checkout') . '</a>';
                }

                return '
                <div class="btn-group">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                </button>
                <div class="dropdown-menu actionmenu">
                ' . $checkout . '
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="' . url('/') . '/assetvehiclelist/detail/' . $accountsingle->id . '"id="btndetail" customdata=' . $accountsingle->id . '  ><i class="fa fa-file-text"></i> ' . trans('lang.detail') . '</a>
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

    public function historyassetbyid(Request $request)
    {
        $id            = $request->input('assetid');

        $data = DB::select("select asset_history.*, assets.name as assetname, IFNULL(employees.fullname, '-') as employeename, department.name as office, COALESCE(NULLIF(users.fullname, ''), '-') as fullname

        from asset_history left join assets  
        on asset_history.assetid = assets.id
        left join employees 
        on asset_history.employeeid = employees.id
        left join department
        on employees.departmentid = department.id
        left join users
        on users.id = asset_history.created_by
        where asset_history.assetid = '$id'
        order by asset_history.created_at desc");
        return Datatables::of($data)

            ->addColumn('status', function ($single) {
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
            ->editColumn('remarks', function ($single) {
                return $this->vehicleHistoryRemarks($single->remarks ?? '');
            })
            ->editColumn('employeename', function ($single) {
                return $this->vehicleHistoryName($single->remarks ?? '', $single->employeename ?? '-');
            })
            ->rawColumns(['status', 'date'])
            ->make(true);
    }

    /**
     * get single data 
     * @param integer $id
     * @return object
     */

    public function byid(Request $request)
    {
        $id = $request->input('id');

        $data = DB::table('assets')->select('assets.*', 'assets.name as assetname', 'assets.description as assetdescription', 'assets.created_at as assetcreated_at', 'assets.updated_at as assetupdated_at', 'assets.description as description', 'brand.*', 'brand.name as brand', 'asset_type.name as type', 'supplier.name as supplier', 'location.name as location')
            ->leftJoin('brand', 'brand.id', '=', 'assets.brandid')
            ->leftJoin('asset_type', 'asset_type.id', '=', 'assets.typeid')
            ->leftJoin('supplier', 'supplier.id', '=', 'assets.supplierid')
            ->leftJoin('location', 'location.id', '=', 'assets.locationid')
            ->where('assets.id', $id)
            ->first();

        if ($data) {

            //set status
            if ($data->checkstatus == '2') {
                $status = trans('lang.checkout');
            } elseif ($data->status == '1') {
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
                $status = trans('lang.unserviceable');
            }

            //get date format setting
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
            $res['assetstatus'] = $status;
            $res['assetbarcode'] = '<img src="data:image/png;base64,' . DNS2D::getBarcodePNG($data->assettag, 'QRCODE') . '" alt="barcode" width="70"  />';

            $res['assetimage']  = url('/') . '/upload/assets/' . $data->picture;
        } else {
            $res['success'] = 'failed';
        }
        return response()->json($res);
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
        return response()->json($res);
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
        $vehiclecategory             = $request->input('vehiclecategory');
        // $vehicledesc             = $request->input('vehicledesc');
        $yearmodel             = $request->input('yearmodel');
        $yearacquired             = $request->input('yearacquired');
        $chassis             = $request->input('chassis');
        $engineno             = $request->input('engineno');
        $fueltype             = $request->input('fueltype');
        $transmission             = $request->input('transmission');
        // $quantity           = $request->input( 'quantity' );
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
                    'serial' => "NA",
                    'vehiclecategory' => $vehiclecategory,
                    // 'vehicledesc' => $vehicledesc,
                    'yearmodel' => $yearmodel,
                    'yearacquired' => $yearacquired,
                    'chassis' => $chassis,
                    'engineno' => $engineno,
                    'fueltype' => $fueltype,
                    'transmission' => $transmission,
                    'quantity' => 1,
                    'purchasedate' => $purchasedate,
                    'checkstatus' => 0,
                    'cost' => $cost,
                    'warranty' => $warranty,
                    'status' => $status,
                    'picture' => $picturename,
                    'description' => $description,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                );
                // dd($data);
                $insert     = DB::table('assets')->insert($data);
            } else {
                $data       = array(
                    'name' => $name,
                    'locationid' => $locationid,
                    'supplierid' => $supplierid,
                    'brandid' => $brandid,
                    'typeid' => $typeid,
                    'assettag' => $assettag,
                    'serial' => "NA",
                    'vehiclecategory' => $vehiclecategory,
                    // 'vehicledesc' => $vehicledesc,
                    'yearmodel' => $yearmodel,
                    'yearacquired' => $yearacquired,
                    'chassis' => $chassis,
                    'engineno' => $engineno,
                    'fueltype' => $fueltype,
                    'transmission' => $transmission,
                    'quantity' => 1,
                    'purchasedate' => $purchasedate,
                    'checkstatus' => 0,
                    'cost' => $cost,
                    'warranty' => $warranty,
                    'status' => $status,
                    'picture' => $defaultimage,
                    'description' => $description,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                );

                $insert     = DB::table('assets')->insert($data);
            }

            if ($insert) {
                $res['message'] = 'success';
            } else {
                $res['message'] = 'failed';
            }
        }
        return response()->json($res);
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
        $supplierid         = $request->input('supplierid');
        $locationid         = $request->input('locationid');
        $typeid             = $request->input('typeid');
        $brandid             = $request->input('brandid');
        $assettag           = $request->input('assettag');
        $name               = $request->input('name');
        $vehiclecategory             = $request->input('vehiclecategory');
        // $vehicledesc             = $request->input('vehicledesc');
        $yearmodel             = $request->input('yearmodel');
        $yearacquired             = $request->input('yearacquired');
        $chassis             = $request->input('chassis');
        $engineno             = $request->input('engineno');
        $fueltype             = $request->input('fueltype');
        $transmission             = $request->input('transmission');
        // $quantity           = $request->input( 'quantity' );
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

        $tagcheck = DB::table('assets')
            ->where('assettag', '=', $assettag)
            ->where('id', '!=', $id)
            ->first();
            

        if ($tagcheck) {
            // dd($tagcheck);

            $res['message'] = 'sadness';
        } else {

        



            if ($request->hasFile('picture')) {
                $this->validate($request, ['picture' => 'mimes:jpeg,png,jpg|max:2048'], $message);
                $picturename  = date('mdYHis') . uniqid() . $request->file('picture')->getClientOriginalName();
                $request->file('picture')->move(public_path("/upload/assets"), $picturename);

                $update = DB::table('assets')->where('id', $id)
                    ->update(
                        [
                            'name' => $name,
                            'locationid' => $locationid,
                            'supplierid' => $supplierid,
                            'brandid' => $brandid,
                            'typeid' => $typeid,
                            'assettag' => $assettag,
                            'serial' => "NA",
                            'vehiclecategory' => $vehiclecategory,
                            // 'vehicledesc' => $vehicledesc,
                            'yearmodel' => $yearmodel,
                            'yearacquired' => $yearacquired,
                            'chassis' => $chassis,
                            'engineno' => $engineno,
                            'fueltype' => $fueltype,
                            'transmission' => $transmission,
                            'quantity' => 1,
                            'purchasedate' => $purchasedate,
                            'checkstatus' => 0,
                            'cost' => $cost,
                            'warranty' => $warranty,
                            'status' => $status,
                            'picture' => $picturename,
                            'description' => $description,
                            'created_at' => $created_at,
                            'updated_at' => $updated_at
                        ]
                    );

            } else {
                $update = DB::table('assets')->where('id', $id)
                    ->update(
                        [
                            'name' => $name,
                            'locationid' => $locationid,
                            'supplierid' => $supplierid,
                            'brandid' => $brandid,
                            'typeid' => $typeid,
                            'assettag' => $assettag,
                            'serial' => "NA",
                            'vehiclecategory' => $vehiclecategory,
                            // 'vehicledesc' => $vehicledesc,
                            'yearmodel' => $yearmodel,
                            'yearacquired' => $yearacquired,
                            'chassis' => $chassis,
                            'engineno' => $engineno,
                            'fueltype' => $fueltype,
                            'transmission' => $transmission,
                            'quantity' => 1,
                            'purchasedate' => $purchasedate,
                            'checkstatus' => 0,
                            'cost' => $cost,
                            'warranty' => $warranty,
                            'status' => $status,
                            'description' => $description,
                            'created_at' => $created_at,
                            'updated_at' => $updated_at
                        ]
                    );

            }

            if ($update) {
                $res['message'] = 'success';
            } else {
                $res['message'] = 'failed';
            }
        }
        return response()->json($res);
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
        $vehiclestatus  = $request->input('vehiclestatus');
        $remarks        = $request->input('remarks');
        $controlno      = $request->input('controlno') ?: $this->generateVehicleTripControlNumber();
        $tripTicket     = $this->vehicleTripTicketData($request);
        $status         = '1';
        if ($vehiclestatus === 'returned') {
            $status = '2';
        } elseif ($vehiclestatus === 'serviceable') {
            $status = '3';
        } elseif ($vehiclestatus === 'unserviceable') {
            $status = '4';
        }
        $checkstatus    = ($vehiclestatus === 'borrowed') ? '2' : '0';
        $receiverby     = Auth::id();
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $historyRemarks = $remarks;

        if ($vehiclestatus === 'borrowed') {
            $historyRemarks = json_encode([
                'vehicle_trip_ticket' => $tripTicket,
                'remarks' => $request->input('trip_remarks')
            ]);
        }

        $data = array('assetid' => $assetid, 'status' => $status, 'employeeid' => $employeeid, 'date' => $date, 'created_at' => $created_at, 'updated_at' => $updated_at);

        if ($this->assetHistoryHasColumn('created_by')) {
            $data['created_by'] = $receiverby;
        }
        if ($this->assetHistoryHasColumn('control_number')) {
            $data['control_number'] = $controlno;
        }
        if ($this->assetHistoryHasColumn('remarks')) {
            $data['remarks'] = $historyRemarks;
        }

        $insertId = DB::table('asset_history')->insertGetId($data);

        if ($insertId) {
            $assetUpdate = [
                'checkstatus'         => $checkstatus,
                'status'              => ($vehiclestatus === 'unserviceable') ? '6' : '1',
                'updated_at'          => $updated_at
            ];

            //set status in table asset
            $update = DB::table('assets')->where('id', $assetid)
                ->update($assetUpdate);

            if ($vehiclestatus === 'unserviceable') {
                $this->syncUnserviceableMaintenance($assetid, $employeeid, $date, $updated_at, $remarks);
            } elseif ($vehiclestatus === 'serviceable') {
                $this->closeUnserviceableMaintenance($assetid, $date, $updated_at);
            }

            $res['success'] = 'success';
            $res['id'] = $insertId;
            $res['print_url'] = ($vehiclestatus === 'borrowed') ? url('assetvehiclelist/borrowedform/' . $insertId) : null;
        } else {
            $res['success'] = 'failed';
        }

        return response()->json($res);
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
        $vehiclestatus  = $request->input('vehiclestatus');
        $remarks        = $request->input('remarks');
        $returnTrip     = $this->vehicleReturnTripTicketData($request);
        $status         = '2';
        if ($vehiclestatus === 'borrowed') {
            $status = '1';
        } elseif ($vehiclestatus === 'serviceable') {
            $status = '3';
        } elseif ($vehiclestatus === 'unserviceable') {
            $status = '4';
        }
        $checkstatus    = ($vehiclestatus === 'borrowed') ? '2' : '0';
        $receiverby     = Auth::id();
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $borrowedHistory = $this->getLatestBorrowedVehicleHistory($assetid);
        $controlno = ($borrowedHistory && !empty($borrowedHistory->control_number)) ? $borrowedHistory->control_number : $this->generateVehicleTripControlNumber();
        $borrowedTrip = $borrowedHistory ? $this->decodeVehicleTripTicket($borrowedHistory->remarks ?? '') : [];
        $historyRemarks = $remarks;

        if ($vehiclestatus === 'returned') {
            $historyRemarks = json_encode([
                'vehicle_return_ticket' => $returnTrip,
                'vehicle_trip_ticket' => $borrowedTrip,
                'remarks' => $request->input('return_remarks')
            ]);
        }

        $data = array('assetid' => $assetid, 'status' => $status, 'employeeid' => $employeeid, 'date' => $date, 'created_at' => $created_at, 'updated_at' => $updated_at);

        if ($this->assetHistoryHasColumn('created_by')) {
            $data['created_by'] = $receiverby;
        }
        if ($this->assetHistoryHasColumn('control_number')) {
            $data['control_number'] = $controlno;
        }
        if ($this->assetHistoryHasColumn('remarks')) {
            $data['remarks'] = $historyRemarks;
        }

        $insertId = DB::table('asset_history')->insertGetId($data);

        if ($insertId) {
            $assetUpdate = [
                'checkstatus'         => $checkstatus,
                'status'              => ($vehiclestatus === 'unserviceable') ? '6' : '1',
                'updated_at'          => $updated_at
            ];

            //set status in table asset
            $update = DB::table('assets')->where('id', $assetid)
                ->update($assetUpdate);

            if ($vehiclestatus === 'unserviceable') {
                $this->syncUnserviceableMaintenance($assetid, $employeeid, $date, $updated_at, $remarks);
            } elseif ($vehiclestatus === 'serviceable') {
                $this->closeUnserviceableMaintenance($assetid, $date, $updated_at);
            }
            $res['success'] = 'success';
            $res['id'] = $insertId;
            $res['print_url'] = ($vehiclestatus === 'returned') ? url('assetvehiclelist/returnform/' . $insertId) : null;
        } else {
            $res['success'] = 'failed';
        }

        return response()->json($res);
    }

    /**
     * get all  from database
     * @return object
     */
    public function getrows()
    {
        $data = DB::table('assets')->get();
        if ($data) {
            $res['success'] = true;
            $res['message'] = $data;
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

    public function generateproductcode()
    {
        $lastid = DB::table('assets')->orderBy('id', 'desc')->first();

        if ($lastid) {
            $res['success'] = 'success';
            $res['message'] =  'AST' . date('ymd') . $lastid->id;
        } else {
            $res['message'] =  'AST' . date('ymd') . '1';
        }
        return response($res);
    }
}
