<?php

namespace Redbill\Providers;

use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('afterOrEqual', 'Redbill\Validators\InvoiceValidator@afterOrEqual');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        require base_path('app/Validators/') . 'InvoiceValidator.php';
    }
}
