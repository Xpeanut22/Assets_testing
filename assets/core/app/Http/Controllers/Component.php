<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComponentModel;
use Illuminate\Support\Facades\File;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use DB;
use App\User;
use App;
use Auth;
use Milon\Barcode\DNS2D;

require(app_path('fpdf\fpdf.php'));

class Component extends Controller
{
    use TraitSettings;
    use TraitAuditTrail;

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
        return view('component.index');
    }


    /**
     * get  detail page
     * @return object
     */
    public function detail($componentid)
    {
        return view('component.detail', compact('componentid'));
    }

    /**
     * check quantity
     * @return object
     */
    public function checkquantity($componentid, $quantity, $status)
    {
        $usedquantity = 0;
        $used =   DB::table('component_assets')
            ->select(array(DB::raw('SUM(component_assets.quantity) as components')))
            ->where('status', $status)
            ->where('component_assets.componentid', $componentid)->first();
        $usedquantity = $used->components;
        if (!$used) {
            $usedquantity = 0;
        }
        $remain =  $quantity - $usedquantity;

        return $remain;
    }

    public function checkquantity1($componentId, $quantity, $status)
    {
        // Implement your logic to check the quantity here
        // This is a placeholder example
        $usedQuantity = DB::table('component_assets')
            ->where('componentid', $componentId)
            ->where('status', $status)
            ->sum('quantity');

        return $quantity - $usedQuantity;
    }

    public function batchcheckquantity($groupid, $serial, $quantity, $status)
    {
        $usedquantity = 0;

        $used = DB::table('component_assets')
            ->join('component', 'component_assets.componentid', '=', 'component.id')
            ->select(DB::raw('SUM(component_assets.quantity) as components'))
            ->where('component_assets.status', $status)
            ->where('component.groupid', $groupid)
            ->where('component.serial', $serial)
            ->where('component.is_delete', 0)
            ->first();
        $remain = $quantity - $usedquantity;
        if (!$used) {
            $usedquantity = 0;
        }
        return $remain;
    }


    public function generatelabel($componentid)
    {
        return view('component.generate')->with('id', $componentid);
    }

    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $componentDeleteFilter = DB::getSchemaBuilder()->hasColumn('component', 'is_delete') ? 'where component.is_delete = 0' : '';

        $data = DB::select("select component.*, component_assets.created_by cacreated ,component_assets.quantity as caquantity, supplier.name as supplier, location.name as location, brand.name as brand, asset_type.name as type, component_assets.control_number, component_assets.issuancetype
        from component left join supplier
        on component.supplierid = supplier.id
        left join brand
        on component.brandid = brand.id
        left join location
        on component.locationid = location.id
        left join asset_type
        on component.typeid = asset_type.id left join component_assets
        on component_assets.componentid = component.id
        $componentDeleteFilter
        order by component.created_at desc");
        return Datatables::of($data)
            ->addColumn('avalaiblequantity', function ($single) {
                $remain = $this->checkquantity($single->id, $single->quantity, 1);
                return $remain;
            })
            ->addColumn('pictures', function ($single) {
                $image = $single->picture ?: 'pic.png';
                $imageUrl = url('/upload/assets/' . $image);
                $fallbackUrl = url('/upload/assets/pic.png');

                return '<img src="' . $imageUrl . '" style="width:90px" onerror="this.onerror=null;this.src=\'' . $fallbackUrl . '\';"/>';
            })
            ->addColumn('action', function ($accountsingle) {
                //for checkout 2 button, checkin or checkout depand the record

                $remain = $this->checkquantity($accountsingle->id, $accountsingle->quantity, 1);
                $checkout = '<a class="dropdown-item" href="#" id="btncheckin" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#checkout"><i class="fa fa-check"></i> ' . trans('lang.issue') . '</a>';

                if ($accountsingle->checkstatus === 2) {
                    if ($remain === 0) {
                        $checkout = '';
                    } else {
                        $checkout = '<a class="dropdown-item" href="#" id="btncheckout"  customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#checkout"><i class="fa fa-check"></i> ' . trans('lang.checkout') . '</a>';
                    }
                }

                return '
                <div class="btn-group">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                </button>
                <div class="dropdown-menu actionmenu">
               ' . $checkout . '
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="' . url('/') . '/componentlist/detail/' . $accountsingle->id . '"id="btndetail" customdata=' . $accountsingle->id . '  ><i class="fa fa-file-text"></i> ' . trans('lang.detail') . '</a>
                <a class="dropdown-item" href="#" id="btnedit" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i> ' . trans('lang.edit') . '</a>
                <a class="dropdown-item" href="#" id="btnedit" customdata=' . $accountsingle->id . '  data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i> ' . trans('lang.delete') . '</a>
                </div>
            </div>';
            })->rawColumns(['avalaiblequantity', 'pictures', 'action'])
            ->make(true);
    }

    public function getGroupedComponents()
    {
        $hasComponentDeleteColumn = DB::getSchemaBuilder()->hasColumn('component', 'is_delete');
        $componentDeleteFilter = $hasComponentDeleteColumn ? 'AND is_delete = 0' : '';
        $componentAliasDeleteFilter = $hasComponentDeleteColumn ? 'AND c.is_delete = 0' : '';
        $componentControlDeleteFilter = $hasComponentDeleteColumn ? 'AND cc.is_delete = 0' : '';

        $data = DB::select("
        SELECT
            c.name,
            COUNT(*) as total,
            (
                SELECT picture
                FROM component
                WHERE name = c.name
                AND picture IS NOT NULL
                $componentDeleteFilter
                LIMIT 1
            ) as picture,
            (
                SELECT GROUP_CONCAT(serial SEPARATOR ',')
                FROM component
                WHERE name = c.name
                $componentDeleteFilter
            ) as all_serials,
            (
                SELECT GROUP_CONCAT(control_number SEPARATOR ',')
                FROM component_assets ca
                LEFT JOIN component cc ON cc.id = ca.componentid
                WHERE cc.name = c.name
                $componentControlDeleteFilter
            ) as all_controls
        FROM component c
        WHERE 1 = 1
        $componentAliasDeleteFilter
        GROUP BY c.name
        ORDER BY c.name
    ");


        return Datatables::of($data)
            ->addColumn('pictures', function ($row) {
                $url = $row->picture
                    ? url('/upload/assets/' . $row->picture)
                    : url('/upload/assets/pic.png');
                $fallbackUrl = url('/upload/assets/pic.png');

                return '<img src="' . $url . '" style="width:90px" onerror="this.onerror=null;this.src=\'' . $fallbackUrl . '\';"/>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-info btn-show-component" data-name="'
                    . $row->name . '">View Items</button>';
            })
            ->addColumn('all_serials', function ($row) {
                return $row->all_serials;
            })
            ->addColumn('all_controls', function ($row) {
                return $row->all_controls;
            })
            ->rawColumns(['pictures', 'action'])
            ->make(true);
    }


    public function getComponentsByName($name)
    {
        $componentDeleteFilter = DB::getSchemaBuilder()->hasColumn('component', 'is_delete') ? 'AND component.is_delete = 0' : '';

        $data = DB::select("
        SELECT
            component.*,
            supplier.name as supplier,
            location.name as location,
            brand.name as brand,
            asset_type.name as type,
            (
                component.quantity - COALESCE((
                    SELECT SUM(ca.quantity)
                    FROM component_assets ca
                    WHERE ca.componentid = component.id
                    AND ca.status = 1
                ), 0)
            ) as caquantity,
            (
                SELECT ca2.control_number
                FROM component_assets ca2
                WHERE ca2.componentid = component.id
                AND ca2.status = 1
                ORDER BY ca2.id DESC
                LIMIT 1
            ) as control_number,
            (
                SELECT ca3.issuancetype
                FROM component_assets ca3
                WHERE ca3.componentid = component.id
                AND ca3.status = 1
                ORDER BY ca3.id DESC
                LIMIT 1
            ) as issuancetype
        FROM component
        LEFT JOIN supplier ON component.supplierid = supplier.id
        LEFT JOIN brand ON component.brandid = brand.id
        LEFT JOIN location ON component.locationid = location.id
        LEFT JOIN asset_type ON component.typeid = asset_type.id
        WHERE component.name = ?
        $componentDeleteFilter
    ", [$name]);

        foreach ($data as $row) {

            $row->action = '
        <div class="btn-group">
            <button class="btn btn-sm btn-primary dropdown-toggle"
                type="button" data-toggle="dropdown">
                <i class="fa fa-ellipsis-h"></i>
            </button>
            <div class="dropdown-menu actionmenu">
                <a class="dropdown-item"
                    href="' . url('/') . '/componentlist/detail/' . $row->id . '">
                    <i class="fa fa-file-text"></i> Detail
                </a>
                <a class="dropdown-item"
                    href="#" customdata=' . $row->id . '
                    data-toggle="modal" data-target="#edit">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <a class="dropdown-item"
                    href="#" customdata=' . $row->id . '
                    data-toggle="modal" data-target="#delete">
                    <i class="fa fa-trash"></i> Delete
                </a>
            </div>
        </div>';
        }

        return response()->json($data);
    }



    /**
     * get single data where is not id
     * @return object
     */

    public function isnotbyid()
    {

        $data = DB::table("component")->select('*')->where('is_delete', 0)->whereNotIn('id', function ($query) {
            $query->select('componentid')->from('depreciation')->whereNotNull('componentid')->where('is_delete', 0);
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
     * get single data
     * @param integer $id
     * @return object
     */

    public function byid(Request $request)
    {
        $id            = $request->input('id');

        $query = DB::table('component')->select('component.*', 'component.name as componentname', 'component.created_at as assetcreated_at', 'component.updated_at as assetupdated_at', 'component.description as componentdescription', 'brand.name as brand', 'asset_type.name as type', 'supplier.name as supplier', 'location.name as location')
            ->leftJoin('brand', 'brand.id', '=', 'component.brandid')
            ->leftJoin('asset_type', 'asset_type.id', '=', 'component.typeid')
            ->leftJoin('supplier', 'supplier.id', '=', 'component.supplierid')
            ->leftJoin('location', 'location.id', '=', 'component.locationid')
            ->where('component.id', $id);

        if (DB::getSchemaBuilder()->hasColumn('component', 'is_delete')) {
            $query->where(function ($innerQuery) {
                $innerQuery->where('component.is_delete', 0)->orWhereNull('component.is_delete');
            });
        }

        $data = $query->first();

        if ($data) {

            //set status
            $status = '-';
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

            //get date format setting
            $settingObj = DB::table('settings')->where('id', '1')->first();
            $dateFormat = ($settingObj && !empty($settingObj->formatdate)) ? $settingObj->formatdate : 'Y-m-d';
            $currency = ($settingObj && isset($settingObj->currency)) ? $settingObj->currency : '';

            $formatDate = function($dateStr) use ($dateFormat) {
                if (empty($dateStr)) {
                    return '-';
                }
                $time = strtotime($dateStr);
                return ($time !== false) ? date($dateFormat, $time) : '-';
            };

            $res['success'] = 'success';
            $res['message'] = $data;
            $res['assetcreated_at'] = $formatDate($data->assetcreated_at ?? null);
            $res['assetupdated_at'] = $formatDate($data->updated_at ?? null);
            $res['assetpurchasedate'] = $formatDate($data->purchasedate ?? null);
            $res['assetcost'] = isset($data->cost) && $data->cost !== '' ? $currency . $data->cost : '-';
            $res['assetstatus'] = $status;

            $assetbarcode = '-';
            if (!empty($data->serial)) {
                try {
                    $assetbarcode = '<img src="data:image/png;base64,' . DNS2D::getBarcodePNG((string)$data->serial, 'QRCODE') . '" alt="barcode" width="70" />';
                } catch (\Throwable $e) {
                    $assetbarcode = '-';
                }
            }
            $res['assetbarcode'] = $assetbarcode;

            $assetImage = !empty($data->picture) ? $data->picture : 'pic.png';
            $res['assetimage']  = url('/upload/assets/' . $assetImage);
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }


    /**
     * get single data
     * @param integer $id
     * @return object
     */

    public function singlehistorycomponentbyid(Request $request)
    {
        $id            = $request->input('id');

        $data = DB::table('component_assets')->select('component_assets.*')
            ->where('component_assets.id', $id)
            ->first();

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
     * @param string  $locationid
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


    // saving for multiple array on input serials

    public function save(Request $request)
    {
        $supplierid         = $request->input('supplierid');
        $locationid         = $request->input('locationid');
        $typeid             = $request->input('typeid');
        $brandid            = $request->input('brandid');
        $name               = $request->input('name');
        $serials            = $request->input('serial'); // Serial input as a comma-separated string
        $quantity           = $request->input('quantity');
        $purchasedate       = $request->input('purchasedate');
        // $cost               = 0;
        $unit               = $request->input('unit');
        $warranty           = $request->input('warranty');
        $status             = $request->input('status');
        $picture            = $request->file('picture');
        $description        = $request->input('description');
        $defaultimage       = 'pic.png';
        $created_at         = date("Y-m-d H:i:s");
        $updated_at         = date("Y-m-d H:i:s");
        $groupid         =

            $message = ['picture.mimes' => trans('lang.upload_error')];

        if ($request->hasFile('picture')) {
            $this->validate($request, ['picture' => 'mimes:jpeg,png,jpg|max:2048'], $message);
            $picturename  = date('mdYHis') . uniqid() . $request->file('picture')->getClientOriginalName();
            $request->file('picture')->move(public_path("/upload/assets"), $picturename);
        } else {
            $picturename = $defaultimage;
        }

        $res['message'] = 'failed'; // Default response

        $maxGroupid = DB::table('component')->max('groupid');
        $newGroupid = $maxGroupid + 1;

        // Split the serials string into an array
        $serialsArray = array_map('trim', explode(',', $serials));

        foreach ($serialsArray as $serial) {
            $data = [
                'name' => $name,
                'locationid' => $locationid,
                'supplierid' => $supplierid,
                'brandid' => $brandid,
                'typeid' => $typeid,
                'serial' => $serial,
                'quantity' => $quantity,
                'purchasedate' => $purchasedate,
                // 'cost' => $cost,
                'unit' => $unit,
                'warranty' => $warranty,
                'status' => $status,
                'picture' => $picturename,
                'description' => $description,
                'checkstatus' => 0,
                'is_delete' => 0,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
                'groupid' => $newGroupid
            ];

            $insert = DB::table('component')->insert($data);
            if ($insert) {
                $res['message'] = 'success';
            } else {
                $res['message'] = 'failed';
                break; // Exit loop if any insert fails
            }
        }

        if ($res['message'] === 'success') {
            $this->auditTrail('Issuance', 'Create', 'Created component: '.$name.' ('.count($serialsArray).' item/s).');
        }

        return response($res);
    }




    // public function save(Request $request){
    //     $supplierid         = $request->input( 'supplierid' );
    //     $locationid         = $request->input( 'locationid' );
    //     $typeid             = $request->input( 'typeid' );
    //     $brandid            = $request->input( 'brandid' );
    //     $name               = $request->input( 'name' );
    //     $serial             = $request->input( 'serial' );
    //     $quantity           = $request->input( 'quantity' );
    //     $purchasedate       = $request->input( 'purchasedate' );
    //     $cost               = $request->input( 'cost' );
    //     $warranty           = $request->input( 'warranty' );
    //     $status             = $request->input( 'status' );
    //     $picture            = $request->file( 'picture' );
    //     $description        = $request->input( 'description' );
    //     $defaultimage       = 'pic.png';
    //     $created_at         = date("Y-m-d H:i:s");
    //     $updated_at         = date("Y-m-d H:i:s");
    //     $message = ['picture.mimes'=>trans('lang.upload_error')];



    //         if($request->hasFile('picture')) {
    //             $this->validate($request, ['picture' => 'mimes:jpeg,png,jpg|max:2048'],$message);
    //             $picturename  = date('mdYHis').uniqid().$request->file('picture')->getClientOriginalName();
    //             $request->file('picture')->move(public_path("/upload/assets"), $picturename);
    //             $data       = array('name'=>$name,
    //                         'locationid'=>$locationid,
    //                         'supplierid'=>$supplierid,
    //                         'brandid'=>$brandid,
    //                         'typeid'=>$typeid,
    //                         'serial'=>$serial,
    //                         'quantity'=>$quantity,
    //                         'purchasedate'=>$purchasedate,
    //                         'cost'=>$cost,
    //                         'warranty'=>$warranty,
    //                         'status'=>$status,
    //                         'picture'=>$picturename,
    //                         'description'=>$description,
    //                         'checkstatus'=>0,
    //                         'created_at'=>$created_at,
    //                         'updated_at'=>$updated_at);
    //             $insert     = DB::table( 'component' )->insert( $data );

    //         }else{
    //             $data       = array('name'=>$name,
    //                             'locationid'=>$locationid,
    //                             'supplierid'=>$supplierid,
    //                             'typeid'=>$typeid,
    //                             'brandid'=>$brandid,
    //                             'serial'=>$serial,
    //                             'quantity'=>$quantity,
    //                             'purchasedate'=>$purchasedate,
    //                             'cost'=>$cost,
    //                             'warranty'=>$warranty,
    //                             'status'=>$status,
    //                             'checkstatus'=>0,
    //                             'picture'=>$defaultimage,
    //                             'description'=>$description,
    //                             'created_at'=>$created_at,
    //                             'updated_at'=>$updated_at);

    //             $insert     = DB::table( 'component' )->insert( $data );

    //         }

    //         if ( $insert ) {
    //             $res['message'] = 'success';

    //         } else{
    //             $res['message'] = 'failed';
    //         }

    //     return response( $res );
    // }

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
        $id                 = $request->input('id');
        $supplierid         = $request->input('supplierid');
        $locationid         = $request->input('locationid');
        $typeid             = $request->input('typeid');
        $brandid            = $request->input('brandid');
        $name               = $request->input('name');
        $serial             = $request->input('serial');
        $quantity           = $request->input('quantity');
        $purchasedate       = $request->input('purchasedate');
        // $cost               = $request->input('cost');
        $unit               = $request->input('unit');
        $warranty           = $request->input('warranty');
        $status             = $request->input('status');
        $picture            = $request->file('picture');
        $description        = $request->input('description');
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $message = ['picture.mimes' => trans('lang.upload_error')];
        $oldComponent       = DB::table('component')->where('id', $id)->where('is_delete', 0)->first();




        if ($request->hasFile('picture')) {
            $this->validate($request, ['picture' => 'mimes:jpeg,png,jpg|max:2048'], $message);
            $picturename  = date('mdYHis') . uniqid() . $request->file('picture')->getClientOriginalName();
            $request->file('picture')->move(public_path("/upload/assets"), $picturename);

            $update = DB::table('component')->where('id', $id)->where('is_delete', 0)
                ->update(
                    [
                        'name'                => $name,
                        'locationid'          => $locationid,
                        'supplierid'          => $supplierid,
                        'brandid'             => $brandid,
                        'typeid'              => $typeid,
                        'serial'              => $serial,
                        'quantity'            => $quantity,
                        'purchasedate'        => $purchasedate,
                        // 'cost'                => $cost,
                        'unit'                => $unit,
                        'warranty'            => $warranty,
                        'status'              => $status,
                        'description'         => $description,
                        'picture'             => $picturename,
                        'updated_at'          => $updated_at
                    ]
                );
        } else {
            $update = DB::table('component')->where('id', $id)->where('is_delete', 0)
                ->update(
                    [
                        'name'                => $name,
                        'locationid'          => $locationid,
                        'supplierid'          => $supplierid,
                        'brandid'             => $brandid,
                        'typeid'              => $typeid,
                        'serial'              => $serial,
                        'quantity'            => $quantity,
                        'purchasedate'        => $purchasedate,
                        // 'cost'                => $cost,
                        'unit'                => $unit,
                        'warranty'            => $warranty,
                        'status'              => $status,
                        'description'         => $description,
                        'updated_at'          => $updated_at
                    ]
                );
        }

        if ($update) {
            $res['message'] = 'success';
            $labelMap = [
                'name' => 'Name',
                'serial' => 'Serial',
                'locationid' => 'Location ID',
                'supplierid' => 'Supplier ID',
                'brandid' => 'Brand ID',
                'typeid' => 'Type ID',
                'quantity' => 'Quantity',
                'unit' => 'Unit',
                'purchasedate' => 'Purchase Date',
                'warranty' => 'Warranty',
                'status' => 'Status',
                'description' => 'Description'
            ];
            $newData = [
                'name' => $name,
                'serial' => $serial,
                'locationid' => $locationid,
                'supplierid' => $supplierid,
                'brandid' => $brandid,
                'typeid' => $typeid,
                'quantity' => $quantity,
                'unit' => $unit,
                'purchasedate' => $purchasedate,
                'warranty' => $warranty,
                'status' => $status,
                'description' => $description
            ];
            $diff = $this->auditCalculateDiff($oldComponent, $newData, $labelMap);
            $detailsText = 'Updated component: '.$name.' ('.$serial.')' . ($diff['details'] ? ":\n" . $diff['details'] : '');
            $this->auditTrail('Issuance', 'Update', $detailsText, 'Issuance', $id, $diff['old'], $diff['new']);
        } else {
            $res['message'] = 'failed';
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
        $componentid    = $request->input('componentid');
        $employeeid        = $request->input('employeeid');
        $quantity       = $request->input('quantity');
        $date           = $request->input('checkoutdate2');
        $controlno           = $request->input('controlno');
        $issuancetype           = $request->input('issuancetype');
        $remarks           = $request->input('remarks');
        $receiverby           = $request->input('receivedby');
        $contactno           = $request->input('contactno');
        $typeofid           = $request->input('typeofid');
        $idno           = $request->input('idno');
        $office           = $request->input('office');
        $status         = '1'; //checkout = 1
        $checkstatus    = '2';
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");

        $hasComponentDeleteColumn = DB::getSchemaBuilder()->hasColumn('component', 'is_delete');
        $balanceQuery = DB::table('component')->select('quantity')->where('id', $componentid);
        if ($hasComponentDeleteColumn) {
            $balanceQuery->where('is_delete', 0);
        }
        $balance = $balanceQuery->first();
        if (!$balance) {
            $res['success'] = 'failed';
            return response($res);
        }
        $checkquantity = $this->checkquantity($componentid, $balance->quantity, 1);
        $remain =  $checkquantity - $quantity;

        if ($remain < 0) {
            $res['success'] = '0';
        } else {
            $data               = array('componentid' => $componentid, 'status' => $status, 'quantity' => $quantity, 'employeeid' => $employeeid, 'contactno' => $contactno, 'typeofid' => $typeofid, 'idno' => $idno, 'department' => $office, 'date' => $date, 'control_number' => $controlno, 'issuancetype' => $issuancetype, 'remarks' => $remarks, 'created_by' => $receiverby, 'created_at' => $created_at, 'updated_at' => $updated_at);
            $insert         = DB::table('component_assets')->insert($data);

            if ($insert) {

                //set status in table asset
                $updateQuery = DB::table('component')->where('id', $componentid);
                if ($hasComponentDeleteColumn) {
                    $updateQuery->where('is_delete', 0);
                }
                $update = $updateQuery->update(
                    [
                        'checkstatus'         => $checkstatus,
                        'updated_at'          => $updated_at
                    ]
                );

                $res['success'] = 'success';
            } else {
                $res['success'] = 'failed';
            }
        }
        return response($res);
    }

    public function saveBatchComponentCheckout(Request $request)
    {
        $components = $request->input('components');

        if (!is_array($components) || empty($components)) {
            return response()->json([
                'success' => false,
                'message' => 'No item selected'
            ], 400);
        }

        if (!$request->filled('checkoutemployeeid1')) {
            return response()->json([
                'success' => false,
                'message' => 'Please select Issued to.'
            ], 422);
        }

        if (!$request->filled('depid')) {
            return response()->json([
                'success' => false,
                'message' => 'Please select Department / Office Representing.'
            ], 422);
        }

        if (!$request->filled('core')) {
            return response()->json([
                'success' => false,
                'message' => 'Please select Condition of Equipment.'
            ], 422);
        }

        $timestamp = now();
        $receiverby = Auth::user()->fullname;
        $successSaves = 0;
        $insertedIds = [];
        $hasComponentDeleteColumn = DB::getSchemaBuilder()->hasColumn('component', 'is_delete');

        // // Generate groupid
        // $lastGroupId = DB::table('component_assets')->max('groupid');
        // $groupid = $lastGroupId ? $lastGroupId + 1 : 1;

        foreach ($components as $component) {

            if (!isset($component['id'])) {
                continue;
            }

            $componentQuery = DB::table('component')
                ->where('id', $component['id']);

            if ($hasComponentDeleteColumn) {
                $componentQuery->where('is_delete', 0);
            }

            $dbComponent = $componentQuery->first();

            if (!$dbComponent) {
                continue;
            }

            $issueQuantity = (int)($component['quantity'] ?? $component['issue_quantity'] ?? 0);
            if ($issueQuantity <= 0) {
                continue;
            }

            $availableQty = (int)$this->checkquantity($dbComponent->id, $dbComponent->quantity, 1);
            if ($issueQuantity > $availableQty) {
                continue;
            }

            $issuanceDate = $request->checkindate ? date('Y-m-d H:i:s', strtotime($request->checkindate)) : now();

            $data = [
                'assetid'        => null, // since this is component checkout
                'componentid'    => $dbComponent->id,
                'quantity'       => $issueQuantity,
                'status'         => 1, // checkout
                'date'           => $issuanceDate,
                'employeeid'     => $request->checkoutemployeeid1 ?? null,
                'remarks'        => $request->remarks ?? null,
                'control_number' => $request->controlno ?? null,
                'created_by'     => $receiverby,
                'issuancetype'   => $request->issuancetype1 ?? null,
                'department'     => $request->depid ?? null,
                'contactno'      => $request->contactno ?? null,
                'idno'           => $request->idno ?? null,
                // 'typeofid'       => $request->typeofid ?? null,
                'created_at'     => $timestamp,
                'updated_at'     => $timestamp,
            ];

            $insertId = DB::table('component_assets')->insertGetId($data);

            if ($insertId) {
                $insertedIds[] = $insertId;

                $updateQuery = DB::table('component')
                    ->where('id', $dbComponent->id);

                if ($hasComponentDeleteColumn) {
                    $updateQuery->where('is_delete', 0);
                }

                $updateQuery->update([
                    'checkstatus' => 2,
                    'updated_at'  => $timestamp
                ]);

                $successSaves++;
            }
        }

        if ($successSaves === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No component was saved. Please check available quantity.'
            ], 422);
        }

        $this->auditTrail('Issuance', 'Scan', 'Issued '.$successSaves.' item/s. Control number: '.($request->controlno ?: '-').'.');

        return response()->json([
            'success' => true,
            'saved'   => $successSaves,
            'print_url' => url('component/batchissuanceprint') . '?ids=' . implode(',', $insertedIds),
            'pdf_url' => url('component/batchissuanceprint') . '?ids=' . implode(',', $insertedIds) . '&download=1'
        ]);
    }

    public function batchissuanceprint(Request $request)
    {
        $idsParam = (string)$request->query('ids', '');
        $ids = collect(explode(',', $idsParam))
            ->map(function ($id) {
                return (int)trim($id);
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->values()
            ->all();

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No issued items found.'
            ], 404);
        }

        $rows = DB::table('component_assets as ca')
            ->join('component as c', 'ca.componentid', '=', 'c.id')
            ->leftJoin('employees as e', 'ca.employeeid', '=', 'e.id')
            ->leftJoin('department as d', 'ca.department', '=', 'd.id')
            ->select(
                'ca.*',
                'c.name as component_name',
                'c.serial as component_serial',
                'e.fullname as employee_name',
                'e.mobile_number as employee_contact',
                'd.name as department_name'
            )
            ->whereIn('ca.id', $ids)
            ->where('ca.status', 1)
            ->orderBy('ca.id', 'asc')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Issued items not found.'
            ], 404);
        }

        $first = $rows->first();
        $issuedTo = $first->employee_name ?: '-';
        $department = $first->department_name ?: '-';
        $contactNo = $first->employee_contact ?: ($first->contactno ?: '-');
        $controlNumber = $first->control_number ?: '-';
        $issueDate = $first->date ? date('m/d/Y H:i', strtotime($first->date)) : date('m/d/Y H:i');
        $issuedBy = Auth::user()->fullname ?: ($first->created_by ?: '-');

        if ($request->query('printout')) {
            $e = function ($value) {
                return e($value ?: '-');
            };
            $itemRows = '';
            $rowNo = 1;
            foreach ($rows as $item) {
                $description = trim(($item->component_name ?: '-') . ' / ' . ($item->component_serial ?: '-'));
                $itemRows .= '<tr>'
                    . '<td>' . $rowNo . '.</td>'
                    . '<td>' . $e($description) . '</td>'
                    . '<td>' . $e($item->quantity) . '</td>'
                    . '<td>' . e($item->remarks ?: '') . '</td>'
                    . '</tr>';
                $rowNo++;
            }
            while ($rowNo <= 5) {
                $itemRows .= '<tr><td>' . $rowNo . '.</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
                $rowNo++;
            }

            $html = '<!doctype html><html><head><meta charset="utf-8"><title>Issuance Printout</title>'
                . '<style>'
                . '@page{size:legal;margin:12mm}body{font-family:Arial,sans-serif;color:#000;margin:0}.page{width:190mm;margin:0 auto}.center{text-align:center}.header{position:relative;padding-top:4px}.logo{position:absolute;top:0;width:72px;height:72px;object-fit:contain}.left-logo{left:0}.right-logo{right:0}.rule{border-top:3px solid #000;border-bottom:1px solid #000;height:3px;margin:8px 0 22px}.top-row{display:flex;justify-content:space-between;margin-bottom:22px;font-size:16px}.underline{display:inline-block;min-width:120px;border-bottom:1px solid #000;padding-left:8px}.title{font-weight:bold;font-size:18px;margin:18px 0 26px}.field{margin:5px 0;font-size:14px}.field span{display:inline-block;border-bottom:1px solid #000;min-width:260px;padding-left:8px}h3{font-size:16px;margin:22px 0 8px}table{border-collapse:collapse;width:100%;font-size:13px}th,td{border:1px solid #000;padding:8px;vertical-align:middle}th{background:#aeaA88;text-align:center}td:nth-child(1),td:nth-child(3){text-align:center}.signatures{margin-top:38px;font-size:13px}.sig-row{display:flex;justify-content:space-between;margin-top:18px}.sig{width:38%;text-align:center}.line{border-bottom:1px solid #000;height:22px;margin-bottom:4px}.name{font-weight:bold}.footer-note{margin-top:18px;font-size:12px}@media print{.no-print{display:none}.page{width:auto}body{margin:0}}'
                . '</style></head><body onload="setTimeout(function(){window.print();},300)">'
                . '<button class="no-print" onclick="window.print()" style="position:fixed;right:16px;top:16px;padding:8px 14px">Print</button>'
                . '<div class="page"><div class="header center">'
                . '<div>Republic of the Philippines</div><strong>CITY GOVERNMENT OF MUNTINLUPA</strong><div>City of Muntinlupa</div>'
                . '<strong>DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT</strong><div>(Formerly Muntinlupa City Disaster Risk Reduction Management Office)</div>'
                . '<div>Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City</div><div>Tel No.: 8925-43-82</div>'
                . '<div class="rule"></div></div>'
                . '<div class="top-row"><div>DATE:<span class="underline">' . $e($issueDate) . '</span></div><div><div>CGM-OP-MCDRRM-01F2</div><div>Control No.<span class="underline">' . $e($controlNumber) . '</span></div></div></div>'
                . '<div class="center title">MATERIALS & EQUIPMENT ISSUANCE FORM</div>'
                . '<div class="field"><strong>Name:</strong><span>' . $e($issuedTo) . '</span></div>'
                . '<div class="field"><strong>Department:</strong><span>' . $e($department) . '</span></div>'
                . '<div class="field"><strong>Contact Number:</strong><span>' . $e($contactNo) . '</span></div>'
                . '<h3 class="center">Material/Equipment Requested</h3>'
                . '<table><thead><tr><th style="width:12%">No.</th><th>Item(s) Description</th><th style="width:18%">Quantity</th><th style="width:24%">Remarks</th></tr></thead><tbody>' . $itemRows . '</tbody></table>'
                . '<div class="footer-note">Please verify all information before releasing the materials/equipment.</div>'
                . '<div class="signatures">'
                . '<div class="sig-row"><div class="sig">RECEIVED BY:<div class="line name">' . $e($issuedTo) . '</div>Signature Over Printed Name</div><div class="sig">CHECKED BY:<div class="line name">ALMOND G. GREGORIO</div>Section Head - Logistic</div></div>'
                . '<div class="sig-row"><div class="sig">ISSUED BY:<div class="line name">' . $e($issuedBy) . '</div>Signature Over Printed Name</div><div class="sig">APPROVED BY:<div class="line name">ERWIN O. ALFONSO</div>Department Head - DDRM</div></div>'
                . '</div></div></body></html>';

            return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
        }

        $pdf = new \FPDF('P', 'mm', 'LEGAL');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetFont('Arial', '', 10);

        $assetBasePath = resource_path('views/component/Munti_IssuanceForm_AMS/Munti_IssuanceForm_AMS');
        $logoLeft = $assetBasePath . DIRECTORY_SEPARATOR . 'muntilogo.png';
        $logoRight = $assetBasePath . DIRECTORY_SEPARATOR . 'ddrm.png';
        $footer = $assetBasePath . DIRECTORY_SEPARATOR . 'CGM FOOTER.png';

        $pdf->SetXY(0, 7);
        $pdf->Cell(216, 4, 'Republic of the Philippines', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(216, 4, 'CITY GOVERNMENT OF MUNTINLUPA', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(216, 4, 'City of Muntinlupa', 0, 0, 'C');

        if (file_exists($logoLeft)) {
            $pdf->Image($logoLeft, 17, 5, 25, 25);
        }
        if (file_exists($logoRight)) {
            $pdf->Image($logoRight, 175, 5, 24, 24);
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(0, 25);
        $pdf->Cell(216, 4, 'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(216, 4, '(Formerly Muntinlupa City Disaster Risk Reduction Management Office)', 0, 1, 'C');
        $pdf->Cell(216, 4, 'Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City', 0, 1, 'C');
        $pdf->Cell(216, 4, 'Tel No.: 8925-43-82', 0, 1, 'C');

        $pdf->SetXY(12, 43.5);
        $pdf->SetFillColor(33, 19, 13);
        $pdf->Cell(192, 0.5, '', 1, 1, 'C', true);
        $pdf->SetXY(12, 45);
        $pdf->Cell(192, 0.2, '', 1, 1, 'C', true);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(20, 50);
        $pdf->Cell(12, 5, 'DATE:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 4, $issueDate, 'B', 0, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(160, 50);
        $pdf->Cell(45, 5, 'CGM-OP-MCDRRM-01F2', 0, 1, 'L');
        $pdf->SetXY(160, 56);
        $pdf->Cell(20, 5, 'Control No.', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 4, $controlNumber, 'B', 0, 'L');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(0, 66);
        $pdf->Cell(216, 4, 'MATERIALS & EQUIPMENT ISSUANCE FORM', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(20, 80);
        $pdf->Cell(14, 5, 'Name:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(88, 4, utf8_decode($issuedTo), 'B', 1, 'L');

        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(24, 5, 'Department:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(78, 4, utf8_decode($department), 'B', 1, 'L');

        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 5, 'Contact Number:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(72, 4, utf8_decode($contactNo), 'B', 1, 'L');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(0, 98);
        $pdf->Cell(216, 4, 'Material/Equipment Requested', 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(20, 108);
        $pdf->SetFillColor(174, 170, 136);
        $pdf->Cell(20, 8, 'No.', 1, 0, 'C', true);
        $pdf->Cell(100, 8, 'Item(s) Description', 1, 0, 'C', true);
        $pdf->Cell(27, 8, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(33, 8, 'Remarks', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 10);
        $rowY = 116;
        $rowNo = 1;
        foreach ($rows as $item) {
            if ($rowY > 236) {
                break;
            }
            $pdf->SetXY(20, $rowY);
            $description = trim(($item->component_name ?: '-') . ' / ' . ($item->component_serial ?: '-'));
            $pdf->Cell(20, 8, $rowNo . '.', 1, 0, 'C');
            $pdf->Cell(100, 8, utf8_decode($description), 1, 0, 'L');
            $pdf->Cell(27, 8, (string)$item->quantity, 1, 0, 'C');
            $pdf->Cell(33, 8, utf8_decode($item->remarks ?: ''), 1, 1, 'L');
            $rowY += 8;
            $rowNo++;
        }

        while ($rowNo <= 5) {
            $pdf->SetXY(20, $rowY);
            $pdf->Cell(20, 8, $rowNo . '.', 1, 0, 'C');
            $pdf->Cell(100, 8, '', 1, 0, 'L');
            $pdf->Cell(27, 8, '', 1, 0, 'C');
            $pdf->Cell(33, 8, '', 1, 1, 'L');
            $rowY += 8;
            $rowNo++;
        }

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(20, 250);
        $pdf->Cell(70, 6, 'RECEIVED BY:', 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, 'CHECKED BY:', 0, 1, 'C');
        $pdf->SetXY(20, 260);
        $pdf->Cell(70, 6, utf8_decode($issuedTo), 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, 'ALMOND G. GREGORIO', 0, 1, 'C');
        $yLine = $pdf->GetY() - 1;
        $pdf->Line(20, $yLine, 90, $yLine);
        $pdf->Line(130, $yLine, 200, $yLine);
        $pdf->SetX(20);
        $pdf->Cell(70, 6, 'Signature Over Printed Name', 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, 'Section Head - Logistic', 0, 1, 'C');

        $pdf->SetXY(20, 290);
        $pdf->Cell(70, 6, 'ISSUED BY:', 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, 'APPROVED BY:', 0, 1, 'C');
        $pdf->SetXY(20, 300);
        $pdf->Cell(70, 6, utf8_decode($issuedBy), 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, 'ERWIN O. ALFONSO', 0, 1, 'C');
        $yLine2 = $pdf->GetY() - 1;
        $pdf->Line(20, $yLine2, 90, $yLine2);
        $pdf->Line(130, $yLine2, 200, $yLine2);
        $pdf->SetX(20);
        $pdf->Cell(70, 6, 'Signature Over Printed Name', 0, 0, 'C');
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(70, 6, 'Department Head - DDRM', 0, 1, 'C');

        if (file_exists($footer)) {
            $pdf->Image($footer, 0, 336, 219, 20);
        }

        $safeControlNo = preg_replace('/[^A-Za-z0-9\-_]/', '_', $controlNumber);
        $safeIssuedTo = preg_replace('/[^A-Za-z0-9\-_]/', '_', (string) $issuedTo);
        $filename = 'material_issuance_' . trim(($safeIssuedTo ?: 'issued_to') . '_' . ($safeControlNo ?: 'batch'), '_') . '.pdf';
        $pdf->SetTitle('Material Issuance - ' . ($issuedTo ?: 'Issued To') . ' (' . ($controlNumber ?: 'Batch') . ')');
        $disposition = $request->query('download') ? 'attachment' : 'inline';

        return response($pdf->Output('S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    public function generateControlNumber(Request $request)
    {
        $prefix = strtoupper($request->get('prefix', 'IF'));
        $year = date('y');
        $pattern = $prefix . '-' . $year . '-%';

        $existingControlNumbers = DB::table('component_assets')
            ->where('control_number', 'like', $pattern)
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

        $nextSequence = $maxSequence + 1;
        $generatedControlNumber = $prefix . '-' . $year . '-' . $nextSequence;

        return response()->json([
            'success' => true,
            'message' => $generatedControlNumber
        ]);
    }

    public function batchsavecheckout(Request $request)
    {
        $groupid    = $request->input('component1');
        $serial    = $request->input('serial1');
        $componentIds = $request->input('component_ids');
        $employeeid        = $request->input('employeeid1');
        $quantity       = $request->input('quantity');
        $date           = $request->input('checkoutdate1');
        $controlno           = $request->input('controlno1');
        $issuancetype           = $request->input('issuancetype1');
        $remarks           = $request->input('remarks1');
        $receiverby           = $request->input('receivedby1');
        $contactno           = $request->input('contactno');
        $typeofid           = $request->input('typeofid');
        $idno           = $request->input('idno');
        $office           = $request->input('office');
        $status         = '1'; //checkout = 1
        $checkstatus    = '2';
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");

        $serialsArray = array_map('trim', explode(',', $serial));


        if (empty($componentIds)) {
            return response()->json([
                'success' => '0',
                'message' => 'No item selected'
            ]);
        }

        DB::beginTransaction();

        try {

            foreach ($componentIds as $componentId) {

                $component = DB::table('component')
                    ->where('id', $componentId)
                    ->where('is_delete', 0)
                    ->first();

                if (!$component) continue;

                $data = [
                    'componentid' => $component->id,
                    'quantity' => $component->quantity,
                    'employeeid' => $employeeid,
                    'contactno' => $contactno,
                    'typeofid' => $typeofid,
                    'idno' => $idno,
                    'department' => $office,
                    'date' => $date,
                    'status' => 1,
                    'remarks' => $remarks,
                    'control_number' => $controlno,
                    'issuancetype' => $issuancetype,
                    'created_by' => $receiverby,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                DB::table('component_assets')->insert($data);

                DB::table('component')
                    ->where('id', $component->id)
                    ->where('is_delete', 0)
                    ->update([
                        'checkstatus' => 2,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            return response()->json(['success' => 'success']);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => 'failed',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getcomponentbygroup(Request $request)
    {
        $groupid = $request->groupid;

        $components = DB::table('component')
            ->where('groupid', $groupid)
            ->where('checkstatus', 0) // only available
            ->where('is_delete', 0)
            ->get();

        return response()->json([
            'data' => $components
        ]);
    }

    // $checkquantity = $this->batchcheckquantity($groupid, $serial, $balance->quantity, 1);
    // $checkquantity = $this->checkquantity($test->id, $test->quantity, 1);
    // dd($checkquantity);
    // $remain =  $checkquantity - $quantity;

    // $balance = DB::table('component')->select('quantity')
    //     ->where('groupid', $groupid)
    //     ->where('serial', $serial)
    //     ->first();

    // $checkquantity = $this->checkquantity($serial, $quantity, 1);
    // dd($checkquantity);

    // dd($remain);

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
        $componentid    = $request->input('componentid');
        $assetid        = $request->input('assetid');
        $historyid      = $request->input('historyid');
        $quantity       = $request->input('quantity');
        $date           = $request->input('checkindate');
        $status         = '2'; //checkout = 1
        $checkstatus    = '0';
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");


        //check balance
        $balance = DB::table('component')->select('quantity')->where('id', $componentid)->where('is_delete', 0)->first();
        if (!$balance) {
            $res['success'] = 'failed';
            return response($res);
        }
        $checkquantity = $this->checkquantity($componentid, $balance->quantity, 2);
        $remain =  $checkquantity - $quantity;


        //checkbalance current should not more then the input
        $balance = DB::table('component_assets')->select('quantity')->where('id', $historyid)->first();

        if ($balance->quantity < $quantity || $quantity <= 0) {
            $res['success'] = '0';
        } else {

            //update current data base on quantity
            $updatequantity = $balance->quantity - $quantity;
            $updatehistory = DB::table('component_assets')->where('id', $historyid)
                ->update(
                    [
                        'quantity'            => $updatequantity,
                        'updated_at'          => $updated_at
                    ]
                );

            $data           = array('assetid' => $assetid, 'status' => $status, 'quantity' => $quantity, 'componentid' => $componentid, 'date' => $date, 'created_at' => $created_at, 'updated_at' => $updated_at);
            $insert         = DB::table('component_assets')->insert($data);

            if ($insert) {
                //set status in table asset
                /*  $update = DB::table( 'assets' )->where( 'id', $assetid )
                ->update(
                    [
                        'checkstatus'         => $checkstatus,
                        'updated_at'          => $updated_at
                    ]
                );*/
                $res['success'] = 'success';
            } else {
                $res['success'] = 'failed';
            }
        }

        return response($res);
    }



    /**
     * get single data by assets id for history
     * @param integer $id
     * @return object
     */
    public function componentBySerial(Request $request)
    {
        $serial = $request->input('searchValue');

        $query = DB::table('component')
            ->select('component.*')
            ->whereRaw('LOWER(component.serial) = ?', [strtolower(trim($serial))]);

        if (DB::getSchemaBuilder()->hasColumn('component', 'is_delete')) {
            $query->where(function ($query) {
                $query->where('component.is_delete', 0)
                    ->orWhereNull('component.is_delete');
            });
        }

        $data = $query->first();

        if (!$data) {
            return response()->json([
                'success' => 'failed',
                'message' => 'not_found'
            ]);
        }

        if (!is_null($data->status) && (int)$data->status !== 1) {
            return response()->json([
                'success' => 'failed',
                'componentstatus' => $data->status
            ]);
        }

        $issuedQuantity = (int) DB::table('component_assets')
            ->where('componentid', $data->id)
            ->where('status', 1)
            ->sum('quantity');

        $totalQuantity = (int) $data->quantity;
        $availableQuantity = max(0, $totalQuantity - $issuedQuantity);

        $data->total_quantity = $totalQuantity;
        $data->issued_quantity = $issuedQuantity;
        $data->available_quantity = $availableQuantity;

        if ($availableQuantity <= 0) {
            return response()->json([
                'success' => 'failed',
                'message' => 'already_issued',
                'available_quantity' => 0,
                'total_quantity' => $totalQuantity,
                'issued_quantity' => $issuedQuantity
            ]);
        }

        return response()->json([
            'success' => 'success',
            'message' => $data
        ]);
    }
    public function assetsbyid(Request $request)
    {
        $id            = $request->input('assetid');
        $componentDeleteFilter = DB::getSchemaBuilder()->hasColumn('component', 'is_delete') ? 'and component.is_delete = 0' : '';

        $data = DB::select("select component.*, supplier.name as supplier, brand.name as brand, asset_type.name as type
        from component left join supplier
        on component.supplierid = supplier.id
        left join brand
        on component.brandid = brand.id
        left join component_assets
        on component.id = component_assets.componentid
        left join asset_type
        on component.typeid = asset_type.id where component_assets.assetid ='$id'
        $componentDeleteFilter
        order by component.created_at desc");
        return Datatables::of($data)
            ->addColumn('avalaiblequantity', function ($single) {
                //count avalaible component
                $usedquantity = 0;
                $total = $single->quantity;
                $used =   DB::table('component_assets')
                    ->select(array(DB::raw('SUM(component_assets.quantity) as components')))
                    ->where('component_assets.componentid', $single->id)->first();
                $usedquantity = $used->components;
                if (!$used) {
                    $usedquantity = 0;
                }
                $remain =  $total - $usedquantity;
                return $remain;
            })
            ->addColumn('pictures', function ($single) {
                $image = $single->picture ?: 'pic.png';
                $imageUrl = url('/upload/assets/' . $image);
                $fallbackUrl = url('/upload/assets/pic.png');

                return '<img src="' . $imageUrl . '" style="width:90px" onerror="this.onerror=null;this.src=\'' . $fallbackUrl . '\';"/>';
            })
            ->rawColumns(['avalaiblequantity', 'pictures'])
            ->make(true);
    }


    /**
     * get single data by component id for history
     * @param integer $id
     * @return object
     */

    public function historycomponentbyid(Request $request)
    {
        $id            = $request->input('id');

        $data = DB::select("select component_assets.*, assets.name as assetname, component.name, component.serial, component.description, component.checkstatus, employees.fullname as efullname, receiver.fullname as rfullname, employees.mobile_number, department.name
        from component_assets left join assets
        on component_assets.assetid = assets.id left join component
        on component_assets.componentid = component.id left join employees
        on component_assets.employeeid = employees.id left join receiver
        on component_assets.created_by = receiver.id left join department
        on department.id = employees.departmentid
        where component_assets.componentid = '$id' and component_assets.status = 1 and component_assets.quantity > 0
        order by component_assets.created_at desc");
        return Datatables::of($data)

            ->addColumn('action', function ($single) {

                return '<a class="btn btn-sm btn-primary" href="#" id="btncheckin" customdata=' . $single->id . '  data-toggle="modal" data-target="#checkin"><i class="fa fa-check"></i> ' . trans('lang.checkin') . '</a>';
            })

            ->addColumn('date', function ($single) {

                $setting = DB::table('settings')->where('id', '1')->first();
                return date($setting->formatdate, strtotime($single->date));
            })
            ->rawColumns(['action', 'date'])
            ->make(true);
    }


    /**
     * get all  from database
     * @return object
     */
    // public function getrows(){
    //     $data = DB::table('component')->get();
    //     if ( $data ) {
    // 		$res['success'] = true;
    // 		$res['message']= $data;
    //     }
    //     return response( $res );
    // }

    public function getrows()
    {
        // Step 1: Get the grouped names along with their first id and groupid
        $groupedData = DB::table('component')
            ->select('name', 'groupid', DB::raw('MIN(id) as first_id'))
            ->whereNotNull('name')
            ->where('is_delete', 0)
            ->groupBy('name', 'groupid')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->get();

        if ($groupedData->isNotEmpty()) {
            $res['success'] = true;
            $res['data'] = $groupedData;
        } else {
            $res['success'] = false;
            $res['message'] = 'No data found';
        }

        return response()->json($res);
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


        //check if used in assets/user
        $getcomponent = DB::table('component_assets')
            ->where('componentid', '=', $id)
            ->first();
        if ($getcomponent) {
            $res['message'] = 'exist';
        } else {
            $getfilename = DB::table('component')
                ->where('id', '=', $id)
                ->where('is_delete', 0)
                ->first();

            $delete          = DB::table('component')->where('id', $id)->where('is_delete', 0)
                ->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")]);
            if ($delete) {
                $res['message'] = 'success';
                $this->auditTrail('Issuance', 'Delete', 'Deleted component ID: '.$id.'.');
            } else {
                $res['message'] = 'failed';
            }
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
        $lastid = DB::table('component')->where('is_delete', 0)->orderBy('id', 'desc')->first();

        if ($lastid) {
            $res['success'] = 'success';
            $res['message'] =  'COM' . date('ymd') . $lastid->id;
        } else {
            $res['message'] =  'COM' . date('ymd') . '1';
        }
        return response($res);
    }
}
