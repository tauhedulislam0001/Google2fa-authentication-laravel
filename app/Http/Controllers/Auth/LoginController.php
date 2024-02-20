<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Exceptions\InvalidOneTimePassword;
use PragmaRX\Google2FALaravel\Support\Authenticator;

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

    public function loginForm()
    {
        return view('auth.login');
    }

    public function storeLogin(Request $request) {

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if (Auth::user()->google2fa_secret) {
                session()->forget('2fa:user:id');
                session()->forget('google2fa_secret');
                session()->put('2fa:user:id', Auth::id());
                session()->put('google2fa_secret',Auth::user()->google2fa_secret);
                Auth::logout();
                return redirect('/verify-2fa');
            }

            return redirect()->intended('/home');
            // $google2fa = app('pragmarx.google2fa');

            // // Save the registration data in an array
            // $user = User::findOrFail(Auth::user()->id);
            // // Add the secret key to the registration data
            // $secret = $google2fa->generateSecretKey();
            // $user->update(['google2fa_secret' => $secret]);

            // // Save the registration data to the user session for just the next request

            // // Generate the QR image. This is the image the user will scan with their app
            // // to set up two factor authentication
            // $QR_Image = $google2fa->getQRCodeInline(
            //     config('app.name'),
            //     $user->email,
            //     $user->google2fa_secret
            // );

            // session()->forget('2fa:user:id');
            // session()->forget('google2fa_secret');
            // session()->put('2fa:user:id', Auth::id());
            // session()->put('google2fa_secret',Auth::user()->google2fa_secret);
            // Auth::logout();

            // return redirect('/verify-2fa');
        }
        return back()->withErrors(['error' => 'Invalid credentials']);
    }

    public function show2faForm()
    {
        return view('verify_2fa.verify_2fa');
    }

    public function verify2fa(Request $request, Authenticator $authenticator)
    {
        try {
            // Retrieve the user's ID and secret key from session
            $userId = session('2fa:user:id');
            $UserFind = User::findOrFail($userId);
            if($UserFind !=null){
                $secretKey = session('google2fa_secret');
                $otp = $request->input('otp');
                $google2fa = app('pragmarx.google2fa');

                $isValid = $google2fa->verifyKey($secretKey, $otp);
                if ($isValid) {
                    session()->forget('2fa:user:id');
                    session()->forget('google2fa_secret');
                    Auth::loginUsingId($UserFind->id);
                    return redirect()->route('home');
                }
            }
            return back()->withErrors(['otp' => 'Invalid OTP']);
        } catch (InvalidOneTimePassword $exception) {
            return back()->withErrors(['otp' => $exception->getMessage()]);
        }
    }


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
