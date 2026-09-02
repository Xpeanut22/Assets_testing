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

class Receiver extends Controller
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
		return view( 'receiver.index' );
    }

    /**
	 * get data from database
	 * @return object
	 */
    public function getdata(){
        $receiverDeleteFilter = DB::getSchemaBuilder()->hasColumn('receiver', 'is_delete') ? 'where receiver.is_delete = 0' : '';

        $data = DB::select("select receiver.*
        from receiver
        $receiverDeleteFilter");
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
        $query = DB::table('receiver');
        if (DB::getSchemaBuilder()->hasColumn('receiver', 'is_delete')) {
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

        $data = DB::table('receiver')->where('id', $id)->where('is_delete', 0)->first();
        
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
	 * @param string  $fullname
	 * @param string  $email
     * @param string  $jobrole
     * @param string  $address
     * @param string  $city
     * @param string  $country
     * @param int     $department
     * @return object
	 */
    public function save(Request $request){
        $fullname       = $request->input( 'fullname' );
        $email          = $request->input( 'email' );
        $department     = $request->input( 'department' );
        $jobrole        = $request->input( 'jobrole' );
        $city           = $request->input( 'city' );
        $country        = $request->input( 'country' );
        $address        = $request->input( 'address' );
        $created_at     = date("Y-m-d H:i:s");
        $updated_at     = date("Y-m-d H:i:s");
      

        $emailcheck = DB::table('receiver')
		    ->where('email', '=', $email)
            ->where('is_delete', 0)
            ->first();
        
        if($emailcheck){
            $res['message'] = 'exist';  
        }
        else{ 
          
          
                $data       = array('fullname'=>$fullname, 
                            'email'=>$email,
                            'jobrole'=>$jobrole,
                            'departmentid'=>$department,
                            'country'=>$country,
                            'city'=>$city,
                            'address'=>$address,
                            'is_delete'=>0,
                            'created_at'=>$created_at,
                            'updated_at'=>$updated_at);

                $insert     = DB::table( 'receiver' )->insert( $data );


            if ( $insert ) {
                $res['message'] = 'success';
                $this->auditTrail('Utilities', 'Create', 'Created receiver: '.$fullname.'.', 'Receiver', null, null, ['fullname' => $fullname, 'email' => $email, 'jobrole' => $jobrole]);
            } else{
                $res['message'] = 'failed';
            }

        }

        return response( $res );
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
    public function update(Request $request){
        $id             = $request->input( 'id' );
        $fullname       = $request->input( 'fullname' );
        $email          = $request->input( 'email' );
        $department     = $request->input( 'department' );
        $jobrole        = $request->input( 'jobrole' );
        $city           = $request->input( 'city' );
        $country        = $request->input( 'country' );
        $address        = $request->input( 'address' );
        $updated_at     = date("Y-m-d H:i:s");
      
        $emailcheck = DB::table('receiver')
        ->where('email', '=', $email)
        ->where('id', '!=', $id)
        ->where('is_delete', 0)
        ->first();
    
        if($emailcheck){
                $res['message'] = 'exist';  
        } 
        else{
            $oldReceiver = DB::table('receiver')->where('id', $id)->where('is_delete', 0)->first();

            $update = DB::table( 'receiver' )->where( 'id', $id )->where('is_delete', 0)
            ->update(
                [
                'fullname'          => $fullname,
                'email'             => $email,
                'departmentid'      => $department,
                'jobrole'           => $jobrole,
                'city'              => $city,
                'country'           => $country,
                'address'           => $address,
                'updated_at'        => $updated_at
                ]
            );

            if ( $update ) {
                $res['message'] = 'success';
                $diff = $this->auditCalculateDiff($oldReceiver, ['fullname' => $fullname, 'email' => $email, 'jobrole' => $jobrole, 'city' => $city], ['fullname' => 'Full Name', 'email' => 'Email', 'jobrole' => 'Job Role', 'city' => 'City']);
                $detailsText = 'Updated receiver: '.$fullname.($diff['details'] ? ":\n" . $diff['details'] : '');
                $this->auditTrail('Utilities', 'Update', $detailsText, 'Receiver', $id, $diff['old'], $diff['new']);
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


        //set delete if no assets to this user

        $id = $request->input( 'id' );
        $receiver = DB::table('receiver')->where('id', $id)->where('is_delete', 0)->first();
      
        $delete = DB::table( 'receiver' )->where( 'id', $id )->where('is_delete', 0)->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")]);

        if ( $delete ) {
            $res['success'] = 'success';
            $this->auditTrail('Utilities', 'Delete', 'Deleted receiver ID: '.$id.'.', 'Receiver', $id, ['fullname' => $receiver->fullname ?? '-', 'email' => $receiver->email ?? '-'], null);
        } else{
            $res['success'] = 'failed';
        }
            return response( $res );
        
	}
}
