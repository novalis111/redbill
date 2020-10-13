<?php

namespace Redbill;

use Illuminate\Database\Eloquent\Model;

/**
 * Redbill\Company
 *
 * @property int $id
 * @property string $company_name
 * @property string $salutation
 * @property string $name
 * @property string $street
 * @property string $street_number
 * @property string $postcode
 * @property string $city
 * @property string $country
 * @property string $telephone
 * @property string $mobile
 * @property string $fax
 * @property string $email
 * @property string $website
 * @property string $tax_number
 * @property string $iban
 * @property string $bic
 * @property string $bank_name
 * @property float $hourly_rate
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Redbill\Invoice[] $asClientInvoices
 * @property-read \Illuminate\Database\Eloquent\Collection|\Redbill\Invoice[] $asOwnerInvoices
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereBic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereFax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereHourlyRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereSalutation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereStreetNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Redbill\Company whereWebsite($value)
 * @mixin \Eloquent
 */
class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = ['company_name', 'salutation', 'name', 'street', 'street_number', 'postcode', 'city', 'country', 'telephone',
           'mobile', 'fax', 'email', 'website', 'tax_number', 'iban', 'bic', 'bank_name', 'hourly_rate'];

    public function asOwnerInvoices()
    {
        return $this->hasMany(Invoice::class, 'owner_id', 'id');
    }

    public function asClientInvoices()
    {
        return $this->hasMany(Invoice::class, 'client_id', 'id');
    }

    /**
     * @return float
     */
    public function getHourlyRate()
    {
        if ($this->hourly_rate > 0) {
            return (float)$this->hourly_rate;
        }
        return (float)config('redbill.default_hour_price');
    }
}
