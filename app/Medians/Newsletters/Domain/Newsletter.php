<?php

namespace Medians\Newsletters\Domain;

use Shared\dbaser\CustomModel;


class Newsletter extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'newsletters';

    protected $primaryKey = 'newsletter_id';
	
	public $fillable = [
		'name',
		'status',
	];

	public function subscribers() 
	{
		return $this->hasOne(Subscriber::class, 'newsletter_id', 'newsletter_id');	
	}

}
