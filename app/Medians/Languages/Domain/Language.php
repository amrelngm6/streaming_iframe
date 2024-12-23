<?php

namespace Medians\Languages\Domain;

use Shared\dbaser\CustomModel;


class Language extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'languages';

    protected $primaryKey = 'language_id';
	
	public $fillable = [
		'name',
		'language_code',
		'icon',
		'status',
		'created_by',
	];

	public $appends = ['picture'];

	public function getPictureAttribute()
	{
		return $this->icon;
	}

	
}
