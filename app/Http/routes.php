<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::auth();

Route::get(
    '/', function () {
    return view('welcome');
}
);
Route::get('/home', 'HomeController@index');

// Invoices
Route::get('/invoice', 'InvoiceController@index');
Route::get('/invoice/view/{id}', 'InvoiceController@view');
Route::get('/invoice/getPdf/{id}', 'InvoiceController@getPdf');
Route::get('/invoice/create', 'InvoiceController@create');
Route::get('/invoice/create/{id}', 'InvoiceController@create');
Route::get('/invoice/edit/{id}', 'InvoiceController@edit');
Route::get('/invoice/set_payed/{id}', 'InvoiceController@setPayed');
Route::get('/invoice/reopen/{id}', 'InvoiceController@reopen');
Route::post('/invoice/save', 'InvoiceController@save');
Route::get('/invoice/span/{from}/{to}', 'InvoiceController@index');
Route::post('/invoice', 'InvoiceController@store');
Route::post('/invoice/addAssets', 'InvoiceController@addAssets');
Route::post('/invoice/removeEntries', 'InvoiceController@removeEntries');
Route::get(
    '/invoice/has_entries/{id}', function ($id) {
    return \Redbill\Invoice::findOrNew($id)->entries->count();
}
);
Route::get(
    '/invoice/checklist/{id}', function ($id) {
    return View::make("invoice_entry._ajaxCheckList")
        ->with("invoice", \Redbill\Invoice::findOrNew($id))
        ->render();
}
);

// Companies
Route::get('/company', 'CompanyController@index');
Route::get('/company/create', 'CompanyController@create');
Route::get('/company/edit/{id}', 'CompanyController@edit');
Route::post('/company/save', 'CompanyController@save');

// Assets
Route::get('/asset', 'AssetController@index');
Route::get('/asset/fetchProjects/{token}', 'AssetController@fetchProjects');
Route::get('/asset/bulkInsert', 'AssetController@bulkInsert');
Route::post('/asset/bulkSave', 'AssetController@bulkSave');
Route::get('/asset/create', 'AssetController@create');
Route::get('/asset/create/{id}', 'AssetController@create');
Route::get('/asset/edit/{id}', 'AssetController@edit');
Route::post('/asset/save', 'AssetController@save');
Route::post('/asset/saveProjectsToClients', 'AssetController@saveProjectsToClients');
Route::get(
    '/asset/checklist/{id}', function ($id) {
    $assetRepo = new \Redbill\Repositories\AssetRepository();
    return View::make("asset._ajaxCheckList")
        ->with("openAssets", $assetRepo->openForClient($id))
        ->render();
}
);
