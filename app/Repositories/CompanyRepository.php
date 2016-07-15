<?php

namespace Redbill\Repositories;

use Redbill\Company;

class CompanyRepository
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Company::orderBy('company_name')->get();
    }
}