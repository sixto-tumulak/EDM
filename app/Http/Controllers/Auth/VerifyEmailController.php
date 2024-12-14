<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        $user = Auth::user();
        if ($request->user()->hasVerifiedEmail()) {
            // return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
            

            if ($user->role === 1) {
                return redirect()->route('dashboard');
            } else { 
                // Redirect to the profile
                return redirect()->route('profile.edit');
            }
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // return redirect()->intended(RouteServiceProvider::HOME.'?verified=1');
        if ($user->role === 1) {
            return redirect()->route('dashboard');
        } else { 
            // Redirect to the profile
            return redirect()->route('profile.edit');
        }
    }
}
