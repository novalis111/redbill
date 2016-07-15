<?php

namespace Redbill\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->_setDefaultInvoiceSpan();
    }

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
     * Set default invoice span to current month
     */
    private function _setDefaultInvoiceSpan()
    {
        if (!\Session::has('invoiceFrom')) {
            \Session::set('invoiceFrom', date('Y-m-01'));
        }
        if (!\Session::has('invoiceTo')) {
            \Session::set('invoiceTo', date('Y-m-t'));
        }
    }
}
