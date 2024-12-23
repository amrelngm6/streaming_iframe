<?php

namespace Medians\Plugins\Infrastructure;

use Medians\Plugins\Domain\Plugin;


class PluginRepository 
{

	public function find($id)
	{
		return Plugin::find($id);
	}

	public function get($limit = 100)
	{
		return Plugin::limit($limit)->get();
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Plugin();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		$dataArray['status'] = isset($dataArray['status']) ? 'on' : null;
		// Return the Model object with the new data
    	$Object = Plugin::firstOrCreate($dataArray);

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Plugin::find($data['id']);
		
		// Return the Model object with the new data
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
			
			return Plugin::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

}
