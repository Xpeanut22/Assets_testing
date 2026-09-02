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

class Category extends Controller
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
        return view('category.index');
    }

    /**
     * get data from database
     * @return object
     */
    public function getdata()
    {
        $categoryDeleteFilter = DB::getSchemaBuilder()->hasColumn('category', 'is_delete') ? 'where is_delete = 0' : '';

        $data = DB::select("select *
        from category
        $categoryDeleteFilter");
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
        $query = DB::table('category');
        if (DB::getSchemaBuilder()->hasColumn('category', 'is_delete')) {
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

        $data = DB::table('category')->where('id', $id)->where('is_delete', 0)->first();

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
        $category       = $request->input('category');
        $description          = $request->input('description');

        $categorycheck = DB::table('category')
            ->where('category', '=', $category)
            ->where('is_delete', 0)
            ->first();

        if ($categorycheck) {
            $res['message'] = 'exist';
        } else {

            $data       = array(
                'category' => $category,
                'Description' => $description,
                'is_delete' => 0
            );

            $insert     = DB::table('category')->insert($data);

            if ($insert) {
                $res['message'] = 'success';
                $this->auditTrail('Utilities', 'Create', 'Created category: '.$category.'.', 'Category', null, null, ['category' => $category, 'description' => $description]);
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
        $category       = $request->input('category');
        $description          = $request->input('description');

        $categorycheck = DB::table('category')
            ->where('category', '=', $category)
            ->where('id', '!=', $id)
            ->where('is_delete', 0)
            ->first();

        if ($categorycheck) {
            $res['message'] = 'exist';
        } else {
            $oldCategory = DB::table('category')->where('id', $id)->where('is_delete', 0)->first();

            $update = DB::table('category')->where('id', $id)->where('is_delete', 0)
                ->update(
                    [
                        'category'          => $category,
                        'Description'             => $description,
                    ]
                );

            if ($update) {
                $res['message'] = 'success';
                $diff = $this->auditCalculateDiff($oldCategory, ['category' => $category, 'description' => $description], ['category' => 'Category', 'description' => 'Description']);
                $detailsText = 'Updated category: '.$category.($diff['details'] ? ":\n" . $diff['details'] : '');
                $this->auditTrail('Utilities', 'Update', $detailsText, 'Category', $id, $diff['old'], $diff['new']);
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
        $category = DB::table('category')->where('id', $id)->where('is_delete', 0)->first();

        $delete = DB::table('category')->where('id', $id)->where('is_delete', 0)->update(['is_delete' => 1]);

        if ($delete) {
            $res['success'] = 'success';
            $this->auditTrail('Utilities', 'Delete', 'Deleted category ID: '.$id.'.', 'Category', $id, ['category' => $category->category ?? '-', 'description' => $category->Description ?? '-'], null);
        } else {
            $res['success'] = 'failed';
        }
        return response($res);
    }
}
