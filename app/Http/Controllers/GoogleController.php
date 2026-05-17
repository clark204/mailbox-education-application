<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function googleRedirect()
    {
        return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
    }

    public function googleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $th) {
            return redirect()->route('view.sign-in');
        }

        $file = 'google_'.$googleUser->getId().'.jpg';
        $localAvatar = null;

        try {
            $avatar = Http::get($googleUser->getAvatar())->body();
            Storage::disk('public')->put('avatars/'.$file, $avatar);
            $localAvatar = 'avatars/'.$file;
        } catch (\Throwable $th) {
            $localAvatar = null;
        }

        $user = User::updateOrCreate(['email' => $googleUser->getEmail()], [
            'first_name' => $googleUser->user['given_name'] ?? '',
            'last_name' => $googleUser->user['family_name'] ?? '',
            'avatar' => $localAvatar,
            'google_id' => $googleUser->getId(),
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        if ($user->wasRecentlyCreated) {
            $user->update([
                'password' => Hash::make(Str::random(10)),
                'has_password' => false,
            ]);
        }

        Auth::login($user, true);
        request()->session()->regenerateToken();

        return redirect()->intended(route('view.primary'))->with('success', 'Welcome '.$user->first_name);
    }
}
