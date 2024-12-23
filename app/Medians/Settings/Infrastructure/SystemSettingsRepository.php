<?php

namespace Medians\Settings\Infrastructure;

use Medians\Settings\Domain\SystemSettings;


class SystemSettingsRepository 
{


	protected $app;


	function __construct()
	{
	}

	/**
	* Find item by `id` 
	*/
	public function find($id) 
	{

		return SystemSettings::find($id);

	}

	/**
	* Find item by `id` 
	*/
	public function getByCode($code) : ? String
	{
		try {
			
			$check = SystemSettings::where('code', $code)->first();
			return isset($check->value) ? $check->value : '';
		} catch (\Exception $e) {
    		throw new \Exception($e->getMessage(), 1);
			
		}
	}

	/**
	* Find all items 
	*/
	public function getAll()
	{
		try {

			return SystemSettings::get();

		} catch (\Exception $e) {
    		throw new \Exception($e->getMessage(), 1);
		}
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new SystemSettings();

    	$Model->firstOrCreate($data);

		// Return the SystemSettings object with the new data
		return $Model;
	}
	

	/**
	* Delete item from database
	*/
	public function delete($code) 
	{
		return SystemSettings::where('code', $code)->delete();
	}


	/**
	* Clear item from database
	*/
	public function clear() 
	{
		SystemSettings::delete();
		
		return $this;
	}


}
