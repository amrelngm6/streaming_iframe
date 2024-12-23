<?php

namespace Medians\Newsletters\Domain;

use Shared\dbaser\CustomModel;


class Subscriber extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'newsletter_subscribers';

    protected $primaryKey = 'subscriber_id';
	
	public $fillable = [
		'newsletter_id',
		'name',
		'email',
		'status'
	];


	public function newsletter() 
	{
		return $this->hasOne(Newsletter::class, 'newsletter_id', 'newsletter_id');	
	}
}
