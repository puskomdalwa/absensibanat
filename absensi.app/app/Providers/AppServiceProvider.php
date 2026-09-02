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
        if (env('APP_ENV') !== 'local') {
        URL::forceScheme('https');
}
    }
}
