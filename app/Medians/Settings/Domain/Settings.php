<?php

namespace Medians\Settings\Domain;

use Shared\dbaser\CustomModel;



class Settings extends CustomModel 
{

	/*
	/ @var String
	*/
	protected $table = 'settings';


	protected $fillable = [
    	'code',
    	'value',
    	'model',
    	'created_by',
	];

	// public $timestamps = false;


	function __construct()
	{
	}


	public function id() : ?int
	{
		return $this->id;
	}


	public function code() : String
	{
		return $this->code;
	}


	public function value() : ?String
	{
		return $this->value;
	}




	public function setId($id) : void
	{
		$this->id = $id;
	}

	public function setCode($code) : void
	{
		$this->code = $code;
	}

	public function setValue($value) : void
	{
		$this->value = $value;
	}


}
