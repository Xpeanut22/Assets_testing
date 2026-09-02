<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BrandModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use DB;
use App\User;
use App;
use Auth;

class Brand extends Controller
{
    use TraitSettings;
    use TraitAuditTrail;

    public function __construct() {
		
		$data = $this->getapplications();
		$lang = $data->language;
		App::setLocale($lang);
        $this->middleware('auth');
    }

    //return view
    public function index() {
		return view( 'brand.index' );
    }

    /**
	 * get data from database
	 * @return object
	 */
    public function getdata(){
        $data = DB::table('brand')->select(['brand.*']);
        if (DB::getSchemaBuilder()->hasColumn('brand', 'is_delete')) {
            $data->where('is_delete', 0);
        }
		return Datatables::of($data)
		->addColumn( 'action', function ( $accountsingle ) {
            return '<a href="#" id="btnedit" customdata='.$accountsingle->id.' class="btn btn-sm btn-primary" data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i> '. trans('lang.edit').'</a>
                    <a href="#" id="btndelete" customdata='.$accountsingle->id.' class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i> '. trans('lang.delete').'</a>';
        } )->make( true );		
    }

    /**
	 * get all  from database
	 * @return object
	 */
    // public function getrows(){
    //     $data = DB::table('brand')->get();
    //     if ( $data ) {
	// 		$res['success'] = true;
	// 		$res['message']= $data;
    //     }
    //     return response( $res );
    // }

    public function getrows() {
        $query = DB::table('brand')->where('type', 'tools');
        if (DB::getSchemaBuilder()->hasColumn('brand', 'is_delete')) {
            $query->where('is_delete', 0);
        }
        $data = $query->get();
    
        $res = ['success' => false, 'message' => 'No data found'];
    
        if ($data->isNotEmpty()) {
            $res['success'] = true;
            $res['message'] = $data;
        }
    
        return response()->json($res);
    }

    public function listofvehiclebrand() {
        $query = DB::table('brand')->where('type', 'vehicle');
        if (DB::getSchemaBuilder()->hasColumn('brand', 'is_delete')) {
            $query->where('is_delete', 0);
        }
        $data = $query->get();
    
        $res = ['success' => false, 'message' => 'No data found'];
    
        if ($data->isNotEmpty()) {
            $res['success'] = true;
            $res['message'] = $data;
        }
    
        return response()->json($res);
    }

    /**
	 * get single data 
	 * @param integer $id
	 * @return object
	 */

    public function byid( Request $request ) {
        $id            = $request->input( 'id' );

        $data = DB::table('brand')->where('id', $id)->where('is_delete', 0)->first();
        
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
	 * @param string  $name
     * @param string  $description
	 * @return object
	 */
    public function save(Request $request){
        $name           = $request->input( 'name' );
        $description    = $request->input( 'description' );
        $type    = $request->input( 'brandtype' );
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $data           = array('name'=>$name, 'description'=>$description,'type'=>$type,'is_delete'=>0,'created_at'=>$created_at, 'updated_at'=>$updated_at);
		$insert         = DB::table( 'brand' )->insert( $data );

		if ( $insert ) {
			$res['success'] = 'success';
			$this->auditTrail('Utilities', 'Create', 'Created brand: '.$name.'.', 'Brand', null, null, ['name' => $name, 'description' => $description, 'type' => $type]);
        } else{
            $res['success'] = 'failed';
        }
        
        return response( $res );
    }

    /**
	 * update data  to database
	 *
	 * @param string  $name
     * @param string  $description
	 * @return object
	 */
    public function update(Request $request){
        $id             = $request->input( 'id' );
        $name           = $request->input( 'name' );
        $description    = $request->input( 'description' );
        $type    = $request->input( 'brandtype' );
        $updated_at     = date("Y-m-d H:i:s");

        $oldBrand = DB::table('brand')->where('id', $id)->where('is_delete', 0)->first();

		$update = DB::table( 'brand' )->where( 'id', $id )->where('is_delete', 0)
		->update(
			[
			'name'          => $name,
            'description'   => $description,
            'updated_at'    => $updated_at
			]
		);
        
        if ( $update ) {
			$res['success'] = 'success';
			$diff = $this->auditCalculateDiff($oldBrand, ['name' => $name, 'description' => $description, 'type' => $type], ['name' => 'Name', 'description' => 'Description', 'type' => 'Type']);
			$detailsText = 'Updated brand: '.$name.($diff['details'] ? ":\n" . $diff['details'] : '');
			$this->auditTrail('Utilities', 'Update', $detailsText, 'Brand', $id, $diff['old'], $diff['new']);
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
		$brand = DB::table('brand')->where('id', $id)->where('is_delete', 0)->first();
		$delete = DB::table( 'brand' )->where( 'id', $id )->where('is_delete', 0)->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")]);
            if ( $delete ) {
                $res['success'] = 'success';
                $this->auditTrail('Utilities', 'Delete', 'Deleted brand ID: '.$id.'.', 'Brand', $id, ['name' => $brand->name ?? '-', 'description' => $brand->description ?? '-', 'type' => $brand->type ?? '-'], null);
            } else{
                $res['success'] = 'failed';
            }
		return response( $res );
	}
}
