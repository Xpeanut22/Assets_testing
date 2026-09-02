<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ReceiverModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use DB;
use App\User;
use App;
use Auth;

class Used extends Controller
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

    //return page view
    public function index()
    {
        return view('used.index');
    }

    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $usedDeleteFilter = DB::getSchemaBuilder()->hasColumn('used', 'is_delete') ? 'where is_delete = 0' : '';

        $data = DB::select("select *
        from used
        $usedDeleteFilter");
        return Datatables::of($data)

            ->addColumn('action', function ($accountsingle) {
                return '<a href="#" id="btnedit" customdata=' . $accountsingle->id . ' class="btn btn-sm btn-primary" data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i> ' . trans('lang.edit') . '</a>
                    <a href="#" id="btndelete" customdata=' . $accountsingle->id . ' class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i> ' . trans('lang.delete') . '</a>';
            })->rawColumns(['gender', 'picture', 'action'])
            ->make(true);
    }


    /**
     * get all  from database
     * @return object
     */
    public function getrows()
    {
        $query = DB::table('used');
        if (DB::getSchemaBuilder()->hasColumn('used', 'is_delete')) {
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

        $data = DB::table('used')->where('id', $id)->where('is_delete', 0)->first();

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
     * @param string  $fullname
     * @param string  $email
     * @param string  $jobrole
     * @param string  $address
     * @param string  $city
     * @param string  $country
     * @param int     $department
     * @return object
     */
    public function save(Request $request)
    {
        $used       = $request->input('used');
        $description          = $request->input('description');

        $categorycheck = DB::table('used')
            ->where('used', '=', $used)
            ->where('is_delete', 0)
            ->first();

        if ($categorycheck) {
            $res['message'] = 'exist';
        } else {

            $data       = array(
                'used' => $used,
                'Description' => $description,
                'is_delete' => 0
            );

            $insert     = DB::table('used')->insert($data);

            if ($insert) {
                $res['message'] = 'success';
                $this->auditTrail('Utilities', 'Create', 'Created use of equipment: '.$used.'.', 'Use of Equipment', null, null, ['name' => $used, 'description' => $description]);
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
     * @param string  $jobrole
     * @param string  $address
     * @param string  $city
     * @param string  $country
     * @param int     $department
     * @return object
     */
    public function update(Request $request)
    {
        $id             = $request->input('id');
        $used       = $request->input('editcategory');
        $description          = $request->input('editdescription');

        $categorycheck = DB::table('used')
            ->where('used', '=', $used)
            ->where('id', '!=', $id)
            ->where('is_delete', 0)
            ->first();

        if ($categorycheck) {
            $res['message'] = 'exist';
        } else {
            $oldUsed = DB::table('used')->where('id', $id)->where('is_delete', 0)->first();

            $update = DB::table('used')->where('id', $id)->where('is_delete', 0)
                ->update(
                    [
                        'used'          => $used,
                        'Description'             => $description,
                    ]
                );

            if ($update) {
                $res['message'] = 'success';
                $diff = $this->auditCalculateDiff($oldUsed, ['used' => $used, 'description' => $description], ['used' => 'Name', 'description' => 'Description']);
                $detailsText = 'Updated use of equipment: '.$used.($diff['details'] ? ":\n" . $diff['details'] : '');
                $this->auditTrail('Utilities', 'Update', $detailsText, 'Use of Equipment', $id, $diff['old'], $diff['new']);
            } else {
                $res['message'] = 'failed';
            }
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


        //set delete if no assets to this user

        $id = $request->input('id');
        $used = DB::table('used')->where('id', $id)->where('is_delete', 0)->first();

        $delete = DB::table('used')->where('id', $id)->where('is_delete', 0)->update(['is_delete' => 1]);

        if ($delete) {
            $res['success'] = 'success';
            $this->auditTrail('Utilities', 'Delete', 'Deleted use of equipment ID: '.$id.'.', 'Use of Equipment', $id, ['name' => $used->used ?? '-', 'description' => $used->Description ?? '-'], null);
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }
}
