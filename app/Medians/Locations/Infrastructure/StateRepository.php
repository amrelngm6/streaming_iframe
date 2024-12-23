<?php

namespace Medians\Locations\Infrastructure;

use Medians\Locations\Domain\State;

class StateRepository 
{


	public function find($id)
	{
		return State::find($id);
	}

	public function findByName($name)
	{
		return State::where('name', $name)->first();
	}

	public function get($limit = 100)
	{
		return State::with('cities')->limit($limit)->orderBy('name','DESC')->get();
	}

	public function getWithCities($limit = 100)
	{
		return State::with('country', 'cities')->limit($limit)->orderBy('name','DESC')->get();
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new State();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the  object with the new data
    	$Object = State::create($dataArray);


    	return $Object;
    }
    	

    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = State::find($data['state_id']);
		
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
			
			$delete = State::find($id)->delete();

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

 
}