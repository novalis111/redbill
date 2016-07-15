<?php

namespace Redbill\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Redbill\Asset;

class AssetRepository
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return Asset::orderBy('delivery_date', 'desc')->get();
    }

    /**
     * @return Collection[]
     */
    public function groupedByClient()
    {
        $open = [];
        $clientIds = \DB::query()->from('assets')->leftJoin('companies', 'client_id', '=', 'companies.id')
            ->orderBy('company_name')->distinct()->get(['client_id']);
        foreach ($clientIds as $entry) {
            /* @var \stdClass $entry */
            $open[$entry->client_id] = $this->_notFullyBilled()
                ->where('client_id', '=', $entry->client_id)->with('client')->get();
        }
        return $open;
    }

    public function notFullyBilled()
    {
        return $this->_notFullyBilled()->get();
    }

    protected function _notFullyBilled()
    {
        /* @var \Illuminate\Database\Eloquent\Builder $builder */
        $builder = Asset::select(['assets.*', 'ie.amount AS billedAmount'])
            ->leftJoin('invoice_entries AS ie', 'ie.asset_id', '=', 'assets.id')
            ->havingRaw('COALESCE(billedAmount, 0) < assets.amount')
            ->orderBy('assets.delivery_date')
            ->groupBy('assets.id');
        // $sql = $builder->getQuery()->toSql();
        // dd($builder->get()->first());
        return $builder;
    }

    public function forClient($company_id)
    {
        return Asset::where('client_id', '=', $company_id)->get();
    }

    public function openForClient($id)
    {
        return $this->_notFullyBilled()->where('client_id', '=', (int)$id)->get();
    }
}