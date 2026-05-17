<?php

namespace App\Http\Controllers;

use App\Mail\VerifyMail;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function signIn(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required|min:8',
        ]);

        $login = trim($request->login);

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $login)->first();
        } else {
            $login = preg_replace('/\D/', '', $login);

            $user = User::whereHas('phones', function ($query) use ($login) {
                $query->where('phone_number', $login);
            })->first();
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid email/phone or password.']);
        }

        Auth::login($user);

        if (! $user->is_verified) {
            Auth::logout();

            session(['verification_user_id' => $user->id]);

            $error = $this->emailService->sendEmail($user, false);
            if ($error) {
                return redirect()->back()->with('error', $error);
            }

            return redirect()->route('view.verify');
        }

        return redirect()->intended(route('view.primary'))->with('success', 'Welcome '.$user->first_name);
    }

    public function signUp(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:225|regex:/^[\pL\s\'-]+$/u',
            'last_name' => 'required|string|max:255|regex:/^[\pL\s\'-]+$/u',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        session(['verification_user_id' => $user->id]);

        $error = $this->emailService->sendEmail($user, false);
        if ($error) {
            return redirect()->back()->with('error', $error);
        }

        return redirect()->route('view.verify');
    }

    public static function emailVerification(User $user, $is_forgot)
    {
        $lock = Cache::lock('otp_lock:'.$user->id, 60);

        if (! $lock->get()) {
            return 'please wait 60 seconds before requesting another OTP.';
        }

        $otp = random_int(100000, 999999);
        ! $is_forgot ? Cache::put('otp:'.$user->id, $otp, 300) : Cache::put('forgot_otp:'.$user->id, $otp, 300);
        Mail::to($user->email)->queue(new VerifyMail($user->first_name, $otp));
        Cache::put('otp_resend:'.$user->id, now()->addSeconds(60), 60);

        return null;
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userID = session()->get('verification_user_id');
        $isForgot = session()->get('is_forgot', false);

        if (! $userID) {
            return redirect()->route('view.sign-in');
        }

        $cacheKey = $isForgot ? 'forgot_otp:'.$userID : 'otp:'.$userID;
        $storedOTP = Cache::get($cacheKey);

        if (! $storedOTP) {
            return back()->withErrors(['error' => 'OTP has expired. Please request a new one.']);
        }

        if (hash_equals((string) $request->otp, (string) $storedOTP)) {
            Cache::forget($cacheKey);

            if ($isForgot) {
                session()->forget('is_forgot');
                session(['reset_user_id' => $userID]);
                session()->forget('verification_user_id');

                return redirect()->route('view.change-password');
            }

            User::where('id', $userID)->update([
                'is_verified' => true,
                'email_verified_at' => now(),
            ]);

            Auth::loginUsingId($userID);
            session()->forget('verification_user_id');
            session()->flash('success', 'Your email has been verified successfully!');
            session()->save();

            return redirect()->route('view.primary');
        }

        return back()->withErrors(['error' => 'Invalid or expired OTP. Please try again.']);
    }

    public function resendOTP()
    {
        $userID = session()->get('verification_user_id');
        $isForgot = session()->get('is_forgot', false);
        if (! $userID) {
            return redirect()->route('view.sign-in');
        }

        $user = User::find($userID);
        if (! $user) {
            return redirect()->route('view.sign-in');
        }

        $error = $this->emailService->sendEmail($user, false);
        if ($error) {
            return redirect()->back()->with('error', $error);
        }

        return redirect()->route('view.verify')->with('success', 'A new OTP has been sent to your email!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('view.sign-in')->with('success', 'You have been logged out.');
    }
}
