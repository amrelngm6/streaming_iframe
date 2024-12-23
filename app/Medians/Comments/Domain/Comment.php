<?php

namespace Medians\Comments\Domain;

use Shared\dbaser\CustomModel;

use Medians\Customers\Domain\Customer;

class Comment extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'comments';

    protected $primaryKey = 'comment_id';

	public $fillable = [
		'comment',
		'customer_id',
		'item_id',
		'item_type',
		'status'
	];



	public function getFields()
	{
		return $this->fillable;
	}

	public function item()
	{
		return $this->morphTo();	
	}

	public function customer()
	{
		return $this->hasOne(Customer::class, 'customer_id', 'customer_id');	
	}

	public function receiverAsCustomer()
	{
		$customer = $this->item->with('customer')->find($this->item_id);

		return $customer->customer ?? null;
	}

	

}
