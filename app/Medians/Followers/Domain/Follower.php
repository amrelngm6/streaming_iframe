<?php

namespace Medians\Followers\Domain;

use Shared\dbaser\CustomModel;

use Medians\Customers\Domain\Customer;

class Follower extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'followers';

    protected $primaryKey = 'follow_id';

	public $fillable = [
		'follower_id',
		'customer_id',
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

	public function follower()
	{
		return $this->hasOne(Customer::class, 'customer_id', 'follower_id');	
	}

}
