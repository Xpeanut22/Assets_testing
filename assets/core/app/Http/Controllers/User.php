<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use Illuminate\Support\Facades\Hash;
use DB;
use App;
use Auth;

class User extends Controller
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
        if (DB::getSchemaBuilder()->hasColumn('users', 'is_delete')) {
            $data->where('is_delete', 0);
        }
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
        $query = DB::table('users');
        if (DB::getSchemaBuilder()->hasColumn('users', 'is_delete')) {
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

        $query = DB::table('users')->where('id', $id);
        if ($this->userHasDeleteColumn()) {
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
            ->when($this->userHasDeleteColumn(), function ($query) {
                return $query->where('is_delete', 0);
            })
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
            if ($this->userHasDeleteColumn()) {
                $data['is_delete'] = 0;
            }
            $insert     = DB::table('users')->insert($data);

            if ($insert) {
                $res['message'] = 'success';
                $this->auditTrail(
                    'User',
                    'Create',
                    'Created User "'.$fullname.'".',
                    'User',
                    $email,
                    null,
                    [
                        'Full Name' => $fullname,
                        'Email' => $email,
                        'Role' => $this->auditUserRoleName($role),
                        'Status' => $this->auditUserStatusName($status)
                    ]
                );
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
            ->when($this->userHasDeleteColumn(), function ($query) {
                return $query->where('is_delete', 0);
            })
            ->first();

        if ($emailcheck) {
            $res['message'] = 'exist';
        } else {
            $oldUserQuery = DB::table('users')->where('id', $id);
            if ($this->userHasDeleteColumn()) {
                $oldUserQuery->where('is_delete', 0);
            }
            $oldUser = $oldUserQuery->first();
            $oldValues = $oldUser ? [
                'Full Name' => $oldUser->fullname,
                'Email' => $oldUser->email,
                'Role' => $this->auditUserRoleName($oldUser->role),
                'Status' => $this->auditUserStatusName($oldUser->status),
                'City' => $oldUser->city,
                'Phone' => $oldUser->phone
            ] : null;
            $newValues = [
                'Full Name' => $fullname,
                'Email' => $email,
                'Role' => $this->auditUserRoleName($role),
                'Status' => $this->auditUserStatusName($status),
                'City' => $city,
                'Phone' => $phone
            ];
            $changedDetails = $this->auditChangedDetails($oldValues, $newValues);

            if ($password != '') {
                $updateQuery = DB::table('users')->where('id', $id);
                if ($this->userHasDeleteColumn()) {
                    $updateQuery->where('is_delete', 0);
                }
                $update = $updateQuery
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
                    $this->auditTrail('User', 'Update', 'Updated User "'.$fullname.'":'.$changedDetails, 'User', $id, $oldValues, $newValues);
                } else {
                    $res['message'] = 'failed';
                }
            } else {
                $updateQuery = DB::table('users')->where('id', $id);
                if ($this->userHasDeleteColumn()) {
                    $updateQuery->where('is_delete', 0);
                }
                $update = $updateQuery
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
                    $this->auditTrail('User', 'Update', 'Updated User "'.$fullname.'":'.$changedDetails, 'User', $id, $oldValues, $newValues);
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
        if ($this->userHasDeleteColumn()) {
            $delete = DB::table('users')->where('id', $id)->where('is_delete', 0)->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")]);
        } else {
            $delete = DB::table('users')->where('id', $id)->delete();
        }
        if ($delete) {
            $res['success'] = 'success';
            $this->auditTrail('User', 'Delete', 'Deleted user ID: '.$id.'.', 'User', $id, null, null);
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }

    private function auditUserRoleName($role)
    {
        return ((string) $role === '1') ? 'Admin' : 'User';
    }

    private function auditUserStatusName($status)
    {
        return ((string) $status === '1') ? 'Active' : 'Inactive';
    }

    private function auditChangedDetails($oldValues, $newValues)
    {
        if (!$oldValues) {
            return '';
        }

        $lines = [];
        foreach ($newValues as $key => $newValue) {
            $oldValue = isset($oldValues[$key]) ? $oldValues[$key] : null;
            if ((string) $oldValue !== (string) $newValue) {
                $lines[] = "\n- ".$key.' changed from '.$oldValue.' to '.$newValue;
            }
        }

        return count($lines) ? implode('', $lines) : "\n- No tracked field changes.";
    }

    public function requestpass(Request $request)
    {
        $roleId = 1;
        $inputPassword = $request->input('password'); // Password from request

        $usersQuery = DB::table('users')->select('password')->where('role', $roleId);
        if ($this->userHasDeleteColumn()) {
            $usersQuery->where('is_delete', 0);
        }
        $users = $usersQuery->get();

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

    private function userHasDeleteColumn()
    {
        return DB::getSchemaBuilder()->hasColumn('users', 'is_delete');
    }
}
