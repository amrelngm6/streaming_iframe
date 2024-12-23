<?php

namespace Medians\Languages\Infrastructure;

use Medians\Languages\Domain\Language;

class LanguageRepository 
{


	public function find($id)
	{
		return Language::find($id);
	}

	public function get($limit = 100)
	{
		return Language::limit($limit)->orderBy('language_id','ASC')->get();
	}

	public function getActive()
	{
		return Language::where('status', 'on')->orderBy('language_id','ASC')->get();
	}



	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Language();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the  object with the new data
    	$Object = Language::create($dataArray);


    	return $Object;
    }
    	

    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Language::find($data['language_id']);
		
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
			
			$delete = Language::find($id)->delete();

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

 
}