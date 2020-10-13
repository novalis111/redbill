<?php

namespace Redbill;

use Illuminate\Database\Eloquent\Model;
use Mockery\CountValidator\Exception;

/**
 * Redbill\Invoice
 *
 * @property int $id
 * @property int $owner_id
 * @property int $client_id
 * @property string $number
 * @property string $title
 * @property string $status
 * @property string $date_ordered
 * @property string $date_delivered
 * @property string $date_billed
 * @property string $date_payed
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Redbill\Company $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\Redbill\InvoiceEntry[] $entries
 * @property-read \Redbill\Company $owner
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereDateBilled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereDateDelivered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereDateOrdered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereDatePayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Invoice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Invoice extends Model
{
    const STATUS_OPEN = 'status_open';
    const STATUS_PAYED = 'status_payed';
    const STATUS_CANCELLED = 'status_cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = ['owner_id', 'client_id', 'title', 'date_ordered', 'date_delivered', 'date_billed', 'date_payed'];

    public function owner()
    {
        return $this->hasOne(Company::class, 'id', 'owner_id');
    }

    public function client()
    {
        return $this->hasOne(Company::class, 'id', 'client_id');
    }

    public function entries()
    {
        return $this->hasMany(InvoiceEntry::class);
    }

    public function sortedEntries()
    {
        return $this->entries->sortBy(
            function ($entry) {
                return $entry->asset->delivery_date;
            }
        );
    }

    /**
     * @return float
     */
    public function getNetSum()
    {
        $sum = 0.0;
        foreach ($this->entries as $entry) {
            /* @var \Redbill\InvoiceEntry $entry */
            $sum += $entry->amount * $entry->price;
        }
        return $sum;
    }

    /**
     * @return float
     */
    public function getGrossSum()
    {
        $sum = 0.0;
        foreach ($this->entries as $entry) {
            /* @var \Redbill\InvoiceEntry $entry */
            $sum += $entry->amount * $entry->price * (1 + (int)$entry->tax_rate / 100);
        }
        return $sum;
    }

    /**
     * @return float
     */
    public function getProfitSum()
    {
        return $this->getNetSum() / (1 + (float)config('redbill.income_tax_rate') / 100);
    }

    /**
     * @return array
     */
    public function getTaxSums()
    {
        $result = [];
        foreach ($this->entries as $entry) {
            $key = md5($entry->tax_rate);
            $result[md5($entry->tax_rate)] = [
                'tax_rate' => $entry->tax_rate,
                'sum'      => isset($result[$key]['sum']) ? $result[$key]['sum'] + $entry->getTax() : $entry->getTax(),
            ];
        }
        return $result;
    }

    /**
     * @param string $newStatus
     *
     * @return $this
     */
    public function setStatus($newStatus)
    {
        if (!in_array($newStatus, $this->getStatuses())) {
            throw new Exception('Invalid invoice status: ' . $newStatus);
        }
        if ($newStatus == self::STATUS_CANCELLED) {
            // TODO: Remove/Delete invoice entries to free asset entries
        }
        $this->status = $newStatus;
        return $this;
    }

    public function getStatusIcon($status = false)
    {
        if (!$status) {
            $status = $this->status;
        }
        switch ($status) {
            case self::STATUS_OPEN:
                return 'glyphicon glyphicon-time rb-green';
                break;
            case self::STATUS_PAYED:
                return 'glyphicon glyphicon-ok-circle rb-green';
                break;
            case self::STATUS_CANCELLED:
                return 'glyphicon glyphicon-ban-circle rb-red';
                break;
            default:
                throw new Exception('Invalid invoice status');
                break;
        }
    }

    public function isOverdue()
    {
        if ($this->status != self::STATUS_OPEN) {
            return false;
        }
        $tsBilled = \DateTime::createFromFormat('!Y-m-d', $this->date_billed)->getTimestamp();
        $timeSinceBilled = time() - $tsBilled;
        if ($timeSinceBilled > ((int)config('redbill.invoice_overdue_days') * 24 * 60 * 60)) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        if ($this->date_payed > 0) {
            return true;
        }
        if ($this->status == self::STATUS_CANCELLED || $this->status == self::STATUS_PAYED) {
            return true;
        }
        return false;
    }

    public function getNumber()
    {
        if ($this->number) {
            return $this->number;
        }
        if (!$this->exists) {
            return false;
        }
        return date(config('redbill.invoice_prefix')) . ($this->id + (int)config('redbill.invoice_increment'));
    }

    static public function getStatuses()
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_PAYED,
            self::STATUS_CANCELLED,
        ];
    }
}
