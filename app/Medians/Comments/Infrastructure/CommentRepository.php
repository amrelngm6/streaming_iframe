<?php

namespace Medians\Comments\Infrastructure;

use Medians\Comments\Domain\Comment;
use Medians\Stations\Domain\Station;


class CommentRepository 
{

	protected $app;



	function __construct()
	{
	}


	public function find($id)
	{
		return Comment::find($id);
	}

	public function get($limit = 1000)
	{
		return Comment::with('item', 'customer')->limit($limit)->get();
	}

	public function getByItem($itemId, $itemType, $limit = 100)
	{
		return Comment::with('item')->where('item_type', $itemType)->where('item_id', $itemId)->limit($limit)->get();
	}

	public function getStreamComments($itemId, $lastId, $limit = 100)
	{
		return Comment::with('item')->where('comment_id', '>', $lastId)->where('item_type', Station::class)->where('item_id', $itemId)->limit($limit)->orderBy('comment_id', 'DESC')->get();
	}




	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Comment();
		
		$data['item_type'] = $data['item_type'] ?? (new \Medians\Media\Domain\MediaItem)::class;

		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		$dataArray['status'] = isset($dataArray['status']) ? 'on' : null;
		// Return the Model object with the new data
    	$Object = Comment::firstOrCreate($dataArray);

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Comment::find($data['comment_id']);
		
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
			
			return Comment::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

}
