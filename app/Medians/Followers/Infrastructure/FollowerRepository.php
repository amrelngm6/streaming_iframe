<?php

namespace Medians\Followers\Infrastructure;

use Medians\Followers\Domain\Follower;


class FollowerRepository 
{

	protected $app;



	function __construct()
	{
	}


	public function find($id)
	{
		return Follower::find($id);
	}

	public function get($limit = 1000)
	{
		return Follower::with('item')->limit($limit)->get();
	}




	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Follower();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = Follower::firstOrCreate($dataArray);

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Follower::find($data['follow_id']);
		
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
			
			return Follower::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


	/**
	 * Unfollow customer
	 */
	
	 public function unfollow($id, $follower_id ) 
	 {
		try {	
			return Follower::where('customer_id', $id)->where('follower_id', $follower_id)->delete();
		} catch (\Exception $e) {
			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
		}
	}
			 

}
