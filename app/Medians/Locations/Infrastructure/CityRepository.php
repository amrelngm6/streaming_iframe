<?php

namespace Medians\Locations\Infrastructure;

use Medians\Locations\Domain\City;

class CityRepository 
{


	public function find($id)
	{
		return City::find($id);
	}

	public function get($limit = 100)
	{
		return City::with('state')->limit($limit)->orderBy('name','DESC')->get();
	}




	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new City();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the  object with the new data
    	$Object = City::create($dataArray);


    	return $Object;
    }
    	

    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = City::find($data['city_id']);
		
		// Return the  object with the new data
    	$Object->update( (array) $data);

    	return $Object;

    }


	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function delete($id) 
	{
		try {
			
			$delete = City::find($id)->delete();

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

 
}