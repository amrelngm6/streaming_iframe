<?php

namespace Medians\Invoices\Infrastructure;

use Medians\Invoices\Domain\Invoice;
use Medians\Invoices\Domain\InvoiceItem;
use Medians\CustomFields\Domain\CustomField;
use Medians\Packages\Domain\PackageSubscription;
use Medians\Trips\Domain\TaxiTrip;
use Medians\Customers\Domain\Customer;


/**
 * Invoice class database queries
 */
class InvoiceRepository 
{

	 
	

	function __construct()
	{
		
	}


	/**
	* Find item by `invoice_id` 
	*/
	public function find($invoice_id) 
	{
		return Invoice::with('customer', 'item')->find($invoice_id);
	}

	/**
	* Find item by `code` 
	*/
	public function findByCode($code) 
	{
		return Invoice::with('customer', 'item')->where('code', $code)->first();
	}

	/**
	* Find item by `params` 
	*/
	public function get($limit = 500) 
	{
		return Invoice::with('customer', 'item', 'transaction')
		
		->limit($limit)
		->orderBy('invoice_id', 'DESC')
		->get();
	}

	/**
	* Find item by `params` 
	*/
	public function getCustomerInvoices($customerId) 
	{
		return Invoice::with('customer',  'item' , 'transaction')
		->where('customer_id', $customerId)
		->where('customer_type', Customer::class)
		->limit(10)
		->orderBy('invoice_id', 'DESC')
		->get();
	}


	/**
	* Find all item between two days By BranchId
	*/
	public function getByDate($params )
	{
	  	$check = Invoice::with('customer', 'item', 'transaction');

	  	if (!empty($params["start_date"]))
	  	{
	  		$check = $check->whereBetween('date' , [$params['start_date'] , $params['end_date']]);
	  	}

	  	if (!empty($params["status"]))
	  	{
	  		$check = $check->where('status' , $params['status'] );
	  	}

  		return $check->orderBy('created_at', 'DESC')->get();
	}




	/**
	* Find latest item
	*/
	public function getLatest($params, $limit = 10 ) 
	{
	  	return Invoice::whereBetween('created_at' , [$params['start'] , $params['end']])
		
	  	->limit($limit)
	  	->orderBy('created_at', 'DESC');
	}
	

	
	public function eventsByDate($params)
	{
		$query = Invoice::whereBetween('date', [$params['start'], $params['end']]);
		return $query;
	}
	
	public function allEventsByDate($params)
	{
		$query = Invoice::whereBetween('date', [$params['start'], $params['end']]);
		return $query;
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{	

		$Model = new Invoice();
		
		$data['code'] = $this->generateCode();

		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = Invoice::firstOrCreate($dataArray);

    	// Store Custom fields
    	!empty($data['field']) ? $this->storeCustomFields((array) $data['field'], $Object->invoice_id) : '';

    	return $Object;
	}


	/**
	* Update item to database
	*/
    public function update($data)
    {

		$Object = Invoice::find($data['invoice_id']);
		
		// Return the Model object with the new data
    	$Object->update( (array) $data);

    	// Store Custom fields
    	!empty($data['field']) ? $this->storeCustomFields((array) $data['field'], $Object->invoice_id) : '';

    	return $Object;
    } 



	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function delete($invoice_id) 
	{
		try {
			
			return Invoice::find($invoice_id)->delete();

		} catch (Exception $e) {

			throw new Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


	/**
	* Save related item to database
	*/
	public function storeCustomFields($data, $id) 
	{
		CustomField::where('model_type', Invoice::class)->where('model_id', $id)->delete();
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$fields = [];
				$fields['model_type'] = Invoice::class;	
				$fields['model_id'] = $id;	
				$fields['code'] = $key;	
				$fields['value'] = $value;

				$Model = CustomField::create($fields);
				$Model->update($fields);
			}
	
			return $Model;		
		}
	}

	

	/**
	 * Generate invoice code
	 */
	public function generateCode()
	{
		$count = Invoice::count();
		$prefix = "INV-";
		return $prefix.($count + 1);
	}
}