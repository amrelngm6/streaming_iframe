<?php

namespace Medians\Transactions\Infrastructure;

use Medians\Transactions\Domain\Transaction;
use Medians\CustomFields\Domain\CustomField;


/**
 * Transaction class database queries
 */
class TransactionRepository 
{



	/**
	* Find item by `transaction_id` 
	*/
	public function find($transaction_id) 
	{
		return Transaction::find($transaction_id);
	}

	/**
	* Find items by `params` 
	*/
	public function get($limit = 500) 
	{
		return Transaction::with('customer', 'item', 'invoice')
		->limit($limit)
		->orderBy('created_at', 'DESC')
		->get();
	}


	/**
	* Find all items between two days By BranchId
	*/
	public function getByDate($params )
	{

	  	$check = Transaction::with('customer', 'item','invoice');

	  	if (!empty($params["start_date"]))
	  	{
	  		$check = $check->whereBetween('date' , [$params['start_date'] , $params['end_date']]);
	  	}

  		return $check->orderBy('created_at', 'DESC')->get();
	}




	/**
	* Find latest items
	*/
	public function getLatest($params, $limit = 10 ) 
	{
	  	return Transaction::whereBetween('created_at' , [$params['start'] , $params['end']])
		
	  	->limit($limit)
	  	->orderBy('created_at', 'DESC');
	}
	
	
	public function eventsByDate($params)
	{
		$query = Transaction::whereBetween('date', [$params['start'], $params['end']]);
		return $query;
	}
	
	public function allEventsByDate($params)
	{
		return $this->eventsByDate($params);
	}



	/**
	* Save item to database
	*/
	public function store($data) 
	{	

		$Model = new Transaction();
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = Transaction::create($dataArray);

    	// Store Custom fields
    	!empty($data['field']) ? $this->storeCustomFields($data['field'], $Object->transaction_id) : '';

    	return $Object;
	}


	/**
	* Update item to database
	*/
    public function update($data)
    {

		$Object = Transaction::find($data['transaction_id']);
		
		// Return the Model object with the new data
    	$Object->update( (array) $data);

    	// Store Custom fields
    	!empty($data['field']) ? $this->storeCustomFields($data['field'], $Object->transaction_id) : '';

    	return $Object;
    } 



	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function delete($transaction_id) 
	{
		try {
			
			return Transaction::find($transaction_id)->delete();

		} catch (Exception $e) {

			throw new Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


	/**
	* Save related items to database
	*/
	public function storeCustomFields($data, $id) 
	{
		CustomField::where('model_type', Transaction::class)->where('model_id', $id)->delete();
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$fields = [];
				$fields['model_type'] = Transaction::class;	
				$fields['model_id'] = $id;	
				$fields['code'] = $key;	
				$fields['value'] = (is_array($value) || is_object($value)) ? json_encode($value) : $value;

				$Model = CustomField::create($fields);
				$Model->update($fields);
			}
	
			return $Model;		
		}
	}
}