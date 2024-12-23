<?php

namespace Medians\Settings\Domain;

use Shared\dbaser\CustomModel;



class AppSettings extends CustomModel 
{

	/*
	/ @var String
	*/
	protected $table = 'app_settings';


	protected $fillable = [
    	'code',
    	'value',
    	'app',
    	'created_by',
	];

	

}
