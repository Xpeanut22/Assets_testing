<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LocationModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use DB;
use App\User;
use App;
use Auth;

class Location extends Controller
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
		return view( 'location.index' );
    }

    /**
	 * get data from database
	 * @return object
	 */
    public function getdata(){
        $data = DB::table('location')->select(['location.*']);
        if (DB::getSchemaBuilder()->hasColumn('location', 'is_delete')) {
            $data->where(function ($innerQuery) {
                $innerQuery->where('is_delete', 0)->orWhereNull('is_delete');
            });
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
    public function getrows(){
        $query = DB::table('location');
        if (DB::getSchemaBuilder()->hasColumn('location', 'is_delete')) {
            $query->where(function ($innerQuery) {
                $innerQuery->where('is_delete', 0)->orWhereNull('is_delete');
            });
        }
        $data = $query->get();
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

        $query = DB::table('location')->where('id', $id);
        if (DB::getSchemaBuilder()->hasColumn('location', 'is_delete')) {
            $query->where(function ($innerQuery) {
                $innerQuery->where('is_delete', 0)->orWhereNull('is_delete');
            });
        }
        $data = $query->first();
        
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
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
        $data           = array('name'=>$name, 'description'=>$description,'created_at'=>$created_at, 'updated_at'=>$updated_at);
        if (DB::getSchemaBuilder()->hasColumn('location', 'is_delete')) {
            $data['is_delete'] = 0;
        }
		$insert         = DB::table( 'location' )->insert( $data );

		if ( $insert ) {
			$res['success'] = 'success';
			$this->auditTrail('Utilities', 'Create', 'Created location: '.$name.'.', 'Location', null, null, ['name' => $name, 'description' => $description]);
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
        $updated_at     = date("Y-m-d H:i:s");

        $oldLocationQuery = DB::table('location')->where('id', $id);
        if (DB::getSchemaBuilder()->hasColumn('location', 'is_delete')) {
            $oldLocationQuery->where(function ($innerQuery) {
                $innerQuery->where('is_delete', 0)->orWhereNull('is_delete');
            });
        }
        $oldLocation = $oldLocationQuery->first();

        $updateQuery = DB::table('location')->where('id', $id);
        if (DB::getSchemaBuilder()->hasColumn('location', 'is_delete')) {
            $updateQuery->where(function ($innerQuery) {
                $innerQuery->where('is_delete', 0)->orWhereNull('is_delete');
            });
        }

		$update = $updateQuery->update(
			[
			'name'          => $name,
            'description'   => $description,
            'updated_at'    => $updated_at
			]
		);
        
        if ( $update ) {
			$res['success'] = 'success';
			$diff = $this->auditCalculateDiff($oldLocation, ['name' => $name, 'description' => $description], ['name' => 'Name', 'description' => 'Description']);
			$detailsText = 'Updated location: '.$name.($diff['details'] ? ":\n" . $diff['details'] : '');
			$this->auditTrail('Utilities', 'Update', $detailsText, 'Location', $id, $diff['old'], $diff['new']);
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
        $locationQuery = DB::table('location')->where('id', $id);
        $hasSoftDelete = DB::getSchemaBuilder()->hasColumn('location', 'is_delete');
        if ($hasSoftDelete) {
            $locationQuery->where(function ($innerQuery) {
                $innerQuery->where('is_delete', 0)->orWhereNull('is_delete');
            });
        }
		$location = $locationQuery->first();

        $deleteQuery = DB::table('location')->where('id', $id);
        if ($hasSoftDelete) {
            $deleteQuery->where(function ($innerQuery) {
                $innerQuery->where('is_delete', 0)->orWhereNull('is_delete');
            });
        }
		$delete = $hasSoftDelete
            ? $deleteQuery->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")])
            : $deleteQuery->delete();
            if ( $delete ) {
                $res['success'] = 'success';
                $this->auditTrail('Utilities', 'Delete', 'Deleted location ID: '.$id.'.', 'Location', $id, ['name' => $location->name ?? '-', 'description' => $location->description ?? '-'], null);
            } else{
                $res['success'] = 'failed';
            }
		return response( $res );
	}
}
