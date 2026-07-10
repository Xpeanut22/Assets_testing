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
        $from = $request->input('datefrom');
        $to = $request->input('dateto');
        $fromDate = str_replace('T', ' ', $from);
        $toDate = str_replace('T', ' ', $to);

        // Fetch only actual count logs within the selected date range.
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
                DB::raw('COALESCE(assets_units.unit, component_units.unit) as unit')
            )
            ->groupBy(DB::raw('COALESCE(component.name, assets.name, inventory.equipment_name, inventory.item)'), 'inventory.created_at', 'receiver.fullname', 'asset_type.description', 'inventory.item', 'assets.quantity', 'component.quantity', 'assets_units.unit', 'component_units.unit')
            ->get();
        // Determine unique dates with content
        $datesWithContent = $data->map(function ($item) {
            return (new \DateTime($item->created_at))->format('Y-m-d');
        })->unique()->sort()->values()->toArray(); // Convert to array

        // Load the existing Excel template
        $templatePath = storage_path('app/inventorytemplate.xlsx'); // Adjust the path to your template file
        $spreadsheet = IOFactory::load($templatePath);

        // Select the active sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Set row positions
        $rowMonth = 2;
        $rowTitles = 3; // Row for date headers
        $rowShifts = 4; // Row for shift headers
        $shiftTimes = ['6-2', '2-10', '10-6'];

        $currentDate = new \DateTime($fromDate);
        $endDate = new \DateTime($toDate);

        $columnIndex = 5; // Starting column (E)

        // Arrays to track headers
        $processedDates = [];
        $dateColumns = [];
        $monthStartColumn = null;
        $currentMonth = '';

        // Generate unique dates and shift columns
        while ($currentDate <= $endDate) {
            $shiftDate = $currentDate->format('Y-m-d');
            $monthYear = $currentDate->format('F Y');


            if ($currentMonth !== $monthYear) {

                if ($monthStartColumn !== null) {
                    // Merge cells for the previous month header
                    $sheet->mergeCellsByColumnAndRow($monthStartColumn, $rowMonth, $columnIndex - 0, $rowMonth);
                    $sheet->setCellValueByColumnAndRow($monthStartColumn, $rowMonth, $currentMonth);


                    // Apply styles to the month header
                    $sheet->getStyleByColumnAndRow($monthStartColumn, $rowMonth, $columnIndex - 0, $rowMonth)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 14,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'D9EAD3'], // Light green background color
                        ],
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THICK,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    // Add the "Actual Count" and "Product" columns
                    $sheet->setCellValueByColumnAndRow($monthStartColumn, $rowTitles, 'Actual Count');
                    $sheet->mergeCellsByColumnAndRow($monthStartColumn, $rowTitles, $monthStartColumn, $rowShifts - 0);
                    $sheet->getStyleByColumnAndRow($monthStartColumn, $rowTitles, $monthStartColumn, $rowShifts - 0)->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'A4C2F4'], // Light blue background color
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    $sheet->setCellValueByColumnAndRow($columnIndex, $rowTitles, 'Remarks');
                    $sheet->mergeCellsByColumnAndRow($columnIndex, $rowTitles, $columnIndex, $rowShifts - 0);
                    $sheet->getStyleByColumnAndRow($columnIndex, $rowTitles, $columnIndex, $rowShifts - 0)->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFD966'], // Light orange background color
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    $columnIndex++; // Move to the next column
                }
                $currentMonth = $monthYear;
                $monthStartColumn = $columnIndex; // Set the start column for the new month

                // Add the "Actual Count" column before the first date of the month
                $sheet->setCellValueByColumnAndRow($columnIndex, $rowTitles, 'Actual Count');
                $sheet->mergeCellsByColumnAndRow($columnIndex, $rowTitles, $columnIndex, $rowShifts - 0);
                $sheet->getStyleByColumnAndRow($columnIndex, $rowTitles)->getAlignment()->setTextRotation(90); // Yellow background color
                $sheet->getColumnDimensionByColumn($columnIndex)->setWidth(3.5);
                $sheet->getStyleByColumnAndRow($columnIndex, $rowTitles, $columnIndex, $rowShifts - 0)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'A4C2F4'], // Light blue background color
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                $columnIndex++; // Move to the next column for dates
            }


            if (in_array($shiftDate, $datesWithContent)) {
                if (!isset($processedDates[$shiftDate])) {
                    $columnStart = $columnIndex;

                    // Set date header
                    $sheet->setCellValueByColumnAndRow($columnStart, $rowTitles, $shiftDate);
                    $sheet->mergeCellsByColumnAndRow($columnStart, $rowTitles, $columnStart + (count($shiftTimes) - 1), $rowTitles); // Merge for shifts
                    $sheet->getStyleByColumnAndRow($columnStart, $rowTitles)->getAlignment()->setWrapText(true); // Enable wrap text
                    $sheet->getStyleByColumnAndRow($columnStart, $rowTitles)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2CC'); // Yellow background color

                    // Set shift times
                    foreach ($shiftTimes as $shiftIndex => $shift) {
                        $shiftColumn = $columnStart + $shiftIndex;
                        $sheet->setCellValueByColumnAndRow($shiftColumn, $rowShifts, $shift);
                        $sheet->getStyleByColumnAndRow($shiftColumn, $rowShifts)->getAlignment()->setWrapText(true); // Enable wrap text
                        $sheet->getStyleByColumnAndRow($shiftColumn, $rowShifts)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('CCFFCC');
                        $sheet->getStyleByColumnAndRow($shiftColumn, $rowShifts)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
                        $sheet->getStyleByColumnAndRow($shiftColumn, $rowShifts)->getAlignment()->setTextRotation(90);
                        $sheet->getColumnDimensionByColumn($shiftColumn)->setWidth(3.5);
                    }

                    $dateColumns[$shiftDate] = [
                        'start' => $columnStart,
                        'end' => $columnStart + (count($shiftTimes) - 1),
                    ];

                    $sheet->getStyleByColumnAndRow($columnStart, $rowTitles, $columnStart + count($shiftTimes) - 1, $rowShifts)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);

                    $processedDates[$shiftDate] = true;
                    $columnIndex += count($shiftTimes); // Move past shift columns
                }
            }

            $currentDate->modify('+1 day');
        }

        if ($monthStartColumn !== null) {
            $sheet->mergeCellsByColumnAndRow($monthStartColumn, $rowMonth, $columnIndex - 0, $rowMonth);
            $sheet->setCellValueByColumnAndRow($monthStartColumn, $rowMonth, $currentMonth);

            // Apply styles to the month header
            $sheet->getStyleByColumnAndRow($monthStartColumn, $rowMonth, $columnIndex - 0, $rowMonth)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 14,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9EAD3'], // Light green background color
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            // Add the "Remarks" column
            $sheet->setCellValueByColumnAndRow($columnIndex, $rowTitles, 'Remarks');
            $sheet->mergeCellsByColumnAndRow($columnIndex, $rowTitles, $columnIndex, $rowShifts - 0);
            $sheet->getStyleByColumnAndRow($monthStartColumn, $rowTitles, $columnIndex, $rowShifts - 0)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFD966'], // Light orange background color
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
        }

        // Process data
        $itemQuantities = [];
        $itemDetails = [];

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
                    'allQuantity' => $row->allQuantity
                ];
            }

            if (!isset($itemQuantities[$itemName][$date])) {
                $itemQuantities[$itemName][$date] = array_fill(0, count($shiftTimes), 0);
            }

            $shiftIndex = $this->getShiftIndex($row->created_at);

            $itemQuantities[$itemName][$date][$shiftIndex] += $quantity;
        }

        // Write data to cells
        $rowStart = 5;
        $colStart = 5;

        foreach ($itemQuantities as $itemName => $dates) {
            $sheet->setCellValueByColumnAndRow(4, $rowStart, $itemName);
            $sheet->setCellValueByColumnAndRow(3, $rowStart, $itemDetails[$itemName]['unit'] ?? '');
            $sheet->setCellValueByColumnAndRow(2, $rowStart, $itemDetails[$itemName]['allQuantity'] ?? '');
            $sheet->setCellValueByColumnAndRow(1, $rowStart, $rowStart - 4);


             // Set item name in column D

            foreach ($dateColumns as $date => $columns) {
                if (isset($dates[$date])) {
                    $shiftQuantities = $dates[$date];
                } else {
                    $shiftQuantities = array_fill(0, count($shiftTimes), 0);
                }

                foreach ($shiftQuantities as $shiftIndex => $quantity) {
                    $sheet->setCellValueByColumnAndRow($columns['start'] + $shiftIndex, $rowStart, $quantity);
                }
            }

            $rowStart++;
        }

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
