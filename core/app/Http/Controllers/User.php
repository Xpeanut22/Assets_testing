<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use Illuminate\Support\Facades\Hash;
use DB;
use App;
use Auth;

class User extends Controller
{
    use TraitSettings;

    public function __construct()
    {

        $data = $this->getapplications();
        $lang = $data->language;
        App::setLocale($lang);
        $this->middleware('auth');
    }

    //return Branch
    public function index()
    {
        return view('user.index');
    }


    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $data = DB::table('users')->select(['users.*']);
        return Datatables::of($data)
            ->addColumn('status', function ($single) {
                $status = '';
                if ($single->status == '1') {
                    $status = '<label class="badge badge-success">' . trans('lang.active') . '</label>';
                }
                if ($single->status == '2') {
                    $status = '<label class="badge badge-warning">' . trans('lang.inactive') . '</label>';
                }
                return $status;
            })
            ->addColumn('role', function ($single) {
                $status = '';
                if ($single->role == '1') {
                    $status = trans('lang.admin');
                }
                if ($single->role == '2') {
                    $status = trans('lang.user');
                }
                return $status;
            })
            ->addColumn('action', function ($accountsingle) {
                return '<a href="#" id="btnedit" customdata=' . $accountsingle->id . ' class="btn btn-sm btn-primary" data-toggle="modal" data-target="#edit"><i class="ti-pencil"></i> ' . trans('lang.edit') . '</a>
                    <a href="#" id="btndelete" customdata=' . $accountsingle->id . ' class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="ti-trash"></i> ' . trans('lang.delete') . '</a>';
            })->rawColumns(['status', 'role', 'action'])
            ->make(true);
    }


    /**
     * get all  from database
     * @return object
     */
    public function getrows()
    {
        $data = DB::table('users')->get();
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

        $data = DB::table('users')->where('id', $id)->first();

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
     * @param string  $password
     * @param string  $status
     * @param string  $city
     * @param string  $phone
     * @param string  $role
     * @return object
     */
    public function save(Request $request)
    {
        $fullname       = $request->input('fullname');
        $email          = $request->input('email');
        $password       = $request->input('password');
        $status         = $request->input('status');
        $city           = $request->input('city');
        $phone          = $request->input('phone');
        $role           = $request->input('role');
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");


        $emailcheck = DB::table('users')
            ->where('email', '=', $email)
            ->first();

        if ($emailcheck) {
            $res['message'] = 'exist';
        } else {

            $data       = array(
                'fullname' => $fullname,
                'email' => $email,
                'password' => bcrypt($password),
                'status' => $status,
                'role' => $role,
                'city' => $city,
                'phone' => $phone,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            );
            $insert     = DB::table('users')->insert($data);

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
     * @param string  $password
     * @param string  $status
     * @param string  $city
     * @param string  $phone
     * @param string  $role
     * @return object
     */
    public function update(Request $request)
    {
        $id             = $request->input('id');
        $fullname       = $request->input('fullname');
        $email          = $request->input('email');
        $password       = $request->input('password');
        $status         = $request->input('status');
        $city           = $request->input('city');
        $phone          = $request->input('phone');
        $role           = $request->input('role');
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");



        $emailcheck = DB::table('users')
            ->where('email', '=', $email)
            ->where('id', '!=', $id)
            ->first();

        if ($emailcheck) {
            $res['message'] = 'exist';
        } else {

            if ($password != '') {
                $update = DB::table('users')->where('id', $id)
                    ->update(
                        [
                            'fullname'          => $fullname,
                            'email'             => $email,
                            'password'          => bcrypt($password),
                            'status'            => $status,
                            'city'              => $city,
                            'phone'             => $phone,
                            'role'              => $role,
                            'updated_at'        => $updated_at
                        ]
                    );

                if ($update) {
                    $res['message'] = 'success';
                } else {
                    $res['message'] = 'failed';
                }
            } else {
                $update = DB::table('users')->where('id', $id)
                    ->update(
                        [
                            'fullname'          => $fullname,
                            'email'             => $email,
                            'status'            => $status,
                            'city'              => $city,
                            'phone'             => $phone,
                            'role'              => $role,
                            'updated_at'        => $updated_at
                        ]
                    );

                if ($update) {
                    $res['message'] = 'success';
                } else {
                    $res['message'] = 'failed';
                }
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
        $id = $request->input('id');
        $delete = DB::table('users')->where('id', $id)->delete();
        if ($delete) {
            $res['success'] = 'success';
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }

    public function requestpass(Request $request)
    {
        $roleId = 1;
        $inputPassword = $request->input('password'); // Password from request

        $users = DB::table('users')->select('password')->where('role', $roleId)->get();

        $matched = false;

        foreach ($users as $user) {
            if (Hash::check($inputPassword, $user->password)) {
                $matched = true;
                break;
            }
        }

        if ($matched) {
            $res['success'] = 'success';
            $res['message'] = 'Password is correct';
        } else {
            $res['success'] = 'failed';
            $res['message'] = 'Incorrect password';
        }

        return response($res);
    }
}
