<?php

namespace Medians\Notifications\Infrastructure;

use Medians\Notifications\Domain\Notification;

use Medians\Users\Domain\User;
use Medians\Customers\Domain\Customer;


/**
 * Notification class database queries
 */
class NotificationRepository 
{

	protected $app ;


	function __construct ()
	{
	}

	/**
	* Find item by `id` 
	*/
	public function find($id) 
	{

		return Notification::find($id);
	}

	/**
	* Find items by `params` 
	*/
	public function get( $user, $limit = 500,$last_id = 0) 
	{
		if (!$user){return null;}
		return Notification::limit($limit)
			->where('receiver_id', $user->id)->where('receiver_type', User::class )
			->where('id', '>', $last_id)
			->orderBy('created_at', 'DESC')
			->get() ;
	}

	
	/**
	* Save item to database
	*/
	public function store($data) 
	{	

		$Model = new Notification();
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = Notification::firstOrCreate($dataArray);

    	return $Object;
	}



	/**
	* Save item to database
	*/
	public function update($data) 
	{	

		$Object = Notification::find($data['id']);
		
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
			
			return Notification::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception($e->getMessage(), 1);
			
		}
	}


}