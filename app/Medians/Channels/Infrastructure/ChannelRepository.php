<?php

namespace Medians\Channels\Infrastructure;

use Medians\Channels\Domain\Channel;
use Medians\Channels\Domain\ChannelMedia;


class ChannelRepository 
{

	protected $app;



	function __construct()
	{
	}


	public function find($id)
	{
		return Channel::with('items', 'activeItem')->withCount('likes')->find($id);
	}

	public function findMedia($id)
	{
		return ChannelMedia::with('media')->where('channel_id', $id)->first();
	}

	public function findItem($id)
	{
		return ChannelMedia::with('media')->find($id);
	}

	public function findMediaByTime($id, $time)
	{
		return ChannelMedia::with('media')->whereRaw("? BETWEEN `start_at` AND DATE_ADD(`start_at`, INTERVAL `duration` SECOND)", [$time])->where('channel_id', $id)->orderBy('start_at', 'DESC')->first();
	}

	public function get($limit = 1000)
	{
		return Channel::withCount('likes')->withCount('items')->with('items')->limit($limit)->get();
	}

	public function getTop($limit = 1000)
	{
		return Channel::withCount('likes')->with('items')->limit($limit)->orderBy('likes_count', 'DESC')->get();
	}

	public function getByCustomer($customer_id)
	{
		return Channel::withCount('likes')->with('items','activeItem')->where('customer_id', $customer_id)->get();
	}

	public function eventsByDate($params)
	{
		$query = Channel::whereBetween('created_at', [$params['start'], $params['end']]);
		return $query;
	}

	/**
	 * Load items with filters
	 */
	public function getWithFilter($params)
	{

			$model = Channel::
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
						$model = $model->orderBy('channel_id','ASC');
						break;
						
					// default:
					case 'new':
						$model = $model->orderBy('channel_id','DESC');
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
						$model = $model->orderBy('channel_id','DESC');
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

		$Model = new Channel();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		$dataArray['status'] = isset($dataArray['status']) ? 'on' : null;
		// Return the Model object with the new data
    	$Object = Channel::firstOrCreate($dataArray);

    	return $Object;
    }
    	

	/**
	* Save media item to database
	*/
	public function store_item($data) 
	{

		$Model = new ChannelMedia();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = ChannelMedia::firstOrCreate($dataArray);

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Channel::find($data['channel_id']);
		
		// Return the Model object with the new data
    	$Object->update( (array) $data);

    	return $Object;

    } 
    	
    /**
     * Update Lead
     */
    public function update_item($data)
    {

		$Object = ChannelMedia::find($data['channel_media_id']);
		
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
			
			$delete = Channel::find($id)->delete();

			$deleteItems = ChannelMedia::where('channel_id', $id)->delete();

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
			
			return ChannelMedia::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

}
