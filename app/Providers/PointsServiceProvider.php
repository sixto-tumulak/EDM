<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\UserPoint;

class PointsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Using Closure based composers...
        View::composer(['components.user.user-profile-card', 'wallet.index'], function ($view) {
            // Get the currently authenticated user
            $user = Auth::user();

            // If the user is authenticated, retrieve their points
            $userPoints = $user ? UserPoint::where('user_id', $user->id)->value('points') : 0;

            // Share the userPoints variable with the components
            $view->with('userPoints', $userPoints);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
