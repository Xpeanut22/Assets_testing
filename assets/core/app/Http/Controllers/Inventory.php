<?php


namespace App\Http\Controllers;

require 'vendor/autoload.php';


use Illuminate\Http\Request;
use App\AssetModel;
use Illuminate\Support\Facades\File;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;
use Carbon\Carbon;
use Milon\Barcode\DNS2D;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;




class Inventory extends Controller
{
    use TraitSettings;

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
        return view('inventory.index');
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
        // Fetch data from assets table with related data
        $assets = DB::select("
        SELECT assets.*, supplier.name as supplier, brand.name as brand, asset_type.name as type, location.name as location
        FROM assets
        LEFT JOIN supplier ON assets.supplierid = supplier.id
        LEFT JOIN brand ON assets.brandid = brand.id
        LEFT JOIN asset_type ON assets.typeid = asset_type.id
        LEFT JOIN location ON assets.locationid = location.id
        ORDER BY assets.created_at desc
    ");

        // Fetch data from components table
        $components = DB::select("
        SELECT component.*
        FROM component
    ");

        // Merge both results
        $data = array_merge($assets, $components);

        return Datatables::of($data)
            ->addColumn('pictures', function ($single) {
                if (isset($single->picture)) {
                    return '<img src="' . url('/') . '/upload/assets/' . $single->picture . '" style="width:90px"/>';
                }
                return '';
            })
            ->rawColumns(['pictures'])
            ->make(true);
    }



    /**
     * get single data by assets id for history
     * @param integer $id
     * @return object
     */

    public function historyassetbyid(Request $request)
    {
        $id = $request->input('assetid');

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
                if ($single->status == '1') {
                    $status = trans('lang.checkout');
                }
                if ($single->status == '2') {
                    $status = trans('lang.checkin');
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
            ->leftJoin('asset_history', 'asset_history.assetid', '=', 'assets.id')
            ->where('assets.id', $id)
            ->first();

        if ($data) {

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

            $res['assetimage'] = url('/') . '/upload/assets/' . $data->picture;
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
        $supplierid = $request->input('supplierid');
        $locationid = $request->input('locationid');
        $typeid = $request->input('typeid');
        $brandid = $request->input('brandid');
        $assettag = $request->input('assettag');
        $name = $request->input('name');
        $serial = $request->input('serial');
        $quantity = $request->input('quantity');
        $purchasedate = $request->input('purchasedate');
        $cost = $request->input('cost');
        $warranty = $request->input('warranty');
        $status = $request->input('status');
        $checkstatus = 0;
        $picture = $request->file('picture');
        $description = $request->input('description');
        $defaultimage = 'pic.png';
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $message = ['picture.mimes' => trans('lang.upload_error')];

        $emailcheck = DB::table('assets')
            ->where('assettag', '=', $assettag)
            ->first();

        if ($emailcheck) {
            $res['message'] = 'exist';
        } else {

            if ($request->hasFile('picture')) {
                $this->validate($request, ['picture' => 'mimes:jpeg,png,jpg|max:2048'], $message);
                $picturename = date('mdYHis') . uniqid() . $request->file('picture')->getClientOriginalName();
                $request->file('picture')->move(public_path("/upload/assets"), $picturename);
                $data = array(
                    'name' => $name,
                    'locationid' => $locationid,
                    'supplierid' => $supplierid,
                    'brandid' => $brandid,
                    'typeid' => $typeid,
                    'assettag' => $assettag,
                    'serial' => $serial,
                    'quantity' => $quantity,
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
                $insert = DB::table('assets')->insert($data);
            } else {
                $data = array(
                    'name' => $name,
                    'locationid' => $locationid,
                    'supplierid' => $supplierid,
                    'typeid' => $typeid,
                    'brandid' => $brandid,
                    'assettag' => $assettag,
                    'serial' => $serial,
                    'quantity' => $quantity,
                    'purchasedate' => $purchasedate,
                    'cost' => $cost,
                    'checkstatus' => 0,
                    'warranty' => $warranty,
                    'status' => $status,
                    'picture' => $defaultimage,
                    'description' => $description,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                );

                $insert = DB::table('assets')->insert($data);
            }

            if ($insert) {
                $res['message'] = 'success';
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
        $id = $request->input('id');
        $locationid = $request->input('locationid');
        $supplierid = $request->input('supplierid');
        $typeid = $request->input('typeid');
        $brandid = $request->input('brandid');
        $assettag = $request->input('assettag');
        $name = $request->input('name');
        $serial = $request->input('serial');
        $quantity = $request->input('quantity');
        $purchasedate = $request->input('purchasedate');
        $cost = $request->input('cost');
        $warranty = $request->input('warranty');
        $status = $request->input('status');
        $picture = $request->file('picture');
        $description = $request->input('description');
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $message = ['picture.mimes' => trans('lang.upload_error')];

        $tagcheck = DB::table('assets')
            ->where('assettag', '=', $assettag)
            ->where('id', '!=', $id)
            ->first();

        if ($tagcheck) {
            $res['message'] = 'exist';
        } else {

            if ($request->hasFile('picture')) {
                $this->validate($request, ['picture' => 'mimes:jpeg,png,jpg|max:2048'], $message);
                $picturename = date('mdYHis') . uniqid() . $request->file('picture')->getClientOriginalName();
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
                            'serial' => $serial,
                            'quantity' => $quantity,
                            'purchasedate' => $purchasedate,
                            'cost' => $cost,
                            'warranty' => $warranty,
                            'status' => $status,
                            'description' => $description,
                            'picture' => $picturename,
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
                            'serial' => $serial,
                            'quantity' => $quantity,
                            'purchasedate' => $purchasedate,
                            'cost' => $cost,
                            'warranty' => $warranty,
                            'status' => $status,
                            'description' => $description,
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
        $assetid = $request->input('assetid');
        $employeeid = $request->input('employeeid');
        $date = $request->input('checkoutdate');
        $status = '1'; //checkout = 1
        $checkstatus = '2';
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $created_by = Auth::id();
        $remarks = $request->input('remarks1');
        $data = array('assetid' => $assetid, 'status' => $status, 'employeeid' => $employeeid, 'date' => $date, 'created_at' => $created_at, 'updated_at' => $updated_at, 'created_by' => $created_by, 'remarks' => $remarks);
        $insert = DB::table('asset_history')->insert($data);

        if ($insert) {

            //set status in table asset
            $update = DB::table('assets')->where('id', $assetid)
                ->update(
                    [
                        'checkstatus' => $checkstatus,
                        'updated_at' => $updated_at,

                    ]
                );

            $res['success'] = 'success';
        } else {
            $res['success'] = 'failed';
        }

        return response($res);
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
        $assetid = $request->input('assetid');
        $employeeid = $request->input('employeeid1');
        $date = $request->input('checkindate');
        $status = '2'; //checkout = 1
        $checkstatus = '0';
        $created_at = date("Y-m-d H:i:s");
        $updated_at = date("Y-m-d H:i:s");
        $created_by = Auth::id();
        $remarks = $request->input('remarks');
        $data = array('assetid' => $assetid, 'status' => $status, 'employeeid' => $employeeid, 'date' => $date, 'created_at' => $created_at, 'updated_at' => $updated_at, 'created_by' => $created_by, 'remarks' => $remarks);
        $insert = DB::table('asset_history')->insert($data);

        if ($insert) {
            //set status in table asset
            $update = DB::table('assets')->where('id', $assetid)
                ->update(
                    [
                        'checkstatus' => $checkstatus,
                        'updated_at' => $updated_at,
                    ]
                );
            $res['success'] = 'success';
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
        $getfilename = DB::table('assets')
            ->where('id', '=', $id)
            ->first();



        $delete = DB::table('assets')->where('id', $id)->delete();
        $notdefaultimage = 'pic.png';
        $filename = $getfilename->picture;

        if ($filename != $notdefaultimage) {
            $deleteimage = File::delete('upload/assets/' . $getfilename->picture);
        }

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
            $res['message'] = 'AST' . date('ymd') . $lastid->id;
        } else {
            $res['message'] = 'AST' . date('ymd') . '1';
        }
        return response($res);
    }


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
        $isValid = DB::select("select a.*,b.*
        from assets as a left join asset_history as b
        on b.assetid = a.id
        where a.assettag = '$input'");

        return response()->json(['isValid' => $isValid]);
    }

    public function logScan(Request $request)
    {
        // Fetch the barcode data from the query parameter
        $barcode_data = $request->input('barcode_data');
        // $shift = $request->input('inventoryshift');
        // $created_by = $request->input('receivedby');
        // dd($created_by);

        $validator = Validator::make($request->all(), [
            'barcode_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'No data received or invalid data'], 400);
        }

        try {
            $assets = DB::select("
            SELECT assets.*, assets.assettag as serial, supplier.name as supplier, brand.name as brand, asset_type.name as type, location.name as location
            FROM assets
            LEFT JOIN supplier ON assets.supplierid = supplier.id
            LEFT JOIN brand ON assets.brandid = brand.id
            LEFT JOIN asset_type ON assets.typeid = asset_type.id
            LEFT JOIN location ON assets.locationid = location.id
            WHERE assets.assettag = ?
        ", [$barcode_data]);

            // Fetch data from components table
            $components = DB::select("
            SELECT component.*, asset_type.name as type
            FROM component
            LEFT JOIN asset_type ON component.typeid = asset_type.id
            WHERE component.serial = ?
        ", [$barcode_data]);

            $data = array_merge($assets, $components);

            if (empty($data)) {
                $res['success'] = 'failed';
                return response($res);
            }
            // Insert the barcode data into the database
            // DB::table('inventory')->insert([
            //     'item' => $barcode_data,
            //     'quantity' => 1,
            //     'shift' => $shift,
            //     'created_by' => $created_by,
            //     'created_at' => now(),
            // ]);

            return response()->json(['success' => 'Data logged successfully', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error logging data: ' . $e->getMessage()], 500);
        }
    }

    public function saveItems(Request $request)
    {
        $data = $request->json()->all();

        // Log::info('Received data:', $data);

        $items = $request->input('items');
        $shift = $request->input('shift');
        $created_by = $request->input('created_by');
        // $typeid = $request->input('typeid');


        if (!isset($data['items']) || !is_array($data['items'])) {
            return response()->json(['success' => false, 'message' => 'Items data is invalid']);
        }



        try {
            foreach ($data['items'] as $barcode_data) {

                if (!is_array($barcode_data)) {
                    return response()->json(['success' => false, 'message' => 'Invalid item data']);
                }

                $serial = $barcode_data['serial'] ?? null;
                // $quantity = $barcode_data['serial'] ?? null;
                $typeid = $barcode_data['typeid'] ?? null;
                $name = $barcode_data['name'] ?? null;



                DB::table('inventory')->insert([
                    'item' => $serial,
                    'equipment_name' => $name,
                    'quantity' => 1,
                    'shift' => $shift,
                    'created_by' => $created_by,
                    'created_at' => now(),
                    'type' => $typeid
                ]);
            }

            return response()->json(['success' => 'Items saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error saving items: ' . $e->getMessage()], 500);
        }
    }

    public function export($from, $to)
    {

        $data = DB::table('inventory')->whereBetween('created_at', [$from, $to])->get();
        return response()->json($data);
    }

    // public function exportexcel(Request $request)
    // {
    //     $from = $request->input('datefrom');
    //     $to = $request->input('dateto');


    //     // $fromDate = substr($from, 0, 10);
    //     // $toDate = substr($to, 0, 10);

    //     // Fetch the data
    //     // $data = DB::table('inventory')->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate])->get();
    //     // $data = DB::table('inventory')->whereBetween('created_at', [$from, $to])->get();
    //     $data = DB::table('inventory')
    //     ->leftJoin('receiver', 'inventory.created_by', '=', 'receiver.id')
    //     ->leftJoin('assets', 'inventory.item', '=', 'assets.assettag')
    //     ->leftJoin('component', 'inventory.item', '=', 'component.serial')
    //     ->leftJoin('asset_type', 'inventory.type', '=', 'asset_type.id')
    //     ->whereBetween('inventory.created_at', [$from, $to])
    //     ->select(
    //         'inventory.item',
    //         DB::raw('SUM(inventory.quantity) as total_quantity'),
    //         DB::raw('COALESCE(component.name, assets.name) as item_name'),
    //         'inventory.created_at',
    //         'receiver.fullname',
    //         'asset_type.description'
    //     )
    //     ->groupBy(DB::raw('COALESCE(component.name, assets.name)'), 'asset_type.description')
    //     ->get();
    // dd($data);


    //     // Create a new Spreadsheet object
    //     $spreadsheet = new Spreadsheet();

    //     // $templatePath = storage_path('app/template.xlsx'); // Adjust the path to your template file
    //     // $spreadsheet = IOFactory::load($templatePath);

    //     $sheet = $spreadsheet->getActiveSheet();

    //     // Set the headers for the columns
    //     // $sheet->setCellValue('A1', 'ID');
    //     $sheet->setCellValue('B1', 'Item Name');
    //     $sheet->setCellValue('C1', 'Actual Count');
    //     $sheet->setCellValue('D1', 'Checked By');
    //     $sheet->setCellValue('E1', 'Created At');
    //     $sheet->setCellValue('F1', 'Type');


    //     // Populate the data
    //     $row = 2;
    //     foreach ($data as $item) {
    //         // $sheet->setCellValue('A' . $row, $item->id);
    //         $sheet->setCellValue('B' . $row, $item->item_name);
    //         $sheet->setCellValue('C' . $row, $item->total_quantity);
    //         $sheet->setCellValue('D' . $row, $item->fullname);
    //         $sheet->setCellValue('E' . $row, $item->created_at);
    //         $sheet->setCellValue('F' . $row, $item->description);

    //         $row++;
    //     }

    //     // Set the filename
    //     $fileName = 'inventory_' . $from . '_to_' . $to . '.xlsx';

    //     // Redirect output to a client’s web browser (Xlsx)
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment;filename="' . $fileName . '"');
    //     header('Cache-Control: max-age=0');

    //     // Write the file
    //     $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    //     $writer->save('php://output');
    //     exit;
    // }


    // for loop dates function
    // public function exportexcel(Request $request)
    // {
    //     // Load the existing Excel template
    //     $templatePath = storage_path('app/inventorytemplate.xlsx'); // Adjust the path to your template file
    //     $spreadsheet = IOFactory::load($templatePath);

    //     // Select the active sheet
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // Example data fetched from the database (you should replace this with your actual database query)
    //     $from = $request->input('datefrom');
    //     $to = $request->input('dateto');

    //     $data = DB::table('inventory')
    //         ->leftJoin('receiver', 'inventory.created_by', '=', 'receiver.id')
    //         ->leftJoin('assets', 'inventory.item', '=', 'assets.assettag')
    //         ->leftJoin('component', 'inventory.item', '=', 'component.serial')
    //         ->leftJoin('asset_type', 'inventory.type', '=', 'asset_type.id')
    //         ->whereBetween('inventory.created_at', [$from, $to])
    //         ->select(
    //             'inventory.item',
    //             DB::raw('SUM(inventory.quantity) as total_quantity'),
    //             DB::raw('COALESCE(component.name, assets.name) as item_name'),
    //             'inventory.created_at',
    //             'receiver.fullname',
    //             'asset_type.description'
    //         )
    //         ->groupBy(DB::raw('COALESCE(component.name, assets.name)'), 'asset_type.description')
    //         ->get();

    //     // Start row for the data
    //     $startRow = 5;
    //     $startColumn = 'C'; // Start column where data entry begins

    //     // Loop through each data item and populate the sheet
    //     foreach ($data as $item) {
    //         $currentColumn = $startColumn;
    //         $day = date('n/j/y', strtotime($item->created_at));

    //         for ($i = 0; $i < 5; $i++) { // Assuming you need to repeat this 5 times (change this as necessary)
    //             // Merge cells for every 3 columns
    //             $mergeRange = $currentColumn . $startRow . ':' . $this->incrementColumn($currentColumn, 2) . $startRow;
    //             $sheet->mergeCells($mergeRange);

    //             // Set values for merged cells
    //             $sheet->setCellValue($currentColumn . $startRow, "Value $i");
    //             $sheet->setCellValue($currentColumn . '3', $day); // Set the date in the header

    //             // Move to the next set of columns
    //             $currentColumn = $this->incrementColumn($currentColumn, 3);
    //         }

    //         // Move to the next row
    //         $startRow++;
    //     }

    //     // Set the filename
    //     $fileName = 'inventory_' . date('Y-m-d') . '.xlsx';

    //     // Redirect output to a client’s web browser (Xlsx)
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment;filename="' . $fileName . '"');
    //     header('Cache-Control: max-age=0');

    //     // Write the file
    //     $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    //     $writer->save('php://output');
    //     exit;
    // }

    // private function incrementColumn($column, $steps)
    // {
    //     for ($i = 0; $i < $steps; $i++) {
    //         $column++;
    //     }
    //     return $column;
    // }


    public function exportexcel(Request $request)
    {
        $from = $request->input('datefrom_display') ?: $request->input('datefrom');
        $to = $request->input('dateto_display') ?: $request->input('dateto');
        $parseInventoryDate = function ($value, $endOfDay = false) {
            $dateOnly = trim(explode(',', (string) $value)[0]);
            $date = \DateTime::createFromFormat('Y-m-d\TH:i', (string) $value)
                ?: \DateTime::createFromFormat('Y-m-d\TH:i:s', (string) $value)
                ?: \DateTime::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', (string) $value))
                ?: \DateTime::createFromFormat('Y-m-d H:i', str_replace('T', ' ', (string) $value))
                ?: \DateTime::createFromFormat('m/d/Y', $dateOnly);

            if (!$date) {
                $date = new \DateTime(str_replace('T', ' ', (string) $value));
            }

            $date->setTime($endOfDay ? 23 : 0, $endOfDay ? 59 : 0, $endOfDay ? 59 : 0);

            return $date;
        };
        $displayInventoryDate = function ($value) {
            $dateOnly = trim(explode(',', (string) $value)[0]);
            $date = \DateTime::createFromFormat('Y-m-d\TH:i', (string) $value)
                ?: \DateTime::createFromFormat('Y-m-d\TH:i:s', (string) $value)
                ?: \DateTime::createFromFormat('Y-m-d H:i:s', str_replace('T', ' ', (string) $value))
                ?: \DateTime::createFromFormat('Y-m-d H:i', str_replace('T', ' ', (string) $value))
                ?: \DateTime::createFromFormat('m/d/Y', $dateOnly);

            if ($date) {
                return $date->format('m/d/Y');
            }

            return $dateOnly !== '' ? $dateOnly : trim((string) $value);
        };

        $fromDateObject = $parseInventoryDate($from);
        $toDateObject = $parseInventoryDate($to, true);
        $fromDate = $fromDateObject->format('Y-m-d H:i:s');
        $toDate = $toDateObject->format('Y-m-d H:i:s');

        $masterItems = DB::table(DB::raw("(
                SELECT assets.name as item_name,
                    SUM(CAST(assets.quantity AS DECIMAL(20,2))) as allQuantity,
                    MAX(assets_units.unit) as unit,
                    CASE
                        WHEN MAX(assets.status) = 1 THEN '" . trans('lang.readytodeploy') . "'
                        WHEN MAX(assets.status) = 2 THEN '" . trans('lang.pending') . "'
                        WHEN MAX(assets.status) = 3 THEN '" . trans('lang.archived') . "'
                        WHEN MAX(assets.status) = 4 THEN '" . trans('lang.broken') . "'
                        WHEN MAX(assets.status) = 5 THEN '" . trans('lang.lost') . "'
                        WHEN MAX(assets.status) = 6 THEN '" . trans('lang.outofrepair') . "'
                        ELSE '-'
                    END as status_label
                FROM assets
                LEFT JOIN units as assets_units ON assets_units.id = assets.unit
                WHERE assets.typeid != 7
                GROUP BY assets.name
                UNION ALL
                SELECT component.name as item_name,
                    SUM(CAST(component.quantity AS DECIMAL(20,2))) as allQuantity,
                    MAX(component_units.unit) as unit,
                    CASE
                        WHEN MAX(component.checkstatus) = 0 THEN 'Available'
                        WHEN MAX(component.checkstatus) = 2 THEN 'Issued'
                        ELSE '-'
                    END as status_label
                FROM component
                LEFT JOIN units as component_units ON component_units.id = component.unit
                GROUP BY component.name
            ) as inventory_items"))
            ->select(
                'item_name',
                DB::raw('SUM(allQuantity) as allQuantity'),
                DB::raw("COALESCE(MAX(unit), '') as unit"),
                DB::raw("COALESCE(MAX(status_label), '-') as status_label")
            )
            ->whereNotNull('item_name')
            ->groupBy('item_name')
            ->orderBy('item_name')
            ->get();

        // Fetch actual count logs within the selected date range.
        $data = DB::table('inventory')
            ->leftJoin('receiver', 'inventory.created_by', '=', 'receiver.id')
            ->leftJoin('assets', 'inventory.item', '=', 'assets.assettag')
            ->leftJoin('component', 'inventory.item', '=', 'component.serial')
            ->leftJoin('asset_type', 'inventory.type', '=', 'asset_type.id')
            ->leftJoin('units as assets_units', 'assets_units.id', '=', 'assets.unit')
            ->leftJoin('units as component_units', 'component_units.id', '=', 'component.unit')


            ->whereBetween('inventory.created_at', [$fromDate, $toDate])
            ->select(
                'inventory.item',
                DB::raw('SUM(inventory.quantity) as total_quantity'),
                DB::raw('COALESCE(component.name, assets.name, inventory.equipment_name, inventory.item) as item_name'),
                'inventory.created_at',
                'receiver.fullname',
                'asset_type.description',
                DB::raw('COALESCE(assets.quantity, component.quantity) as allQuantity'),
                DB::raw('COALESCE(assets_units.unit, component_units.unit) as unit'),
                'assets.status as asset_status',
                'component.checkstatus as component_checkstatus'
            )
            ->groupBy(DB::raw('COALESCE(component.name, assets.name, inventory.equipment_name, inventory.item)'), 'inventory.created_at', 'receiver.fullname', 'asset_type.description', 'inventory.item', 'assets.quantity', 'component.quantity', 'assets_units.unit', 'component_units.unit', 'assets.status', 'component.checkstatus')
            ->get();
        // Determine unique dates with content
        $datesWithContent = $data->map(function ($item) {
            return (new \DateTime($item->created_at))->format('Y-m-d');
        })->unique()->sort()->values()->toArray(); // Convert to array

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventory Report');

        $rowTitle = 1;
        $rowDateRange = 2;
        $rowDateHeaders = 3;
        $rowHeaders = 4;
        $rowStart = 5;
        $shiftTimes = ['6-2', '2-10', '10-6'];

        $currentDate = clone $fromDateObject;
        $endDate = clone $toDateObject;
        $fromDateLabel = $displayInventoryDate($from);
        $toDateLabel = $displayInventoryDate($to);
        if ($fromDateLabel === '') {
            $fromDateLabel = $fromDateObject->format('m/d/Y');
        }
        if ($toDateLabel === '') {
            $toDateLabel = $toDateObject->format('m/d/Y');
        }
        $reportDateLabel = 'from ' . $fromDateLabel . ' to ' . $toDateLabel;

        $sheet->setCellValue('A1', 'DISASTER TOOLS AND CONSUMABLES EQUIPMENT');
        $sheet->setCellValue('A2', 'Inventory Report: ' . $reportDateLabel);

        $sheet->setCellValue('A4', 'No.');
        $sheet->setCellValue('B4', 'Quantity');
        $sheet->setCellValue('C4', 'Units');
        $sheet->setCellValue('D4', 'Items');
        $sheet->setCellValue('E4', 'Actual Count');
        $sheet->setCellValue('F4', 'Status');

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(45);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(20);

        $dateColumns = [];
        $columnIndex = 7;

        while ($currentDate <= $endDate) {
            $shiftDate = $currentDate->format('Y-m-d');
            if (in_array($shiftDate, $datesWithContent)) {
                $columnStart = $columnIndex;
                $sheet->setCellValueByColumnAndRow($columnStart, $rowDateHeaders, $shiftDate);
                $sheet->mergeCellsByColumnAndRow($columnStart, $rowDateHeaders, $columnStart + 2, $rowDateHeaders);
                foreach ($shiftTimes as $shiftIndex => $shift) {
                    $shiftColumn = $columnStart + $shiftIndex;
                    $sheet->setCellValueByColumnAndRow($shiftColumn, $rowHeaders, $shift);
                    $sheet->getColumnDimensionByColumn($shiftColumn)->setWidth(10);
                }
                $dateColumns[$shiftDate] = [
                    'start' => $columnStart,
                    'end' => $columnStart + 2,
                ];
                $columnIndex += 3;
            }

            $currentDate->modify('+1 day');
        }

        $remarksColumn = $columnIndex;
        $sheet->setCellValueByColumnAndRow($remarksColumn, $rowHeaders, 'Remarks');
        $sheet->getColumnDimensionByColumn($remarksColumn)->setWidth(30);

        // Process data
        $itemQuantities = [];
        $itemDetails = [];

        foreach ($masterItems as $item) {
            $itemName = $item->item_name;
            $itemQuantities[$itemName] = [];
            $itemDetails[$itemName] = [
                'unit' => $item->unit,
                'allQuantity' => $item->allQuantity,
                'status' => $item->status_label ?? '-'
            ];
        }

        foreach ($data as $row) {
            $itemName = $row->item_name;
            $date = (new \DateTime($row->created_at))->format('Y-m-d');
            $quantity = $row->total_quantity;

            if (!isset($itemQuantities[$itemName])) {
                $itemQuantities[$itemName] = [];
            }
            if (!isset($itemDetails[$itemName])) {
                $itemDetails[$itemName] = [
                    'unit' => $row->unit,
                    'allQuantity' => $row->allQuantity,
                    'status' => '-'
                ];
            }

            if (!empty($row->asset_status)) {
                $statusMap = [
                    '1' => trans('lang.readytodeploy'),
                    '2' => trans('lang.pending'),
                    '3' => trans('lang.archived'),
                    '4' => trans('lang.broken'),
                    '5' => trans('lang.lost'),
                    '6' => trans('lang.outofrepair'),
                ];

                $itemDetails[$itemName]['status'] = $statusMap[(string) $row->asset_status] ?? $itemDetails[$itemName]['status'];
            } elseif ($row->component_checkstatus !== null) {
                $itemDetails[$itemName]['status'] = ((string) $row->component_checkstatus === '0') ? 'Available' : 'Issued';
            }

            if (!isset($itemQuantities[$itemName][$date])) {
                $itemQuantities[$itemName][$date] = array_fill(0, count($shiftTimes), 0);
            }

            $shiftIndex = $this->getShiftIndex($row->created_at);

            $itemQuantities[$itemName][$date][$shiftIndex] += $quantity;
        }

        $currentRow = $rowStart;
        foreach ($itemQuantities as $itemName => $dates) {
            $sheet->setCellValueByColumnAndRow(1, $currentRow, $currentRow - 4);
            $sheet->setCellValueByColumnAndRow(2, $currentRow, $itemDetails[$itemName]['allQuantity'] ?? '');
            $sheet->setCellValueByColumnAndRow(3, $currentRow, $itemDetails[$itemName]['unit'] ?? '');
            $sheet->setCellValueByColumnAndRow(4, $currentRow, $itemName);
            $sheet->setCellValueByColumnAndRow(6, $currentRow, $itemDetails[$itemName]['status'] ?? '-');

            foreach ($dateColumns as $date => $columns) {
                $shiftQuantities = isset($dates[$date]) ? $dates[$date] : array_fill(0, count($shiftTimes), 0);

                foreach ($shiftQuantities as $shiftIndex => $quantity) {
                    $sheet->setCellValueByColumnAndRow($columns['start'] + $shiftIndex, $currentRow, $quantity ?: '');
                }
            }

            $currentRow++;
        }

        $lastRow = max($currentRow - 1, $rowHeaders);
        $lastColumn = $remarksColumn;
        $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumn);

        $sheet->mergeCellsByColumnAndRow(1, $rowTitle, $lastColumn, $rowTitle);
        $sheet->mergeCellsByColumnAndRow(1, $rowDateRange, $lastColumn, $rowDateRange);

        $sheet->getStyleByColumnAndRow(1, $rowTitle, $lastColumn, $rowTitle)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F79646'],
            ],
        ]);
        $sheet->getStyleByColumnAndRow(1, $rowDateRange, $lastColumn, $rowDateRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyleByColumnAndRow(1, $rowDateHeaders, $lastColumn, $rowHeaders)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9EAD3'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getStyleByColumnAndRow(1, $rowHeaders, $lastColumn, $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getStyleByColumnAndRow(1, $rowStart, 3, $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyleByColumnAndRow(5, $rowStart, $lastColumn, $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyleByColumnAndRow(4, $rowStart, 4, $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyleByColumnAndRow($remarksColumn, $rowStart, $remarksColumn, $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        for ($row = $rowStart; $row <= $lastRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        $signatoryRow = $lastRow + 4;
        $checkedStartColumn = 1;
        $checkedEndColumn = 3;
        $notedStartColumn = 4;
        $notedEndColumn = 6;

        $sheet->mergeCellsByColumnAndRow($checkedStartColumn, $signatoryRow, $checkedEndColumn, $signatoryRow);
        $sheet->mergeCellsByColumnAndRow($checkedStartColumn, $signatoryRow + 1, $checkedEndColumn, $signatoryRow + 1);
        $sheet->mergeCellsByColumnAndRow($notedStartColumn, $signatoryRow, $notedEndColumn, $signatoryRow);
        $sheet->mergeCellsByColumnAndRow($notedStartColumn, $signatoryRow + 1, $notedEndColumn, $signatoryRow + 1);

        $sheet->setCellValueByColumnAndRow($checkedStartColumn, $signatoryRow, 'Checked by: DOMINIC A. NAVARRO');
        $sheet->setCellValueByColumnAndRow($checkedStartColumn, $signatoryRow + 1, 'Section Head - Logistics');
        $sheet->setCellValueByColumnAndRow($notedStartColumn, $signatoryRow, 'Noted by: LEONARDO S. SESE JR.');
        $sheet->setCellValueByColumnAndRow($notedStartColumn, $signatoryRow + 1, 'Division Head - Operation and Warning');

        $sheet->getStyleByColumnAndRow($checkedStartColumn, $signatoryRow, $notedEndColumn, $signatoryRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyleByColumnAndRow($checkedStartColumn, $signatoryRow + 1, $notedEndColumn, $signatoryRow + 1)->applyFromArray([
            'font' => [
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(22);
        $sheet->getRowDimension(4)->setRowHeight(24);

        $sheet->setAutoFilter("A4:{$lastColumnLetter}{$lastRow}");
        $sheet->freezePane('A5');
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.25);
        $sheet->getPageMargins()->setLeft(0.25);
        $sheet->getPageMargins()->setBottom(0.5);

        // Save the file
        $filename = 'inventory_report_' . date('Y-m-d_H-i-s') . '.xlsx';
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(storage_path('app/' . $filename));

        return response()->download(storage_path('app/' . $filename))->deleteFileAfterSend(true);
    }

    // Helper function to determine shift index based on timestamp
    private function getShiftIndex($timestamp)
    {
        $time = (new \DateTime($timestamp))->format('H:i:s');
        if ($time >= '06:00:00' && $time < '14:00:00') {
            return 0; // 6-2 shift
        } elseif ($time >= '14:00:00' && $time < '22:00:00') {
            return 1; // 2-10 shift
        } else {
            return 2; // 10-6 shift
        }
    }







}
