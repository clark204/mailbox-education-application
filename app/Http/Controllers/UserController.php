<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:225|regex:/^[\pL\s\'-]+$/u',
            'last_name' => 'required|string|max:255|regex:/^[\pL\s\'-]+$/u',
        ]);

        Auth::user()->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);

        return back()->with('success', 'Account successfully updated.');
    }

    public function changeAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Avatar updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => $user->has_password ? 'required|min:8' : 'nullable',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($user->has_password && ! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
            'has_password' => true,
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function forgotSendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        $error = AuthController::emailVerification($user, true);
        if ($error) {
            return redirect()->back()->with('error', $error);
        }

        session(['verification_user_id' => $user->id]);
        session(['is_forgot' => true]);

        return redirect()->route('view.verify');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        $userID = Session::get('reset_user_id');
        if (! $userID) {
            return redirect()->route('view.sign-in');
        }
        $user = User::find($userID);
        if (! $user) {
            return redirect()->route('view.sign-in');
        }
        $user->update([
            'password' => Hash::make($request->new_password),
            'has_password' => true,
        ]);

        Session::forget('reset_user_id');

        return redirect()->route('view.sign-in')->with('success', 'Password reset successfully. Please sign in.');
    }
}
