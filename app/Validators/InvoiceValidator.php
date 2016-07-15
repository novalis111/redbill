<?php

namespace Redbill\Validators;

class InvoiceValidator
{
    public function afterOrEqual($attribute, $value, $parameters, $validator)
    {
        /* @var \Illuminate\Validation\Validator $validator */
        $compareDate = $validator->getData()['data'][$parameters[0]];
        if (\DateTime::createFromFormat('Y-m-d', $value) >= \DateTime::createFromFormat('Y-m-d', $compareDate)) {
            return true;
        }
        return false;
    }
}