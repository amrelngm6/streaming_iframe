<?php

namespace Medians\Gallery\Infrastructure;

use Medians\Gallery\Domain\Gallery;
use Medians\Gallery\Domain\GalleryItem;
use Medians\Gallery\Domain\GalleryField;



/**
 * Gallery class database queries
 */
class GalleryRepository 
{

	

	/**
	* Find item by `id` 
	*/
	public function find($id) 
	{
		return Gallery::with('items')->find($id);
	}

	/**
	* Find items by `params` 
	*/
	public function get($params = null) 
	{
		return Gallery::with('items')->get();
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{	

		$Model = new Gallery();
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		// Return the Model object with the new data
    	$Object = Gallery::firstOrCreate($dataArray);

		
    	// Store slides
    	!empty($data['items']) ? $this->storeItems($data['items'], $Object->gallery_id) : '';


    	return $Object;
	}


	/**
	* Update item to database
	*/
    public function update($data)
    {

		$Object = Gallery::find($data['gallery_id']);
		
		// Return the Model object with the new data
    	$Object->update( (array) $data);

    	// Store slides
    	!empty($data['items']) ? $this->storeItems($data['items'], $Object->gallery_id) : '';

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
			
			return Gallery::find($id)->delete();

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}


	
	/**
	* Save related items to database
	*/
	public function storeItems($data, $id) 
	{
		$clear = GalleryItem::where('gallery_id', $id)->delete();

		if ($data)
		{
			$Model = null;
			foreach ($data as $value )
			{
				
				try {
					
					$item = (array) $value;
					if (isset($item['title']) && isset($item['media']))
					{
						$fields = $item;
						$fields['gallery_id'] = $id;	
						$fields['status'] = 'on';	
						
						$Model = GalleryItem::firstOrCreate($fields);
					}

				} catch (\Throwable $th) {
					echo $th->getMessage();
					error_log($th->getMessage());
				}
				
			}
			return $Model;		
		}
	}
	
}