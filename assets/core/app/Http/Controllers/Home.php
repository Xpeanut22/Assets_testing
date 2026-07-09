<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App\AssetsModel;
use App;
use Auth;

class Home extends Controller
{
   use TraitSettings;

    public function __construct() {
		
		$data = $this->getapplications();
		$lang = $data->language;
		App::setLocale($lang);
        $this->middleware('auth');
    }


	//return view
    public function index() {
		return view( 'home.index' );
    }


	/**
	 * Show total balance.
	 *
	 * @return object
	 */
	public function totalbalance() {

		$totalasset   = DB::table('assets')
		->select(DB::raw('count(*) as totalasset'))
		->first();

		$totalcomponent   = DB::table('component')
		->select(DB::raw('sum(quantity) as totalcomponent'))
		->first();

		$totalmaintenance   = DB::table('maintenance')
		->select(DB::raw('count(*) as totalmaintenance'))
		->first();

		$totalemployee   = DB::table('employees')
		->where('is_delete', 0)
		->select(DB::raw('count(*) as totalemployee'))
		->first();

		$data['totalasset'] 		= $totalasset->totalasset;
		$data['totalcomponent'] 	= $totalcomponent->totalcomponent;
		$data['totalemployee'] 		= $totalemployee->totalemployee;
		$data['totalmaintenance'] 	= $totalmaintenance->totalmaintenance;

		return response($data);
	}


	/**
	 * Show asset by type
	 *
	 * @return object
	 */
	public function assetbytype() {

		$data = DB::table('assets')
		->leftJoin('asset_type', 'asset_type.id', '=', 'assets.typeid')
		->select(DB::raw('count(*) as amount, asset_type.name as type'))
		->groupBy('asset_type.id')
		->groupBy('asset_type.name')
		->get();
		return response($data);
	}

	/**
	 * Show asset by status
	 *
	 * @return object
	 */
	public function assetbystatus() {

		$data= AssetsModel::groupBy('status')->select('status', DB::raw('count(*) as amount'))->get();

		return response($data);
	}


	/**
	 * get data recent asset from database
	 * @return object
	 */
    public function recentassetactivity(){
        $data = DB::table('asset_history')
        ->select('asset_history.*', 'assets.name as asset', 'employees.fullname as employees', 'asset_type.name as type', 'location.name as location')
        ->leftJoin('assets', 'assets.id', '=', 'asset_history.assetid')
        ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
        ->leftJoin('employees', 'employees.id', '=', 'asset_history.employeeid')
        ->leftJoin('location', 'location.id', '=', 'assets.locationid')
        ->offset(0)->limit(10)
		->orderBy('asset_history.updated_at', 'desc')
		->orderBy('asset_history.created_at', 'desc')
		->get();

        return Datatables::of($data)
        
        ->addColumn( 'status', function ( $accountsingle ) {
           

            if($accountsingle->status==2){
                    
                    $status = '<span class="badge badge-data text-white background-blue">'.trans('lang.checkin').'</span>';
                
            }else{
                    $status = '<span class="badge badge-data text-white background-yellow">'.trans('lang.checkout').'</span>';
            }

            return  $status;
           
        } )->rawColumns(['status'])
        ->make(true);		
    }

    /**
	 * get data recent component from database
	 * @return object
	 */
    public function recentcomponentactivity(){
        $data = DB::table('component_assets')
        ->select('component_assets.*', 'component.serial as cserial', 'receiver.fullname as rfullname', 'component_assets.control_number as ccontrolno', 'component.name as component', 'employees.fullname as employees','employees.mobile_number', 'employees.departmentid as deparmentid', 'department.description as departmentname' ,'assets.name as asset', 'asset_type.name as type', 'location.name as location')
        ->leftJoin('assets', 'assets.id', '=', 'component_assets.assetid')
        ->leftJoin('asset_type', 'assets.typeid', '=', 'asset_type.id')
		->leftJoin('employees', 'employees.id', '=', 'component_assets.employeeid')
		->leftJoin('department', 'department.id', '=', 'employees.departmentid')
        ->leftJoin('location', 'location.id', '=', 'assets.locationid')
        ->leftJoin('component', 'component.id', '=', 'component_assets.componentid')
        ->leftJoin('receiver', 'receiver.id', '=', 'component_assets.created_by')

		->orderBy('component_assets.updated_at', 'desc')
		->orderBy('component_assets.created_at', 'desc')
		->get();

        return Datatables::of($data)
        
        ->addColumn( 'status', function ( $accountsingle ) {
           

            if($accountsingle->status==2){
                    
                    $status = '<span class="badge badge-data text-white background-blue">'.trans('lang.checkin').'</span>';
                
            }else{
                    $status = '<span class="badge badge-data text-white background-yellow">'.trans('lang.checkout').'</span>';
            }

            return  $status;
           
        } )->rawColumns(['status'])
        ->make(true);		
    }
}
