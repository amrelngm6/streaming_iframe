<?php

namespace Medians\Products\Domain;

use Shared\dbaser\CustomModel;


class ProductCategory extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'product_categories';

    protected $primaryKey = 'product_category_id';
	
	public $fillable = [
		'product_id',
		'category_id',
	];

}
