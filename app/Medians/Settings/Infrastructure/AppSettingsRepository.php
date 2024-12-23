<?php

namespace Medians\Settings\Infrastructure;

use Medians\Settings\Domain\AppSettings;


class AppSettingsRepository 
{


	/**
	* Find item by `id` 
	*/
	public function getByCode($app, $code) : ? String
	{
		try {
			
			$check = AppSettings::where('app', strtolower($app))->where('code', $code)->first();
			return isset($check->value) ? $check->value : '';
		} catch (\Exception $e) {
    		throw new \Exception($e->getMessage(), 1);
			
		}
	}

	/**
	* Find all items 
	*/
	public function getAll($app)
	{
		try {

			return AppSettings::where('app', strtolower($app))->get();

		} catch (\Exception $e) {
    		throw new \Exception($e->getMessage(), 1);
		}
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new AppSettings();

    	$Model->firstOrCreate($data);

		// Return the AppSettings object with the new data
		return $Model;
	}
	

	/**
	* Delete item from database
	*/
	public function delete($app, $code) 
	{
		return AppSettings::where('app', strtolower($app))->where('code', $code)->delete();
	}




}
