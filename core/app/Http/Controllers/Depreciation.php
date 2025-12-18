<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DepreciationModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;

class Depreciation extends Controller
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
		return view( 'depreciation.index' );
    }

    /**
	 * get data from database
	 * @return object
	 */
    public function getdata(){
        $data = DB::table('depreciation')
                ->select('depreciation.*', 'assets.name as asset', 'assets.id as assetid', 'assets.cost as assetcost', 'component.id as componentid', 'component.name as component', 'component.cost as componentcost')
                ->leftjoin('assets', 'depreciation.assetid', '=', 'assets.id')
                ->leftjoin('component', 'depreciation.componentid', '=', 'component.id');
		return Datatables::of($data)
        ->addColumn('name', function ( $accountsingle ) {
            if($accountsingle->asset !='' || $accountsingle->asset !=NULL){
                $name =  $accountsingle->asset;
            }else if($accountsingle->component !='' || $accountsingle->component !=NULL){
                $name =  $accountsingle->component;
            }else{
                $name =  '-';
            }
            return $name;
        })
        ->addColumn('category', function ( $accountsingle ) {
                if($accountsingle->assetid !='' || $accountsingle->assetid !=NULL){
                    $cat =  trans('lang.asset');
                }else if($accountsingle->componentid !='' || $accountsingle->componentid !=NULL){
                    $cat =  trans('lang.component');
                }else{
                    $cat =  '-';
                }

                return $cat;
        })
        ->addColumn('cost', function ( $accountsingle ) {
            if($accountsingle->assetcost !='' || $accountsingle->assetcost !=NULL){
                $cost = $accountsingle->assetcost;
            }else if($accountsingle->componentid !='' || $accountsingle->componentid !=NULL){
                $cost = $accountsingle->componentcost;
            }else{
                $cost = '-';
            }
            return $cost;
        })
		->addColumn( 'action', function ( $accountsingle ) {
            return '<a href="#" id="btnedit" customdata='.$accountsingle->id.' class="btn btn-sm btn-primary" data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i> '. trans('lang.edit').'</a>
                    <a href="#" id="btndelete" customdata='.$accountsingle->id.' class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i> '. trans('lang.delete').'</a>';
        } )->make( true );		
    }

    /**
	 * get all  from database
	 * @return object
	 */
    public function getrows(){
        $data = DB::table('depreciation')->get();
        if ( $data ) {
			$res['success'] = true;
			$res['message']= $data;
        }
        return response( $res );
    }

    /**
	 * get single data 
	 * @param integer $id
	 * @return object
	 */

    public function byid( Request $request ) {
        $id            = $request->input( 'id' );

        $data = DB::table('depreciation')
                ->select('depreciation.*', 'assets.name as asset', 'component.name as component','assets.cost as assetcost','component.cost as componentcost')
                ->leftjoin('assets', 'depreciation.assetid', '=', 'assets.id')
                ->leftjoin('component', 'depreciation.componentid', '=', 'component.id')
                ->where('depreciation.id', $id)->first();
        
        if ( $data ) {
			$res['success'] = 'success';
			$res['message']= $data;
        } else{
            $res['success'] = 'failed';
        }
        return response( $res );
        
    }

    /**
	 * insert data  to database
	 *
	 * @param int $assetid
     * @param int $componentid
     * @param string  $period
     * @param string $value
	 * @return object
	 */
    public function save(Request $request){ 
        $assetid            = $request->input( 'asset' );
        $componentid        = $request->input( 'component' );
        $period             = $request->input( 'period' );
        $assetvalue         = $request->input( 'assetvalue' );
        $created_at         = date("Y-m-d H:i:s");
        $updated_at         = date("Y-m-d H:i:s");

        $data           = array('assetid'=>$assetid, 'componentid'=>$componentid,'period'=>$period,'assetvalue'=>$assetvalue,'created_at'=>$created_at, 'updated_at'=>$updated_at);
		$insert         = DB::table( 'depreciation' )->insert( $data );

		if ( $insert ) {
			$res['success'] = 'success';
			
        } else{
            $res['success'] = 'failed';
        }
        
        return response( $res );
    }

    /**
	 * update data  to database
	 *
	 * @param int $assetid
     * @param int $componentid
     * @param string  $period
     * @param string $value
	 * @return object
	 */
    public function update(Request $request){
        $id                 = $request->input( 'id' );
        $period             = $request->input( 'editperiod' );
        $assetvalue         = $request->input( 'editassetvalue' );
        $updated_at         = date("Y-m-d H:i:s");

		$update = DB::table( 'depreciation' )->where( 'id', $id )
		->update(
			[
            'period'        => $period,
            'assetvalue'    => $assetvalue,
            'updated_at'    => $updated_at
			]
		);
        
        if ( $update ) {
			$res['success'] = 'success';
			
        } else{
            $res['success'] = 'failed';
        }

        return response( $res );
    }

     /**
	 * delete to database
	 *
	 * @param integer $id
	 * @return object
	 */

	public function delete( Request $request ) {
		$id = $request->input( 'id' );
		$delete = DB::table( 'depreciation' )->where( 'id', $id )->delete();
            if ( $delete ) {
                $res['success'] = 'success';
            } else{
                $res['success'] = 'failed';
            }
		return response( $res );
	}


    /**
	 * get depreciation detail frp, database
	 *
	 * @param integer $id
	 * @return object
	 */
    public function calculator(Request $request){
        //Declaration of variables, jangan pake datatable, pake table biasa aja
        $id = $request->input( 'id' );
        $data = DB::table('depreciation')
                ->select('depreciation.*', 'assets.id as assetid', 'assets.cost as assetcost')
                ->leftjoin('assets', 'depreciation.assetid', '=', 'assets.id')
                ->where('depreciation.assetid', $id)->first();

                if ( $data ) {
                    $res['success'] = 'success';
                    $res['message']= $data;
                } else{
                    $res['success'] = 'failed';
                }
        return response ($res);

      
    }

    /**
	 * get depreciation detail frp, database
	 *
	 * @param integer $id
	 * @return object
	 */
    public function calculatorcomponent(Request $request){
        //Declaration of variables, jangan pake datatable, pake table biasa aja
        $id = $request->input( 'id' );
        $data = DB::table('depreciation')
                ->select('depreciation.*', 'component.id as componentid', 'component.cost as componentcost')
                ->leftjoin('component', 'depreciation.componentid', '=', 'component.id')
                ->where('depreciation.componentid', $id)->first();

                if ( $data ) {
                    $res['success'] = 'success';
                    $res['message']= $data;
                } else{
                    $res['success'] = 'failed';
                }
        return response ($res);

      
    }
}
