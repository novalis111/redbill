<?php

namespace Redbill\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Redbill\Invoice;

class InvoiceRepository
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Invoice::all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function open()
    {
        return Invoice::whereIn('status', [Invoice::STATUS_OPEN])
            ->orderBy('date_billed')->with('owner', 'client', 'entries')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function payed()
    {
        $builder = Invoice::whereIn('status', [Invoice::STATUS_PAYED])
            ->orderBy('date_payed', 'desc')->with('owner', 'client', 'entries');
        return $this->_filterSpan($builder, 'date_payed')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function cancelled()
    {
        $builder = Invoice::whereIn('status', [Invoice::STATUS_CANCELLED])
            ->orderBy('number', 'asc')->with('owner', 'client', 'entries');
        return $this->_filterSpan($builder, 'date_delivered')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getPayedYears()
    {
        return Invoice::select(['date_payed'])->whereNotNull('date_payed')->orderBy('date_payed', 'desc')->get();
    }

    protected function _filterSpan(Builder $builder, $type = false)
    {
        $from = \Session::get('invoiceFrom', date('Y') . '-01-01');
        $to = \Session::get('invoiceTo', date('Y') . '-12-31');
        switch ($type) {
            case 'date_ordered':
            case 'date_delivered':
            case 'date_billed':
            case 'date_payed':
                break;
            default:
                $type = 'date_payed';
                break;
        }
        return $builder->whereBetween($type, [$from, $to]);
    }
}
