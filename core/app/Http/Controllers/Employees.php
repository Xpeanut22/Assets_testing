<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmployeesModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;


class Employees extends Controller
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
        return view('employee.index');
    }

    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $data = DB::select("select employees.*, department.name as department 
        from employees left join department 
        on employees.departmentid = department.id order by employees.created_at desc");
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
        $data = DB::table('employees')->get();
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

        $data = DB::table('employees')->where('id', $id)->first();

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
        $fullname       = $request->input('fullname');
        $email          = $request->input('email');
        $number          = $request->input('number');
        $department     = $request->input('department');
        $jobrole        = $request->input('jobrole');
        $city           = $request->input('city');
        $country        = $request->input('country');
        $address        = $request->input('address');
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");


        $emailcheck = DB::table('employees')
            ->where('email', '=', $email)
            ->first();

        if ($emailcheck) {
            $res['message'] = 'exist';
        } else {


            $data       = array(
                'fullname' => $fullname,
                'email' => $email,
                'mobile_number' => $number,
                'jobrole' => $jobrole,
                'departmentid' => $department,
                'country' => $country,
                'city' => $city,
                'address' => $address,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            );

            $insert     = DB::table('employees')->insert($data);


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
        $fullname       = $request->input('fullname');
        $email          = $request->input('email');
        $number          = $request->input('number');
        $department     = $request->input('department');
        $jobrole        = $request->input('jobrole');
        $city           = $request->input('city');
        $country        = $request->input('country');
        $address        = $request->input('address');
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");

        $emailcheck = DB::table('employees')
            ->where('email', '=', $email)
            ->where('id', '!=', $id)
            ->first();

        if ($emailcheck) {
            $res['message'] = 'exist';
        } else {

            $update = DB::table('employees')->where('id', $id)
                ->update(
                    [
                        'fullname'          => $fullname,
                        'email'             => $email,
                        'mobile_number'     => $number,
                        'departmentid'      => $department,
                        'jobrole'           => $jobrole,
                        'city'              => $city,
                        'country'           => $country,
                        'address'           => $address,
                        'updated_at'        => $updated_at
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

        $delete = DB::table('employees')->where('id', $id)->delete();

        if ($delete) {
            $res['success'] = 'success';
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }
}
