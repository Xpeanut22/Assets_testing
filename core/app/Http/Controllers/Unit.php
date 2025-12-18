<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ReceiverModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;

class Unit extends Controller
{
    use TraitSettings;

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
        return view('unit.index');
    }

    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $data = DB::select("select *
        from units ");
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
        $data = DB::table('units')->get();
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

        $data = DB::table('units')->where('id', $id)->first();

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
        $unit       = $request->input('unit');
        $description          = $request->input('description');

        $unitcheck = DB::table('units')
            ->where('unit', '=', $unit)
            ->first();

        if ($unitcheck) {
            $res['message'] = 'exist';
        } else {

            $data       = array(
                'unit' => $unit,
                'Description' => $description
            );

            $insert     = DB::table('units')->insert($data);

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
        $unit       = $request->input('unit');
        $description          = $request->input('description');

        $unitcheck = DB::table('units')
            ->where('unit', '=', $unit)
            ->where('id', '!=', $id)
            ->first();

        if ($unitcheck) {
            $res['message'] = 'exist';
        } else {

            $update = DB::table('units')->where('id', $id)
                ->update(
                    [
                        'unit'          => $unit,
                        'Description'             => $description,
                    ]
                );

            if ($update) {
                $res['message'] = 'success';
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

        $delete = DB::table('units')->where('id', $id)->delete();

        if ($delete) {
            $res['success'] = 'success';
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }
}
