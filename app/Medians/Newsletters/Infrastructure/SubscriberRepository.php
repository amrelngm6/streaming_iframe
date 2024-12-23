<?php

namespace Medians\Newsletters\Infrastructure;

use Medians\Newsletters\Domain\Subscriber;
use Medians\CustomFields\Domain\CustomField;


class SubscriberRepository 
{

	
	public function find($id)
	{
		return Subscriber::find($id);
	}
	
	public function findByEmail($email)
	{
		return Subscriber::where('email', $email)->first();
	}

	public function get($limit = 100)
	{
		return Subscriber::with('newsletter')->limit($limit)->get();
	}

 
	public function eventsByDate($params)
	{
		return Subscriber::whereBetween('created_at', [$params['start'], $params['end']]);
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Subscriber();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the  object with the new data
    	$Object = Subscriber::create($dataArray);

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Subscriber::find($data['subscriber_id']);
		
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
			
			$delete = Subscriber::find($id)->delete();

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


 
}