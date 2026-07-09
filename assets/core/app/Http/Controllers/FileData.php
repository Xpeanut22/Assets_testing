<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AssetModel;
use Illuminate\Support\Facades\File; 
use Yajra\Datatables\Datatables;
use App\Http\Controllers\TraitSettings;
use DB;
use App\User;
use App;
use Auth;
use Milon\Barcode\DNS2D;


class FileData extends Controller
{
    use TraitSettings;

    public function __construct() {
        
        $data = $this->getapplications(); 
        $lang = $data->language;
        App::setLocale($lang);
        $this->middleware('auth');
    }



    /**
     * get data from database
     * @return object
     */
    public function getdataaset(Request $request){
        $id            = $request->input( 'assetid' );
        $fileDeleteFilter = DB::getSchemaBuilder()->hasColumn('file', 'is_delete') ? 'and file.is_delete = 0' : '';

        $data = DB::select("select file.* 
        from file left join assets 
        on file.assetid = assets.id
        where file.assetid = '$id'
        $fileDeleteFilter
        order by file.created_at desc"); 
        return Datatables::of($data)
        ->addColumn('filename',function($single){
            return '<a target="_blank" href="'.url('/').'/upload/assets/'.$single->filename.'"/>'.$single->filename.'</a>';
        })
        ->addColumn( 'action', function ( $accountsingle ) {
            return '<a href="#" id="btndelete" customdata='.$accountsingle->id.' class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete"><i class="fa fa-trash"></i> '. trans('lang.delete').'</a>';
           
        } )->rawColumns(['filename', 'action'])
        ->make(true);       
    }

    /**
     * get data from database
     * @return object
     */
    public function getdatacomponent(Request $request){
        $id            = $request->input( 'componentid' );
        $fileDeleteFilter = DB::getSchemaBuilder()->hasColumn('file', 'is_delete') ? 'and file.is_delete = 0' : '';

        $data = DB::select("select file.* 
        from file left join component 
        on file.componentid = component.id
        where file.componentid = '$id'
        $fileDeleteFilter
        order by file.created_at desc"); 
        return Datatables::of($data)
        ->addColumn('filename',function($single){
            return '<a target="_blank" href="'.url('/').'/upload/assets/'.$single->filename.'"/>'.$single->filename.'</a>';
        })
        ->addColumn( 'action', function ( $accountsingle ) {
            return '<a href="#" id="btndelete" customdata='.$accountsingle->id.' class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deletefile"><i class="fa fa-trash"></i> '. trans('lang.delete').'</a>';
           
        } )->rawColumns(['filename', 'action'])
        ->make(true);       
    }



    /**
     * insert data  to database
     *
     * @param integer  $assetid
     * @param integer  $componentid
     * @param integer  $name
     * @param string  $filename
     * @return object
     */
    public function save(Request $request){
        $assetid            = $request->input( 'assetid' );
        $componentid        = $request->input( 'componentid' );
        $name               = $request->input( 'name' );
        $filename           = $request->file( 'filename' );
        $created_at         = date("Y-m-d H:i:s");
        $updated_at         = date("Y-m-d H:i:s");
        $message            = ['filename.mimes'=>trans('lang.upload_error')];

      
            if($request->hasFile('filename')) {
                $this->validate($request, ['filename' => 'mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx, application/vnd.ms-excel|max:2048'],$message);
                $filename  = date('mdYHis').uniqid().'_'.$request->file('filename')->getClientOriginalName();
                $request->file('filename')->move(public_path("/upload/assets"), $filename);
                $data       = array('assetid'=>$assetid, 
                            'componentid'=>$componentid,
                            'name'=>$name,
                            'filename'=>$filename,
                            'is_delete'=>0,
                            'created_at'=>$created_at,
                            'updated_at'=>$updated_at);
                $insert     = DB::table( 'file' )->insert( $data ); 
            }

            if ( $insert ) {
                $res['message'] = 'success';
                
            } else{
                $res['message'] = 'failed';
            }
       
        return response( $res );
    }


    /**
     * get all  from database
     * @return object
     */
    public function getrows(){
        $data = DB::table('file')->where('is_delete', 0)->get();
        if ( $data ) {
            $res['success'] = true;
            $res['message']= $data;
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

        $delete = DB::table( 'file' )->where( 'id', $id )->where('is_delete', 0)
            ->update(['is_delete' => 1, 'updated_at' => date("Y-m-d H:i:s")]);
                if ( $delete ) {
                    $res['success'] = 'success';
                } else{
                    $res['success'] = 'failed';
                }
            return response( $res );
        
    }

}
