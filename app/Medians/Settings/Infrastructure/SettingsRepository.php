<?php

namespace Medians\Settings\Infrastructure;

use Medians\Settings\Domain\Settings;


class SettingsRepository 
{


	/**
	* Find item by `id` 
	*/
	public function find($id) : ?Settings
	{

		return Settings::find($id);

	}

	/**
	* Find item by `id` 
	*/
	public function getByCode($code) : ? String
	{
		try {
			
			$check = Settings::where('code', $code)->first();
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
			return Settings::get();

		} catch (\Exception $e) {
    		throw new \Exception($e->getMessage(), 1);
		}
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Settings();

    	$Model->firstOrCreate($data);

		// Return the Settings object with the new data
		return $Model;
	}
	

	/**
	* Delete item from database
	*/
	public function delete($code) 
	{
		return Settings::where('code', $code)->delete();
	}


	/**
	* Clear item from database
	*/
	public function clear() 
	{
		Settings::delete();
		
		return $this;
	}


}
