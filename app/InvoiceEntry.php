<?php

namespace Redbill;

use Illuminate\Database\Eloquent\Model;

/**
 * Redbill\InvoiceEntry
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $asset_id
 * @property string $title
 * @property float $amount
 * @property float $price
 * @property float $tax_rate
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Redbill\Asset $asset
 * @property-read \Redbill\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\InvoiceEntry whereUpdatedAt($value)
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
