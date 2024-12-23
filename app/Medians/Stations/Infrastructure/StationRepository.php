<?php

namespace Medians\Stations\Infrastructure;

use Medians\Stations\Domain\Station;
use Medians\Stations\Domain\StationMedia;


class StationRepository 
{

	
	public function find($id)
	{
		return Station::with('items', 'activeItem')->withCount('likes')->find($id);
	}

	public function findMedia($id)
	{
		return StationMedia::with('media')->where('station_id', $id)->first();
	}

	public function findItem($id)
	{
		return StationMedia::with('media')->find($id);
	}

	public function findMediaByTime($id, $time)
	{
		return StationMedia::with('media')->whereRaw("? BETWEEN `start_at` AND DATE_ADD(`start_at`, INTERVAL `duration` SECOND)", [$time])->where('station_id', $id)->orderBy('start_at', 'DESC')->first();
	}

	public function get($limit = 1000)
	{
		return Station::withCount('likes', 'items')->with('items')->limit($limit)->get();
	}

	public function getTop($limit = 1000)
	{
		return Station::withCount('likes')->with('items')->limit($limit)->orderBy('likes_count', 'DESC')->get();
	}

	public function getByCustomer($customer_id)
	{
		return Station::withCount('likes')->with('items','activeItem')->where('customer_id', $customer_id)->get();
	}


	public function eventsByDate($params)
	{
		$query = Station::whereBetween('created_at', [$params['start'], $params['end']]);
		return $query;
	}

	
	/**
	 * Load items with filters
	 */
	public function getWithFilter($params)
	{

			$model = Station::
            // where('status', 'on')->
			withCount('likes')->
			with('items');

			if (isset($params['customer_id']))
			{
				$model = $model->where('customer_id', $params['customer_id'] );
				
			}

			if (!empty($params['title']))
			{
				$model = $model->where('name', 'LIKE', '%'.$params['title'].'%');
			}

			if (!empty($params['sort_by']))
			{
				switch ($params['sort_by']) {
					case 'best':
						$model = $model->withCount('views')->orderBy('views_count','DESC');
						break;
						
					case 'old':
						$model = $model->orderBy('station_id','ASC');
						break;
						
					// default:
					case 'new':
						$model = $model->orderBy('station_id','DESC');
						break;
				}
			}

			if (!empty($params['date']))
			{
				switch (strtolower($params['date'])) {
					case 'day':
					case 'week':
					case 'month':
					case 'year':
						$model = $model->whereBetween('created_at', [ date('Y-m-d', strtotime("-1 ".$params['date'])) , date('Y-m-d')]);
						break;
						
					default:
						$model = $model->orderBy('station_id','DESC');
						break;
				}
			}

			$totalCount = $model->count();

			$offset = (($params['limit'] ?? 1) * (!empty($params['page']) ? floatval( $params['page'] - 1)  : 0));
			return ['count' => $totalCount, 'items'=>$model->offset($offset)->limit(floatval($params['limit']))->get()];
	 }
 
	



	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Station();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		$dataArray['status'] = isset($dataArray['status']) ? 'on' : null;
		// Return the Model object with the new data
    	$Object = Station::firstOrCreate($dataArray);

    	return $Object;
    }
    	

	/**
	* Save media item to database
	*/
	public function store_item($data) 
	{

		$Model = new StationMedia();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = StationMedia::firstOrCreate($dataArray);

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Station::find($data['station_id']);
		
		// Return the Model object with the new data
    	$Object->update( (array) $data);

    	return $Object;

    } 
    	
    /**
     * Update Lead
     */
    public function update_item($data)
    {

		$Object = StationMedia::find($data['station_media_id']);
		
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
			
			$delete = Station::find($id)->delete();

			$deleteItems = StationMedia::where('station_id', $id)->delete();

			return $delete;
			

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function deleteItem($id) 
	{
		try {
			
			return StationMedia::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

}
