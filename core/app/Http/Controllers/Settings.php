<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SettingModel;
use App\Http\Controllers\TraitSettings;
use DB;
use App;
use Auth;


class Settings extends Controller
{
    use TraitSettings;

	public function __construct() {
		$data = $this->getapplications();
		$lang = $data->language;
		App::setLocale($lang);
		$this->middleware( 'auth' );
	}
	

	 //return settings
	 public function index() {
		return view( 'setting.index' );
    }
    
    /**
	 * get application settings
	 *
	 * @return object
	 */
	public function getdata() {
		$data = DB::table('settings')->where('id', '1')->first();
		if ($data) {

			$res['success'] = true;
			$res['data']  = $data;
			$res['logo']  = url('/').'/upload/'.$data->logo;
			$res['message'] = 'success';
			return response($res);
		}
    }
    

    /**
	 * update application settings to database
	 *
	 * @param string  $company
	 * @param string  $phone
	 * @param string  $city
	 * @param string  $website
	 * @param string  $address
	 * @param string  $currency
	 * @param string  $language
	 * @param string  $dateformat
	 * @return object
	 */
	public function update(Request $request){
        $company      = $request->input('company');
        $address      = $request->input('address');
		$email       = $request->input('email');
		$phonenumber = $request->input('phonenumber');
        $country    = $request->input('country');
        $logoname2  = $request->file('logo');
		$currency   = $request->input('currency');
		$language   = $request->input('language');
		$formatdate = $request->input('formatdate');
		

		$message = ['logo.mimes'=>trans('lang.type_image')];

		if ($request->hasFile('logo')) {
			$this->validate($request, [
				'logo' => 'image|mimes:jpeg,png,jpg|max:2048'
				],$message);
			$logoname  = $request->file('logo')->getClientOriginalName();
			$request->file('logo')->move(public_path("/upload"), $logoname);
			$update = DB::table('settings')->where('id', '1')
			->update(
				[
				'company'       =>$company,
				'address'       =>$address,
				'email'         =>$email,
				'phonenumber'   =>$phonenumber,
				'country'   	=>$country,
                'currency'      =>$currency,
                'language'      =>$language,
				'formatdate'    =>$formatdate,
				'logo'          =>$logoname
				]
			);
		} else{

			$update = DB::table('settings')->where('id', '1')->update(
				[
                    'company'       =>$company,
                    'address'       =>$address,
                    'email'         =>$email,
                    'phonenumber'   =>$phonenumber,
                    'country'   	=>$country,
                    'currency'      =>$currency,
                    'language'      =>$language,
                    'formatdate'    =>$formatdate
				]
			);
		}

		if ( $update ) {
			$res['message'] = 'success';
			
        } else{
            $res['message'] = 'failed';
        }

        return response( $res );


	}

}
