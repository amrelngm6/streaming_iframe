<?php

namespace Medians\Blog\Infrastructure;

use Medians\Blog\Domain\Blog;
use Medians\Hooks\Domain\Hook;
use Medians\Content\Domain\Content;
use Medians\CustomFields\Domain\CustomField;


class BlogRepository 
{


	/**
	 * Load app for Sessions and helpful
	 * methods for authentication and
	 * settings for branch
	 */ 
	protected $app ;


	function __construct()
	{
		$this->app = new \config\APP;

	}


	public static function getModel()
	{
		return new Blog();
	}


	public function find($id)
	{
		$item = Blog::with('lang_content', 'langs')->withSum('views','times')->find($id);
		$item->content_langs = $item->langs->keyBy('lang');
		return $item;
	}

	public function get($limit = 500, $lang = null)
	{
		
		return Blog::with('user','lang_content')->with(['category'=>function($q){
			return $q->with('lang_content');
		}])->limit($limit)->orderBy('id', 'DESC')->get();
	}

	
	public function getFront($limit = 100, $lang = null)
	{
		return Blog::with('user','lang_content')
		->whereHas('lang_content', function($q){
			return $q->where('content', '!=', '');
		})
		->with(['category'=>function($q){
			return $q->with('lang_content');
		}])->limit($limit)
		->where('status', 'on')
		->orderBy('id', 'DESC')->get();
	}


	public function getByCategory($id, $limit = 100)
	{
		return Blog::with('lang_content', 'user')
		->whereHas('lang_content', function($q){
			return $q->where('content', '!=', '');
		})
		->where('status', 'on')
		->where('category_id', $id)
		->limit($limit)
		->orderBy('id', 'DESC')->get();
	}

	public function countByCategory($id)
	{
		return Blog::whereHas('lang_content', function($q){
			return $q->where('content', '!=', '');
		})
		->where('status', 'on')
		->where('category_id', $id)
		->count();
	}

	public function paginate($limit = 100, $offset = 0)
	{
		return Blog::with('lang_content','user')
		->whereHas('lang_content', function($q){
			return $q->where('content', '!=', '');
		})
		->where('status', 'on')
		->limit($limit)
		->offset($offset)
		->orderBy('id', 'DESC')
		->get();
	}

	public function getFeatured($limit = 1)
	{
		return Blog::with('lang_content','user')
		->where('status', 'on')
		->whereHas('lang_content', function($q){
			return $q->where('content', '!=', '');
		})->orderBy('updated_at', 'DESC')->first();
	}


	public function search($request, $limit = 20)
	{
		$title = $request->get('search');
		$return = Blog::whereHas('lang_content', function($q) use ($title){
			$q->where('title', 'LIKE', '%'.$title.'%');
		})
		->where('status', 'on')
		->with('lang_content','user')
		->limit($limit)->orderBy('updated_at', 'DESC')
		->get();

		return $return;
	}

	public function similar($model, $limit = 3)
	{
		$title = str_replace([' ','-'], '%', $model->lang_content->title);

		return Blog::whereHas('lang_content', function($q) use ($title){
			foreach (explode('%', $title) as $i) {
				$q->where('title', 'LIKE', '%'.$i.'%')->orWhere('content', 'LIKE', '%'.$i.'%');
			}
		})
		->where('id', '!=', $model->id)
		->where('status', 'on')
		->with('category', 'lang_content','user')->limit($limit)->orderBy('updated_at', 'DESC')->get();
	}


	/**
	 * Load items with filters
	 */
	public function getWithFilter($params)
	{

			$model = Blog::where('status', 'on');

			if (!empty($params['title']))
			{
				$model = $model->where('name', 'LIKE', '%'.$params['title'].'%');
			}

			if (!empty($params['author_id']) )
			{
				$model = $model->where('created_by', $params['author_id']);
			}

			if (!empty($params['sort_by']))
			{
				switch ($params['sort_by']) {
					case 'best':
						$model = $model->withCount('views')->orderBy('views_count','DESC');
						break;
						
					case 'old':
						$model = $model->orderBy('id','ASC');
						break;
						
					case 'new':
						$model = $model->orderBy('id','DESC');
						break;
				}
			} else {
				$model = $model->orderBy('id','DESC');
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
						$model = $model->orderBy('id','DESC');
						break;
				}
			}

			$totalCount = $model->count();

			$offset = (($params['limit'] ?? 1) * (!empty($params['page']) ? floatval( $params['page'] - 1)  : 0));
			return ['count' => $totalCount, 'items'=>$model->offset($offset)->limit(floatval($params['limit'] ?? 4))->get()];
	}
 
	


	/**
	* Save model to database
	*/
	public function store($data) 
	{

		$Model = new Blog();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $this->getModel()->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the FBUserInfo object with the new data
    	$Object = Blog::create($dataArray);
    	$Object->update($dataArray);

    	// Store languages content
    	!empty($data['content_langs']) ? $this->storeContent(json_decode($data['content_langs'], true), $Object->id) : '';
		
    	// Store Custom fields
    	isset($data['field']) ? $this->storeCustomFields($data['field'], $Object->id) : '';

    	return $Object;
    }
    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Blog::find($data['id']);
		
		$data['updated_at'] = date('Y-m-d H:i:s');
		// Return the FBUserInfo object with the new data
    	$Object->update( (array) $data);

    	// Store languages content
    	!empty($data['content_langs']) ? $this->storeContent(json_decode($data['content_langs'], true), $data['id']) : '';

    	// Store Custom fields
    	!empty($data['field']) ? $this->storeCustomFields($data['field'], $data['id']) : '';

    	return $Object;

    }


	/**
	* Delete model to database
	*
	* @Returns Boolen
	*/
	public function delete($id) 
	{
		try {
			
			$delete = Blog::find($id)->delete();

			if ($delete){
				$this->storeContent(null, $id);
				$this->storeCustomFields(null, $id);
			}

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}





	/**
	* Save related models to database
	*/
	public function storeContent($data, $id) 
	{
		Content::where('item_type', Blog::class)->where('item_id', $id)->delete();
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$fields = $value;
				$fields['item_type'] = Blog::class;	
				$fields['item_id'] = $id;	
				$fields['lang'] = $key;	
				$fields['prefix'] = isset($value['prefix']) ? Content::generatePrefix($value['prefix']) : Content::generatePrefix($value['title']);	

				$Model = Content::create($fields);
			}
	
			return $Model;		
		}
	}




	/**
	* Save related models to database
	*/
	public function storeCustomFields($data, $id) 
	{
		CustomField::where('model_type', Blog::class)->where('model_id', $id)->delete();
		if ($data && $data != '[]')
		{
			foreach ($data as $key => $value)
			{
				$fields = [];
				$fields['model_type'] = Blog::class;	
				$fields['model_id'] = $id;	
				$fields['code'] = $key;	
				$fields['value'] = $value;

				$Model = CustomField::create($fields);
			}
	
			return $Model;		
		}
	}



}