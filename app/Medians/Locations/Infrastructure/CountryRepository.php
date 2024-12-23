<?php

namespace Medians\Locations\Infrastructure;

use Medians\Locations\Domain\Country;

class CountryRepository 
{


	public function find($id)
	{
		return Country::find($id);
	}

	public function get($limit = 100)
	{
		return Country::limit($limit)->orderBy('name','DESC')->get();
	}

	public function getActive($limit = 100)
	{
		return Country::with('states')->orderBy('name','DESC')->get();
	}



	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Country();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the  object with the new data
    	$Object = Country::create($dataArray);


    	return $Object;
    }
    	

    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Country::find($data['country_id']);
		
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
			
			$delete = Country::find($id)->delete();

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

 
}