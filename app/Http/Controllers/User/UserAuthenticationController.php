<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class UserAuthenticationController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('web')->user();
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::latest()->get();
        return view('user.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        // google 2fa authentication
        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());
        // Save the registration data in an array

        // Add the secret key to the registration data
        $secret = $google2fa->generateSecretKey();

        $user->update(['google2fa_secret' => $secret]);

        // Save the registration data to the user session for just the next request

        // Generate the QR image. This is the image the user will scan with their app
        // to set up two factor authentication
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        // Find the position of the SVG declaration
        $start_position = strpos($QR_Image, '<svg');

        // Extract the SVG part excluding the XML declaration
        $svg_code = substr($QR_Image, $start_position);

        return view('user.edit', compact('user', 'svg_code'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return redirect()->route('user.view');
    }
}
