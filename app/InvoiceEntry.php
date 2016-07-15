<?php

namespace Redbill;

use Illuminate\Database\Eloquent\Model;

/**
 * Redbill\InvoiceEntry
 *
 * @property integer $id
 * @property integer $invoice_id
 * @property integer $asset_id
 * @property string $title
 * @property float $amount
 * @property float $price
 * @property float $tax_rate
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Redbill\Invoice $invoice
 * @property-read \Redbill\Asset $asset
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry whereInvoiceId($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry whereAssetId($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry whereTaxRate($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Redbill\InvoiceEntry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InvoiceEntry extends Model
{
    protected $fillable = ['asset_id', 'title', 'amount', 'price', 'tax_rate'];

    /**
     * Get the user that owns the task.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id');
    }

    public function asset()
    {
        return $this->hasOne(Asset::class, 'id', 'asset_id');
    }

    /**
     * @return float
     */
    public function getGross()
    {
        return $this->amount * $this->price * (1 + (int)$this->tax_rate / 100);
    }

    /**
     * @return float
     */
    public function getNet()
    {
        return $this->amount * $this->price;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        return $this->getNet() / (1 + (float)config('redbill.income_tax_rate') / 100);
    }

    /**
     * @return float
     */
    public function getTax()
    {
        return $this->getGross() - $this->getNet();
    }
}
