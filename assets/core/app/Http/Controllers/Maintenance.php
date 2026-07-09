<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BrandModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;

class Maintenance extends Controller
{
    use TraitSettings;

    public function __construct()
    {

        $data = $this->getapplications();
        $lang = $data->language;
        App::setLocale($lang);
        $this->middleware('auth');
    }

    //return view
    public function index()
    {
        return view('maintenance.index');
    }

    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $maintenanceDeleteFilter = DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete') ? 'and maintenance.is_delete = 0' : '';

        $data = DB::select("select maintenance.*, supplier.name as supplier, assets.name as asset, assets.id as assetsid, assets.assettag,
        CASE
            WHEN maintenance.type = 'Unserviceable' THEN IFNULL(employees.fullname, receiver.fullname)
            ELSE IFNULL(receiver.fullname, employees.fullname)
        END as fullname
        from maintenance left join supplier 
        on maintenance.supplierid = supplier.id left join assets 
        on maintenance.assetid = assets.id left join receiver
        on maintenance.created_by = receiver.id
        left join employees
        on maintenance.created_by = employees.id
        where maintenance.type != 'Operational'
        $maintenanceDeleteFilter
        order by maintenance.created_at desc");
        return Datatables::of($data)
            ->addColumn('action', function ($accountsingle) {
                return '<a href="#" id="btnedit" customdata=' . $accountsingle->id . ' class="btn btn-sm btn-primary" data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i> ' . trans('lang.edit') . '</a>
                    <a href="#" id="btndelete" customdata=' . $accountsingle->id . ' class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i> ' . trans('lang.delete') . '</a>';
            })
            ->addColumn('completion', function ($accountsingle) {

                $date1 = $accountsingle->startdate;
                $date2 = $accountsingle->enddate;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

                $completion =  $days;

                return $completion;
            })
            ->rawColumns(['action', 'completion'])
            ->make(true);
    }

    /**
     * get all  from database
     * @return object
     */
    public function getrows()
    {
        $query = DB::table('maintenance');
        if (DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete')) {
            $query->where('is_delete', 0);
        }
        $data = $query->get();
        if ($data) {
            $res['success'] = true;
            $res['message'] = $data;
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

        $query = DB::table('maintenance')->where('id', $id);
        if (DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete')) {
            $query->where('is_delete', 0);
        }
        $data = $query->first();

        if ($data) {
            $res['success'] = 'success';
            $res['message'] = $data;
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }

    /**
     * get single data by assets id for history
     * @param integer $id
     * @return object
     */

    public function assetsbyid(Request $request)
    {
        $id            = $request->input('assetid');
        $maintenanceDeleteFilter = DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete') ? 'and maintenance.is_delete = 0' : '';

        $data = DB::select("select maintenance.*, supplier.name as supplier, assets.name as asset, assets.assettag as assettag
        from maintenance left join supplier 
        on maintenance.supplierid = supplier.id
        left join assets 
        on maintenance.assetid = assets.id where maintenance.assetid = '$id'
        $maintenanceDeleteFilter
        order by maintenance.created_at desc");
        return Datatables::of($data)
            ->make(true);
    }

    /**
     * insert data  to database
     *
     * @param integer  $assetid
     * @param integer  $supplierid
     * @param string  $startdate
     * @param string  $enddate
     * @param string  $type
     * @return object
     */
    public function save(Request $request)
    {
        $assetid        = $request->input('assetid');
        $type           = $request->input('type');
        // $supplierid     = $request->input( 'supplierid' );
        // $mamount        = $request->input( 'mamount' );
        $reason_remarks = $request->input('reason_remarks');
        $startdate      = $request->input('startdate');
        $enddate        = $request->input('enddate');
        $receivedby     = $request->input('receivedby');

        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $data           = array(
            'assetid' => $assetid,
            // 'supplierid'=>$supplierid,
            'type' => $type,
            // 'mamount'=>$mamount,
            'reason_remarks' => $reason_remarks,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'created_by' => $receivedby,
            'created_at' => $created_at,
            'updated_at' => $updated_at
        );
        if (DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete')) {
            $data['is_delete'] = 0;
        }

        $insert         = DB::table('maintenance')->insert($data);
        $update = DB::table('assets')->where('id', $assetid)
            ->update(
                [
                    'status'          => '7',
                ]
            );


        if ($insert) {
            $res['success'] = 'success';
        } else {
            $res['success'] = 'failed';
        }

        return response($res);
    }

    /**
     * update data  to database
     *
     * @param integer  $assetid
     * @param integer  $supplierid
     * @param string  $startdate
     * @param string  $enddate
     * @param string  $type
     * @return object
     */
    public function update(Request $request)
    {
        $id             = $request->input('id');
        $assetid           = $request->input('assetid');
        $type           = $request->input('type');
        // $supplierid     = $request->input('supplierid');
        $mamount        = $request->input('mamount');
        $reason_remarks = $request->input('reason_remarks');
        $startdate      = $request->input('startdate');
        // dd($request->input('startdate'));
        $enddate        = $request->input('enddate');
        $updated_at     = date("Y-m-d H:i:s");
        $query = DB::table('maintenance')->where('id', $id);
        if (DB::getSchemaBuilder()->hasColumn('maintenance', 'is_delete')) {
            $query->where('is_delete', 0);
        }
        $update = $query->update(
            [
                'assetid'       => $assetid,
                'type'          => $type,
                // 'supplierid'    => $supplierid,
                'mamount'       => $mamount,
                'reason_remarks' => $reason_remarks,
                'startdate'     => $startdate,
                'enddate'       => $enddate,
                'updated_at'    => $updated_at
            ]
        );

        if ($type == "Operational") {
            $update1 = DB::table('assets')->where('id', $assetid)
                ->update(
                    [
                        'status'          => '1',
                    ]
                );
        }


        if ($update) {
            $res['success'] = 'success';
        } else {
            $res['success'] = 'failed';
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
        $delete = DB::table('maintenance')->where('id', $id)->where('is_delete', 0)
            ->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")]);
        if ($delete) {
            $res['success'] = 'success';
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }
}
