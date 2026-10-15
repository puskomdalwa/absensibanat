<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Departemen;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('departemen')) {
            $departemen = Departemen::where('nama', '!=', 'Admin')->get(); // Fetch departments
            View::share('departemen', $departemen);
        }
        
        Carbon::setLocale('id');

        // Share $showNominal and $nominalMode across all views
        View::composer('*', function ($view) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $view->with('showNominal', \App\Models\Setting::isNominalVisible($user));
            $view->with('nominalMode', \App\Models\Setting::getNominalDisplayMode());
        });

        if (!app()->runningInConsole()) {
            $host = request()->getHost();
            $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1'])
                || str_ends_with($host, '.test')
                || str_ends_with($host, '.local')
                || str_starts_with($host, '192.168.')
                || str_starts_with($host, '10.')
                || str_starts_with($host, '172.');

            if (!$isLocal || request()->isSecure() || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
                URL::forceScheme('https');
            }
        }
    }
}
