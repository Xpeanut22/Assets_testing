<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmployeesModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use DB;
use App\User;
use App;
use Auth;


class Employees extends Controller
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
        return view('employee.index');
    }

    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $employeeDeleteFilter = DB::getSchemaBuilder()->hasColumn('employees', 'is_delete') ? 'where employees.is_delete = 0' : '';

        $data = DB::select("select employees.*, department.name as department 
        from employees left join department 
        on employees.departmentid = department.id
        $employeeDeleteFilter
        order by employees.created_at desc");
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
        $query = DB::table('employees');
        if (DB::getSchemaBuilder()->hasColumn('employees', 'is_delete')) {
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

        $data = DB::table('employees')->where('id', $id)->where('is_delete', 0)->first();

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
            ->where('is_delete', 0)
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
                'is_delete' => 0,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            );

            $insert     = DB::table('employees')->insert($data);


            if ($insert) {
                $res['message'] = 'success';
                $departmentName = DB::table('department')->where('id', $department)->value('name');
                $this->auditTrail('Utilities', 'Create', 'Created client: '.$fullname.'.', 'Client', null, null, [
                    'fullname' => $fullname,
                    'email' => $email,
                    'mobile_number' => $number,
                    'jobrole' => $jobrole,
                    'department' => $departmentName ?: '-',
                    'city' => $city,
                    'country' => $country,
                    'address' => $address
                ]);
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
            ->where('is_delete', 0)
            ->first();

        if ($emailcheck) {
            $res['message'] = 'exist';
        } else {
            $oldEmployee = DB::table('employees')->where('id', $id)->where('is_delete', 0)->first();

            $update = DB::table('employees')->where('id', $id)->where('is_delete', 0)
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
                $departmentName = DB::table('department')->where('id', $department)->value('name');
                $diff = $this->auditCalculateDiff($oldEmployee, [
                    'fullname' => $fullname,
                    'email' => $email,
                    'mobile_number' => $number,
                    'departmentid' => $department,
                    'jobrole' => $jobrole,
                    'city' => $city,
                    'country' => $country,
                    'address' => $address
                ], [
                    'fullname' => 'Full Name',
                    'email' => 'Email',
                    'mobile_number' => 'Mobile Number',
                    'departmentid' => 'Department',
                    'jobrole' => 'Job Role',
                    'city' => 'City',
                    'country' => 'Country',
                    'address' => 'Address'
                ]);

                if (isset($diff['new']['Department'])) {
                    $diff['new']['Department'] = $departmentName ?: '-';
                }

                if (isset($diff['old']['Department'])) {
                    $oldDepartmentName = DB::table('department')->where('id', $oldEmployee->departmentid ?? null)->value('name');
                    $diff['old']['Department'] = $oldDepartmentName ?: '-';
                }

                $detailsText = 'Updated client: '.$fullname.($diff['details'] ? ":\n" . $diff['details'] : '');
                $this->auditTrail('Utilities', 'Update', $detailsText, 'Client', $id, $diff['old'], $diff['new']);
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
        $employee = DB::table('employees')->where('id', $id)->where('is_delete', 0)->first();

        $delete = DB::table('employees')->where('id', $id)->where('is_delete', 0)
            ->update([
                'is_delete' => 1,
                'updated_at' => date("Y-m-d H:i:s")
            ]);

        if ($delete) {
            $res['success'] = 'success';
            $departmentName = DB::table('department')->where('id', $employee->departmentid ?? null)->value('name');
            $this->auditTrail('Utilities', 'Delete', 'Deleted client ID: '.$id.'.', 'Client', $id, [
                'fullname' => $employee->fullname ?? '-',
                'email' => $employee->email ?? '-',
                'mobile_number' => $employee->mobile_number ?? '-',
                'jobrole' => $employee->jobrole ?? '-',
                'department' => $departmentName ?: '-'
            ], null);
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }
}
