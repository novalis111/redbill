<?php

namespace Redbill\Http\Requests;

class SaveInvoiceRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.title'          => 'required|filled|max:255',
            'data.date_ordered'   => 'required|date_format:Y-m-d|before:+2 days',
            'data.date_delivered' => 'required|date_format:Y-m-d|afterOrEqual:date_ordered|before:+2 days',
            'data.date_billed'    => 'required|date_format:Y-m-d|afterOrEqual:date_delivered|before:+2 days',
            'data.date_payed'     => 'sometimes|date_format:Y-m-d|afterOrEqual:date_billed|before:+2 days',
        ];
    }
}
