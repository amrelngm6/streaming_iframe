<?php

namespace Medians\Newsletters\Infrastructure;

use Medians\Newsletters\Domain\Newsletter;
use Medians\CustomFields\Domain\CustomField;


class NewsletterRepository 
{

	
	public function find($id)
	{
		return Newsletter::with('subscribers')->find($id);
	}

	public function get($limit = 100)
	{
		return Newsletter::limit($limit)->get();
	}



	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Newsletter();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the  object with the new data
    	$Object = Newsletter::create($dataArray);

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Newsletter::find($data['newsletter_id']);
		
		// Return the  object with the new data
    	$update = $Object->update( (array) $data);

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
			
			$delete = Newsletter::find($id)->delete();

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


 
}