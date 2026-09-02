<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SupplierModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use App\Http\Controllers\TraitAuditTrail;
use DB;
use App\User;
use App;
use Auth;
class Supplier extends Controller
{
    use TraitSettings;
    use TraitAuditTrail;

    public function __construct() {
		
		$data = $this->getapplications();
		$lang = $data->language;
		App::setLocale($lang);
        $this->middleware('auth');
    }

    //return page view
    public function index() {
		return view( 'supplier.index' );
    }

    /**
	 * get data from database
	 * @return object
	 */
    public function getdata(){
        $data = DB::table('supplier')->select(['supplier.*']);
        if (DB::getSchemaBuilder()->hasColumn('supplier', 'is_delete')) {
            $data->where('is_delete', 0);
        }
        return Datatables::of($data)
       
		->addColumn( 'action', function ( $accountsingle ) {
            return '<a href="#" id="btnedit" customdata='.$accountsingle->id.' class="btn btn-sm btn-primary" data-toggle="modal" data-target="#edit"><i class="fa fa-pencil"></i> '. trans('lang.edit').'</a>
                    <a href="#" id="btndelete" customdata='.$accountsingle->id.' class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i> '. trans('lang.delete').'</a>';
        } )->rawColumns(['gender','picture', 'action'])
        ->make(true);		
    }


    /**
	 * get all  from database
	 * @return object
	 */
    public function getrows(){
        $query = DB::table('supplier');
        if (DB::getSchemaBuilder()->hasColumn('supplier', 'is_delete')) {
            $query->where('is_delete', 0);
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

        $data = DB::table('supplier')->where('id', $id)->where('is_delete', 0)->first();
        
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
	 * @param string  $email
     * @param string  $zip
     * @param string  $address
     * @param string  $city
     * @param string  $country
     * @param string  $phone
     * @return object
	 */
    public function save(Request $request){
        $name           = $request->input( 'name' );
        $email          = $request->input( 'email' );
        $phone          = $request->input( 'phone' );
        $zip            = $request->input( 'zip' );
        $city           = $request->input( 'city' );
        $country        = $request->input( 'country' );
        $address        = $request->input( 'address' );
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
      

        $emailcheck = DB::table('supplier')
		    ->where('email', '=', $email)
            ->where('is_delete', 0)
            ->first();
        
        if($emailcheck){
            $res['message'] = 'exist';  
        }
        else{ 
          
          
                $data       = array('name'=>$name, 
                            'email'=>$email,
                            'zip'=>$zip,
                            'phone'=>$phone,
                            'address'=>$address,
                            'country'=>$country,
                            'city'=>$city,
                            'address'=>$address,
                            'is_delete'=>0,
                            'created_at'=>$created_at,
                            'updated_at'=>$updated_at);

                $insert     = DB::table( 'supplier' )->insert( $data );


            if ( $insert ) {
                $res['message'] = 'success';
                $this->auditTrail('Utilities', 'Create', 'Created supplier: '.$name.'.', 'Supplier', null, null, ['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address]);
            } else{
                $res['message'] = 'failed';
            }

        }

        return response( $res );
    }

    /**
	 * update data  to database
	 *
	 * @param string  $name
	 * @param string  $email
     * @param string  $zip
     * @param string  $address
     * @param string  $city
     * @param string  $country
     * @param string  $phone
	 * @return object
	 */
    public function update(Request $request){
        $id             = $request->input( 'id' );
        $name           = $request->input( 'name' );
        $email          = $request->input( 'email' );
        $phone          = $request->input( 'phone' );
        $zip            = $request->input( 'zip' );
        $city           = $request->input( 'city' );
        $country        = $request->input( 'country' );
        $address        = $request->input( 'address' );
        $updated_at     = date("Y-m-d H:i:s");
      
        $emailcheck = DB::table('supplier')
        ->where('email', '=', $email)
        ->where('id', '!=', $id)
        ->where('is_delete', 0)
        ->first();
    
        if($emailcheck){
                $res['message'] = 'exist';  
        } 
        else{
            $oldSupplier = DB::table('supplier')->where('id', $id)->where('is_delete', 0)->first();

            $update = DB::table( 'supplier' )->where( 'id', $id )->where('is_delete', 0)
            ->update(
                [
                'name'              => $name,
                'email'             => $email,
                'zip'               => $zip,
                'phone'             => $phone,
                'city'              => $city,
                'country'           => $country,
                'address'           => $address,
                'updated_at'        => $updated_at
                ]
            );

            if ( $update ) {
                $res['message'] = 'success';
                $diff = $this->auditCalculateDiff($oldSupplier, ['name' => $name, 'email' => $email, 'phone' => $phone, 'address' => $address, 'city' => $city], ['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'address' => 'Address', 'city' => 'City']);
                $detailsText = 'Updated supplier: '.$name.($diff['details'] ? ":\n" . $diff['details'] : '');
                $this->auditTrail('Utilities', 'Update', $detailsText, 'Supplier', $id, $diff['old'], $diff['new']);
            } else{
                $res['message'] = 'failed';
            }
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
        $supplier = DB::table('supplier')->where('id', $id)->where('is_delete', 0)->first();
        $delete = DB::table( 'supplier' )->where( 'id', $id )->where('is_delete', 0)->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")]);

        if ( $delete ) {
            $res['success'] = 'success';
            $this->auditTrail('Utilities', 'Delete', 'Deleted supplier ID: '.$id.'.', 'Supplier', $id, ['name' => $supplier->name ?? '-', 'email' => $supplier->email ?? '-'], null);
        } else{
            $res['success'] = 'failed';
        }
            return response( $res );
        
	}
}
