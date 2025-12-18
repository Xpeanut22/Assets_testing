<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Class needed for login and Logout logic
use Illuminate\Foundation\Auth\AuthenticatesUsers;

//Auth facade
use Auth;
use DB;
use Validator;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
      }
      
    public function authenticate(Request $request)
    {
		$email      = $request->input('email');
        $password   = $request->input('password');
        // Validations
        $rules = [
        'email'=>'required|email',
        'password'=>'required|min:4'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
        // Validation failed
        return response()->json([
            'message' => $validator->messages(),
        ]);
        } else {

        // Fetch User
        //$user = User::where('email',$request->email)->first();
        $user = Auth::attempt(['email' => $email, 'password' => $password, 'status' => '1']);
        if($user) {

                $res['success'] = 'success';
                return response($res);
        } else {
                $res['success'] = 'failed';
                $res['message']= trans('lang.invalid_login'); 
                return response($res);
        }
        }
       
    }


    /**
	 * get application settings
	 *
	 * @return object
	 */
	public function getapplication() {
		$data = DB::table('settings')->where('settingsid', '1')->first();
		if ($data) {

			$res['success'] = true;
			$res['data']  = $data;
			$res['message'] = 'list data';
			return response($res);
		}
	}
	
	public function showLoginForm()
   {
       return view('auth.login');
   }
}