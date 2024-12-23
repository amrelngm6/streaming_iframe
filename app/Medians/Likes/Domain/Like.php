<?php

namespace Medians\Likes\Domain;

use Shared\dbaser\CustomModel;

use Medians\Devices\Domain\Device;
use Medians\Products\Domain\Product;

class Like extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'likes';

    protected $primaryKey = 'like_id';

	public $fillable = [
		'customer_id',
		'item_id',
		'item_type',
	];



	public function getFields()
	{
		return $this->fillable;
	}

	public function item()
	{
		return $this->morphTo();	
	}

}
