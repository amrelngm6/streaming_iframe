<?php

namespace Medians\Hooks\Infrastructure;

use Medians\Hooks\Domain\Hook;


class HookRepository 
{

	public function find($id)
	{
		return Hook::with('plugin')->find($id);
	}

	public function get($limit = 100)
	{
		return Hook::with('plugin')->limit($limit)->get();
	}





	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Hook();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		$dataArray['status'] = isset($dataArray['status']) ? 'on' : null;
		// Return the Model object with the new data
    	$Object = Hook::firstOrCreate($dataArray);

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Hook::find($data['id']);
		
		// Return the Model object with the new data
    	$Object->update( (array) $data);

        isset($data['options']) ? $Object->hookPlugin()->update($data['options'], $Object) : null;

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
			
			return Hook::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

}
