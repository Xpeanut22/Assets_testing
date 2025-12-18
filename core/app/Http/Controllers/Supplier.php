<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SupplierModel;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;
class Supplier extends Controller
{
    use TraitSettings;

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
        $data = DB::table('supplier')->get();
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

        $data = DB::table('supplier')->where('id', $id)->first();
        
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
                            'created_at'=>$created_at,
                            'updated_at'=>$updated_at);

                $insert     = DB::table( 'supplier' )->insert( $data );


            if ( $insert ) {
                $res['message'] = 'success';
                
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
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
      
        $emailcheck = DB::table('supplier')
        ->where('email', '=', $email)
        ->where('id', '!=', $id)
        ->first();
    
        if($emailcheck){
                $res['message'] = 'exist';  
        } 
        else{

            $update = DB::table( 'supplier' )->where( 'id', $id )
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
        $delete = DB::table( 'supplier' )->where( 'id', $id )->delete();

        if ( $delete ) {
            $res['success'] = 'success';
        } else{
            $res['success'] = 'failed';
        }
            return response( $res );
        
	}
}
