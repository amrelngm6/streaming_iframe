<?php

namespace Medians\Notifications\Infrastructure;

use Medians\Notifications\Domain\NotificationEvent;



/**
 * NotificationEvent class database queries
 */
class NotificationEventRepository 
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

		return NotificationEvent::find($id);
	}

	/**
	* Find items by `params` 
	*/
	public function get($params = null) 
	{
		return NotificationEvent::with('template')->get();
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{	

		$Model = new NotificationEvent();
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = NotificationEvent::firstOrCreate($dataArray);

    	return $Object;
	}


	/**
	* Update item to database
	*/
    public function update($data)
    {

		$Object = NotificationEvent::find($data['id']);
		
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
			
			return NotificationEvent::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}



	/**
	 * Load all notifiable Models for 
	 * Dynamic events 
	 * 
	 */
	public function loadModels()
	{
		return [
			'Customer' => \Medians\Customers\Domain\Customer::class,
			'User' => \Medians\Users\Domain\User::class,
			'Comment' => \Medians\Comments\Domain\Comment::class,
			'Package' => \Medians\Packages\Domain\PackageSubscription::class,
			'Invoice' => \Medians\Invoices\Domain\Invoice::class,
			'Transaction' => \Medians\Transactions\Domain\Transaction::class,
			'Media' => \Medians\Media\Domain\MediaItem::class,
		];
	}   


	/**
	 * Load all receiveable Models for 
	 * Dynamic events 
	 * 
	 */
	public function loadReceiverModels()
	{
		return [
			'User' => \Medians\Users\Domain\User::class,
			'Customer' => \Medians\Customers\Domain\Customer::class,
		];
	}   
}