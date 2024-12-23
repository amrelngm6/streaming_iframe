<?php

namespace Medians\Invoices\Domain;


use Shared\dbaser\CustomModel;

use Medians\Customers\Domain\Customer;
use Medians\Packages\Domain\PackageSubscription;
use Medians\CustomFields\Domain\CustomField;
use Medians\Transactions\Domain\Transaction;

class Invoice extends CustomModel
{


	/**
	* @var String
	*/
	protected $table = 'invoices';

	protected $primaryKey = 'invoice_id';

	/**
	* @var Array
	*/
	public $fillable = [
		'code'	
		,'subscription_id'	
		,'customer_id'	
		,'payment_method'	
		,'subtotal'	
		,'discount_amount'	
		,'total_amount'	
		,'date'	
		,'status'	
		,'notes' 
		,'currency_code' 
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
		return $this->hasOne(PackageSubscription::class, 'subscription_id', 'subscription_id')->with('package');
	}

	public function transactions()
	{
		return $this->hasMany(Transaction::class, 'invoice_id', 'invoice_id');
	}

	public function transaction()
	{
		return $this->hasOne(Transaction::class, 'invoice_id', 'invoice_id');
	}


}
