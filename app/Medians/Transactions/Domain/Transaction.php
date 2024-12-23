<?php

namespace Medians\Transactions\Domain;


use Shared\dbaser\CustomModel;

use Medians\Customers\Domain\Customer;
use Medians\Packages\Domain\PackageSubscription;
use Medians\CustomFields\Domain\CustomField;
use Medians\Invoices\Domain\Invoice;

class Transaction extends CustomModel
{


	/**
	* @var String
	*/
	protected $table = 'transactions';

	protected $primaryKey = 'transaction_id';

	/**
	* @var Array
	*/
	public $fillable = [
		'invoice_id'
		,'customer_id'	
		,'subscription_id'	
		,'payment_method'	
		,'amount'	
		,'currency_code'	
		,'date'	
		,'status'	
	];

	public $appends = ['field'];

	public function getFieldAttribute()
	{
		return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
	}

	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}

	public function getFields()
	{
		return $this->fillable;
	}
	
    public function customer()
    {
		return $this->hasOne(Customer::class, 'customer_id', 'customer_id');
    }

    public function item()
    {
		return $this->hasOne(PackageSubscription::class, 'subscription_id', 'subscription_id');
    }

	public function invoice()
	{
		return $this->hasOne(Invoice::class, 'invoice_id', 'invoice_id');
	}



}
