<?php

namespace App\Http\Controllers\store\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\admin\Mst_store;


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
    protected $redirectTo = 'store/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function showLoginForm()
    {
        return view('store.auth.login');
    }


    public function usrlogin(Request $request)
    {
       

        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
             
            return $this->sendLoginResponse($request);

        }

        return $this->sendFailedLoginResponse($request);
    }

   protected function validateLogin(Request $request)
{
    $this->validate($request, [
        $this->username() => 'exists:mst_stores,' . $this->username() . ',store_account_status,1',
        'password' => 'required|string',
    ], [
        $this->username() . '.exists' => 'The Mobile Number invalid or The Account has been InActive.'
    ]);
}
     public function username()
    {
        return 'store_mobile';
    }


     //protected function attemptLogin(Request $request)
    // {
    //     return $this->guard('store')->attempt(
    //         $this->credentials($request), $request->filled('remember')
    //     );
    // }
    //  protected function sendLoginResponse(Request $request)
    // {
    //     $request->session()->regenerate();

    //     $this->clearLoginAttempts($request);

    //     if ($response = $this->authenticated($request, $this->guard('store')->user())) {
    //         return $response;
    //     }

    //     return $request->wantsJson()
    //                 ? new JsonResponse([], 204)
    //                 : redirect()->intended($this->redirectPath());
    // }

    protected function credentials(Request $request)
    {
        $store = Mst_store::where('store_mobile',$request->store_username)->first();

        if ($store) {
           /* if ($admin->status == 0) {
                return ['username'=>'inactive','password'=>'You are not an active person, please contact Admin'];
            }else{*/
                return ['store_mobile'=>$request->store_username,'password'=>$request->password];
            }

        return $request->only($this->username(), 'password');
    }

    public function __construct()
    {
        //$this->middleware('guest');
        $this->middleware('guest:store')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('store');
    }

     public function logout(Request $request)
    {
        Auth::guard('store')->logout();
        $cookie = \Cookie::forget('first_time');

        $request->session()->flush();
        $request->session()->regenerate();
        $request->session()->invalidate();

        return redirect('store-login');
    }


}
