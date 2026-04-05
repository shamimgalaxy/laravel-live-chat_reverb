<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * This provides the 'users' list to every view in the application.
         * We do NOT include 'selectedUser' here so that the Livewire 
         * component can manage that variable without being overwritten.
         */
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('users', User::where('id', '!=', Auth::id())->get());
            } else {
                $view->with('users', collect());
            }
        });
    }
}