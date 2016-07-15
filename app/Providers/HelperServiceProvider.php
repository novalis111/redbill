<?php

namespace Redbill\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        require base_path('app/Helpers/') . 'NumberFormatHelper.php';
        require base_path('app/Helpers/') . 'LittleHelpers.php';
    }
}
