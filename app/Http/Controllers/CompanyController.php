<?php

namespace Redbill\Http\Controllers;

use Illuminate\Http\Request;
use Redbill\Company;
use Redbill\Http\Requests;
use Redbill\Repositories\CompanyRepository;

class CompanyController extends Controller
{
    /**
     * @var CompanyRepository
     */
    private $companies;

    /**
     * Create a new controller instance.
     *
     * @param CompanyRepository $companies
     */
    public function __construct(CompanyRepository $companies)
    {
        $this->middleware('auth');
        $this->companies = $companies;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('company/index', ['companies' => $this->companies->all()]);
    }

    public function create(Request $request)
    {
        return view('company/edit', ['company' => new Company()]);
    }

    public function edit(Request $request, $id)
    {
        return view('company/edit', ['company' => Company::findOrFail($id)]);
    }

    public function save(Request $request)
    {
        $this->validate(
            $request, [
                'data.company_name'  => 'required|max:255',
                'data.salutation'    => 'required|max:50',
                'data.name'          => 'required|max:255',
                'data.street'        => 'required|max:255',
                'data.street_number' => 'required|max:10',
                'data.postcode'      => 'required|max:15',
                'data.city'          => 'required|max:50',
                'data.country'       => 'required|max:100',
            ]
        );
        /* @var Company $company */
        $company = Company::findOrNew($request->company_id);
        $company->fill($request->data)->save();
        return redirect('company')->with(
            'status', trans('redbill.company_name_saved', ['name' => $company->company_name])
        );
    }
}