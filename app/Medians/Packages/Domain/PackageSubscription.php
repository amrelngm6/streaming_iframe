<?php

namespace Medians\Packages\Domain;

use Shared\dbaser\CustomModel;

use Medians\Invoices\Domain\Invoice;
use Medians\CustomFields\Domain\CustomField;
use Medians\Customers\Domain\Customer;

/**
 * Subscription class database queries
 */
class PackageSubscription extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'package_subscriptions';

    protected $primaryKey = 'subscription_id';

	protected $fillable = [
    	'package_id',
		'customer_id',
        'start_date',
        'end_date',
        'duration',
        'payment_type',
        'payment_status',
        'total_cost',
        'notes',
        'status',
	];

    public $appends = ['is_paid', 'is_valid', 'is_expired', 'name', 'field'];

    
	public function getIsValidAttribute()
	{
		return (strtotime(date("Y-m-d")) <= strtotime($this->end_date)) ? (($this->payment_type == 'free' && $this->status != 'canceled') || $this->status == 'active') : false;
	}

    public function getIsExpiredAttribute()
	{
		return (strtotime(date("Y-m-d")) > strtotime($this->end_date));
	}
    
	public function getFieldAttribute()
	{
		return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
	}

	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}

    public function getNameAttribute()
    {
        // return isset($this->package->name) ? $this->package->name : '';
    }

    public function getIsPaidAttribute()
    {
        return $this->payment_type == 'paid' ? true : null;
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function package()
    {
        return $this->hasOne(Package::class, 'package_id', 'package_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'subscription_id', 'subscription_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'customer_id', 'customer_id');
    }


}
