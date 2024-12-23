<?php

namespace Medians\Likes\Infrastructure;

use Medians\Likes\Domain\Like;
use Medians\Media\Domain\MediaItem;
use Medians\Playlists\Domain\Playlist;
use Medians\Stations\Domain\Station;
use Medians\Channels\Domain\Channel;
use Medians\Blog\Domain\Blog;

class LikeRepository 
{

	protected $app;



	function __construct()
	{
	}


	public function find($id)
	{
		return Like::find($id);
	}


	public function get($limit = 1000)
	{
		return Like::with('item')->limit($limit)->get();
	}

	public function checkLiked($item_id, $customer_id)
	{
		return Like::where('item_id', $item_id)->where('item_type', MediaItem::class)->where('customer_id', $customer_id)->first();
	}

	public function checkLikedPlaylist($item_id, $customer_id)
	{
		return Like::where('item_id', $item_id)->where('item_type', Playlist::class)->where('customer_id', $customer_id)->first();
	}

	public function checkLikedStation($item_id, $customer_id)
	{
		return Like::where('item_id', $item_id)->where('item_type', Station::class)->where('customer_id', $customer_id)->first();
	}

	public function checkLikedChannel($item_id, $customer_id)
	{
		return Like::where('item_id', $item_id)->where('item_type', Channel::class)->where('customer_id', $customer_id)->first();
	}

	public function checklikedArticle($item_id, $customer_id)
	{
		return Like::where('item_id', $item_id)->where('item_type', Blog::class)->where('customer_id', $customer_id)->first();
	}




	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Like();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = Like::firstOrCreate($dataArray);

    	return $Object;
    }

	/**
	* Save item to database
	*/
	public function store_media($data) 
	{
		$data['item_type'] = MediaItem::class;

		return $this->store($data);
    }

	/**
	* Save item to database
	*/
	public function store_playlist($data) 
	{

		$data['item_type'] = Playlist::class;

		return $this->store($data);
    }
    	
    	

	/**
	* Save item to database
	*/
	public function store_station($data) 
	{

		$data['item_type'] = Station::class;

		return $this->store($data);
    }
    	

	/**
	* Save item to database
	*/
	public function store_channel($data) 
	{
		$data['item_type'] = Channel::class;

		return $this->store($data);
    }
    	

	/**
	* Save item to database
	*/
	public function store_article($data) 
	{
		$data['item_type'] = Blog::class;

		return $this->store($data);
    }
    	

    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Like::find($data['like_id']);
		
		// Return the Model object with the new data
    	$Object->update( (array) $data);

    	return $Object;

    } 


	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function delete($params, $customer_id) 
	{
		try {
			
			return $this->checkLiked($params['item_id'], $customer_id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function deletePlaylist($params, $customer_id) 
	{
		try {
			
			return $this->checkLikedPlaylist($params['item_id'], $customer_id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function deleteStation($params, $customer_id) 
	{
		try {
			
			return $this->checkLikedStation($params['item_id'], $customer_id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function deleteChannel($params, $customer_id) 
	{
		try {
			
			return $this->checkLikedChannel($params['item_id'], $customer_id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function deleteArticle($params, $customer_id) 
	{
		try {
			
			return $this->checkLikedArticle($params['item_id'], $customer_id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

}
