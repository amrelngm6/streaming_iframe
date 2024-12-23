<?php

namespace Medians\Settings\Domain;

use Shared\dbaser\CustomModel;



class SystemSettings extends CustomModel 
{

	/*
	/ @var String
	*/
	protected $table = 'system_settings';


	protected $fillable = [
    	'code',
    	'value',
    	'created_by',
	];

	// public $timestamps = false;


}
