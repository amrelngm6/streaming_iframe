<?php

namespace Medians\Media\Infrastructure;

use Medians\Media\Domain\MediaItem;
use Medians\Media\Domain\MediaFile;
use Medians\Media\Domain\MediaGenre;
use Medians\Categories\Domain\Genre;
use Medians\Categories\Domain\Mood;
use Medians\Blog\Domain\Blog;
use Medians\Content\Domain\Content;
use Medians\CustomFields\Domain\CustomField;


class MediaItemRepository 
{

	
	/**
	 * Load app for Sessions and helpful
	 * methods for authentication and
	 * settings for branch
	 */ 

	public static function getModel()
	{
		return new MediaItem();
	}

	public function find($id)
	{
		return MediaItem::with('genres' ,'artist','main_file')->withCount('likes', 'comments', 'plays')->find($id);
	}

	public function findByFile($file)
	{
		return MediaItem::whereHas('main_file' , function($q) use ($file) {
			return $q->where('path', $file);
		})->withCount('likes', 'comments', 'plays')->first();
	}

	public function get($limit = 100)
	{
		return MediaItem::with('main_file')->where('type', 'audio')->limit($limit)->get();
	}

	public function getByType($type = 'audio', $limit = 1000)
	{
		return MediaItem::withCount('likes', 'comments', 'views')->with('files')->with(['comments' => function($q) {
			return $q->with('customer')->limit(10);
		}])
		->where('type', $type)->limit($limit)->orderBy('media_id', 'DESC')->get();
	}


	public function eventsByDate($params)
	{
		return MediaItem::whereBetween('created_at', [$params['start'], $params['end']]);
	}

	/**
	 * Load items with filters
	 */
	public function getWithFilter($params)
	{

			$model = MediaItem::
            // where('status', 'on')->
			with('genres', 'main_file' ,'artist');

			if (isset($params['likes']) && isset($params['customer_id']))
			{
				$model->whereHas('likes', function($q) use ($params) {
					$q->where('customer_id', $params['customer_id'] );
				});
			}

			if (!empty($params['genre']))
			{
				$model = $model->whereHas('genres', function($q) use ($params) {
					$q->where('category_id', $params['genre'] );
				});
			}

			if (!empty($params['title']))
			{
				$model = $model->where('name', 'LIKE', '%'.$params['title'].'%');
			}

			if (!empty($params['type']) && in_array($params['type'], ['audio', 'audiobook','video', 'short_video','iframe']))
			{
				$model = $model->where('type', $params['type']);
			}

			if (!empty($params['author_id']) )
			{
				$model = $model->where('author_id', $params['author_id']);
			}

			if (!empty($params['sort_by']))
			{
				switch ($params['sort_by']) {
					case 'best':
						$model = $model->withCount('views')->orderBy('views_count','DESC');
						break;
						
					case 'old':
						$model = $model->orderBy('media_id','ASC');
						break;
						
					// default:
					case 'new':
						$model = $model->orderBy('media_id','DESC');
						break;
				}
			} else {
				$model = $model->orderBy('media_id','DESC');
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
						$model = $model->orderBy('media_id','DESC');
						break;
				}
			}

			$totalCount = $model->count();

			$offset = (($params['limit'] ?? 1) * (!empty($params['page']) ? floatval( $params['page'] - 1)  : 0));
			return ['count' => $totalCount, 'items'=>$model->offset($offset)->limit(floatval($params['limit'] ?? 4))->get()];
	 }
 
	




	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new MediaItem();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $this->getModel()->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}	

		$dataArray['status'] = isset($dataArray['status']) ? 'on' : 0;
		// Return the FBUserInfo object with the new data
    	$Object = MediaItem::create($dataArray);

    	// Store languages content
    	isset($data['selected_genres']) ? $this->storeGenres($data['selected_genres'] ,$Object->media_id) : '';
    	isset($data['files']) ? $this->storeFiles($data['files'] ,$Object) : '';
    	isset($data['field']) ? $this->storeCustomFields($data['field'] ,$Object->media_id) : '';

    	return $Object;
    }
    	
    	

    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = MediaItem::find($data['media_id']);
		
		// Return the FBUserInfo object with the new data
    	$Object->update( (array) $data);

    	// Store languages content
    	isset($data['selected_genres']) ? $this->storeGenres($data['selected_genres'] ,$Object->media_id) : '';
    	isset($data['field']) ? $this->storeCustomFields($data['field'] ,$Object->media_id) : '';
    	isset($data['files']) ? $this->storeFiles($data['files'] ,$Object) : '';
    	isset($data['chapters']) ? $this->storeChapters($data['chapters'] ,$Object) : '';

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
			
			$item = MediaItem::find($id);
			$delete = $item->delete();

			if ($delete){
				CustomField::where('model_type', MediaItem::class)->where('model_id', $id)->delete();
				$this->clearMediaFiles($id);
			}

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

    
	/**
	* Save related items to database
	*/
	public function storeCustomFields($data, $id) 
	{
		CustomField::where('model_type', MediaItem::class)->where('model_id', $id)->delete();
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$fields = [];
				$fields['model_type'] = MediaItem::class;	
				$fields['model_id'] = $id;	
				$fields['code'] = $key;	
				$fields['value'] = $value;

				$Model = CustomField::create($fields);
			}
	
			return $Model;		
		}
	}

	
	/**
	* Save file related items to database
	*/
	public function storeFileCustomFields($data, $id) 
	{
		CustomField::where('model_type', MediaFile::class)->where('model_id', $id)->delete();
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$fields = [];
				$fields['model_type'] = MediaFile::class;	
				$fields['model_id'] = $id;	
				$fields['code'] = $key;	
				$fields['value'] = $value;

				$Model = CustomField::create($fields);
			}
	
			return $Model;		
		}
	}


	/**
	* Save related items to database
	*/
	public function storeFiles($data, $item) 
	{
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$value = (array) $value;
				$fields = $value;
				$fields['media_id'] = $item->media_id;	
				$fields['title'] = $item->name;	
				$fields['sort'] = $value['sort'] ?? 0;	

				$Model = $this->storeFile($fields, $item);
			}
	
			return $Model;		
		}
	}

	/**
	* Save related items to database
	*/
	public function storeFile($file, $item) 
	{
		if ($file)
		{
			$fields = [];
			$fields['media_id'] = $item->media_id;	
			$fields['title'] = $file['title'] ?? '';	
			$fields['path'] = $file['path'];
			$fields['sort'] = $file['sort'] ?? 0;	
			$fields['storage'] = $file['storage'] ?? 'local';	
			$fields['type'] = $file['type'] ?? 'audio';	

			$Model = MediaFile::firstOrCreate($fields);
	
			isset($file['field']) ? $this->storeFileCustomFields($data['field'] ,$Object->media_id) : '';
	
			return $Model;		
		}
	}

	/**
	* Clear media files from database
	*/
	public function clearMediaFiles($media_id) 
	{
		return MediaFile::where('media_id', $media_id)->delete();
	}

	/**
	* Save related items to database
	*/
	public function storeChapters($data, $item) 
	{

		if ($data['media_file_id'])
		{
	
			$clear = $this->clearMediaFiles($item->media_id);

			foreach ($data['media_file_id'] as $key => $value)
			{

				$fields = [];
				$fields['media_id'] = $item->media_id;	
				$fields['title'] = $data['title'][$key];	
				$fields['path'] = $data['path'][$key];	
				$fields['sort'] = $key;	

				$Model = $this->storeFile($fields, $item);
			}
	
			return $Model;		
		}
	}

	/**
	* Save related genres to database
	*/
	public function storeGenres($data, $id) 
	{
		MediaGenre::where('media_id', $id)->delete();
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$fields = [];
                
				$fields['media_id'] = $id;	
				$fields['genre_id'] = $value;	

				$Model = MediaGenre::create($fields);
			}
	
			return $Model;		
		}
	}

}
