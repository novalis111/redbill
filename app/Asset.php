<?php

namespace Redbill;

use Illuminate\Database\Eloquent\Model;
use Redbill\AssetInterfaces\AssetInterface;

/**
 * Redbill\Asset
 *
 * @property int $id
 * @property string $interface_token
 * @property int $foreign_id
 * @property int $client_id
 * @property string $type
 * @property string $title
 * @property float $amount
 * @property string $unit
 * @property string $delivery_date
 * @property string $comment
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Redbill\Company $client
 * @property-read \Illuminate\Database\Eloquent\Collection|\Redbill\InvoiceEntry[] $invoiceEntries
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereForeignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereInterfaceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Asset whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Asset extends Model
{
    const UNIT_HOURS = 'hours';
    const UNIT_PIECES = 'pieces';

    const TYPE_REDBILL_TIME = 'redbill_time';
    const TYPE_REDBILL_PRODUCT = 'redbill_product';
    const TYPE_REDMINE_TIME = 'redmine_time';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = ['interface_token', 'foreign_id', 'client_id', 'type', 'title', 'amount', 'unit', 'delivery_date', 'comment'];

    protected $_billedAmount;

    public static function getUnits()
    {
        return [self::UNIT_HOURS, self::UNIT_PIECES];
    }

    /**
     * Used to quickly insert new assets through bulk insertion
     * @param $clientId
     * @param $title
     * @param $amount
     * @param $date
     *
     * @return static
     */
    public static function insertBulk($clientId, $title, $amount, $date)
    {
        return self::create(
            [
                'interface_token' => AssetInterface::TOKEN,
                'client_id'       => $clientId,
                'type'            => self::TYPE_REDBILL_TIME,
                'title'           => $title,
                'amount'          => $amount,
                'unit'            => self::UNIT_HOURS,
                'delivery_date'   => \DateTime::createFromFormat('Y-m-d', $date),
            ]
        );
    }

    public function client()
    {
        return $this->hasOne(Company::class, 'id', 'client_id');
    }

    public function fullTitle()
    {
        return formatFloat($this->amount) . ' ' . trans('redbill.' . $this->unit) . ' ' . $this->title;
    }

    /**
     * An asset can belong to multiple invoice entries with different amounts billed
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invoiceEntries()
    {
        return $this->hasMany(InvoiceEntry::class, 'asset_id');
    }

    /**
     * @return float
     */
    public function getAmountLeft()
    {
        return (float)($this->amount - $this->getBilledAmount());
    }

    /**
     * @return float
     */
    public function getBilledAmount()
    {
        if (isset($this->_billedAmount)) {
            return $this->_billedAmount;
        }
        $this->_billedAmount = 0.0;
        $this->invoiceEntries->each(
            function ($invoiceEntry) {
                /* @var \Redbill\InvoiceEntry $invoiceEntry */
                $this->_billedAmount += $invoiceEntry->amount;
            }
        );
        return $this->_billedAmount;
    }
}
