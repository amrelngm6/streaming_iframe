<?php

namespace Medians\Plugins\Domain;

use Shared\dbaser\CustomModel;

class Plugin extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'plugins';

	public $fillable = [
		 'name' ,'description', 'class', 'status', 
	];


	public $appends = ['field', 'plugin'];


	public function getFieldAttribute() 
	{
		return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
	}


	public function getFields()
	{
		return $this->fillable;
	}

	public function getPluginAttribute() 
	{
		$object = class_exists("\\".$this->class) ? new $this->class : null;	
		return $object;	
	}
	


}
