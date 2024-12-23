<?php

namespace Medians\Packages\Infrastructure;

use Medians\Packages\Domain\PackageSubscription;
use Medians\Students\Domain\Student;
use Medians\Customers\Domain\Employee;
use Medians\Customers\Domain\SuperVisor;


/**
 * PackageSubscription class database queries
 */
class PackageSubscriptionRepository 
{

	

	/**
	* Find item by `id` 
	*/
	public function find($id) 
	{

		return PackageSubscription::with('customer','package')->find($id);
	}

	/**
	* Find items by `params` 
	*/
	public function get($params = null) 
	{
		return PackageSubscription::with('customer','package', 'invoice')->get();
	}

	
	public function eventsByDate($params)
	{
		$query = PackageSubscription::whereBetween('created_at', [$params['start'], $params['end']]);
		return $query;
	}
	
	public function allEventsByDate($params)
	{
		$query = PackageSubscription::whereBetween('created_at', [$params['start'], $params['end']]);
		return $query;
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{	

		$Model = new PackageSubscription();

		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = PackageSubscription::firstOrCreate($dataArray);

    	return $Object;
	}

	
	/**
	* Update item to database
	*/
    public function update($data)
    {

		$Object = PackageSubscription::find($data['subscription_id']);
		
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
			
			return PackageSubscription::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function cancelSubscription($subscriptionId) 
	{
		try {
			
			$check = PackageSubscription::with('customer')->find($subscriptionId);

			if (isset($check->payment_status) && $check->payment_status == 'unpaid')
			{
				return PackageSubscription::find($subscriptionId)->delete();
			}

			if (isset($check->payment_status) && $check->payment_status == 'paid')
			{
				return array('error'=>translate('Paid subscriptions not cancelable'));
			}


		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


}