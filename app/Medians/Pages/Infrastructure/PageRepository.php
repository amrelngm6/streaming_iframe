<?php

namespace Medians\Pages\Infrastructure;

use Medians\Pages\Domain\Page;

use Medians\Content\Domain\Content;
use Medians\CustomFields\Domain\CustomField;

class PageRepository 
{


	public function find($page_id, $prefix = null)
	{
		return Page::with(['lang_content'=>function($q) use ($prefix){
			$prefix ? $q->where('prefix', $prefix) : $q;
		}])->find($page_id);
	}


	public function findByPrefix($prefix = null)
	{
		return Page::with(['lang_content'=>function($q) use ($prefix){
			$prefix ? $q->where('prefix', $prefix) : $q;
		}])->
		whereHas('lang_content',function($q) use ($prefix){
			$prefix ? $q->where('prefix', $prefix) : $q;
		})->
		first();
	}


	public function homepage()
	{
		return Page::where('homepage', 'on')->with(['lang_content'=>function($q) {
			// $q->where('lang', $_SESSION['lang']);
		}])->first();
	}

	public function get($limit = 100)
	{
		return Page::with('lang_content')->limit($limit)->orderBy('page_id', 'DESC')->get();
	}

	function findByLang($page_id, $lang)
	{
		return Page::with(['lang_content' => function ($q) use ($lang) {
			$q->where('lang', $lang);
		}])->find($page_id);
	} 

	public function getByCategory($page_id, $limit = 100)
	{
		return Page::with('content','user')->where('category_id', $page_id)->limit($limit)->orderBy('page_id', 'DESC')->get();
	}

	public function getFeatured($limit = 1)
	{
		return Page::with('content','user')->orderBy('updated_at', 'DESC')->first();
	}



	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Page();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the  object with the new data
    	$Object = Page::create($dataArray);

    	// Store Custom fields
    	isset($data['field']) ? $this->storeFields($data['field'], $Object->page_id) : '';

    	// Store Lang content
    	!empty($data['content']) ? $this->storeContent( (array) $data['content'], $Object->page_id) : '';

    	return $Object;
    }
    	
	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = Page::find($data['page_id']);
		
		// Return the  object with the new data
    	$update = $Object->update( (array) $data);

		// Store Custom fields
    	isset($data['field']) ? $this->storeFields($data['field'], $Object->page_id) : '';

    	!empty($data['content']) ? $this->storeContent($data['content'], $Object->page_id) : '';

    	return $Object;

    }

	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function delete($page_id) 
	{
		try {
			
			$delete = Page::find($page_id)->delete();

			if ($delete){
				$this->storeContent(null, $page_id);
			}

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

	/**
	* Save related items to database
	*/
	public function storeContent($data, $page_id) 
	{
		Content::where('item_type', Page::class)->where('item_id', $page_id)->delete();
		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$Model = $this->storeLang($value, $key, $page_id);
			}	
	
			return $Model;		
		}
	}

	public function storeLang($fields, $key, $page_id )
	{
		$fields = ( array) $fields;
		$checkPrefix = isset($fields['prefix']) ? $fields['prefix'] : Content::generatePrefix($fields['title']);	
		$prefix = Content::where('prefix',$checkPrefix)->first() ? $checkPrefix.rand(999, 999999) : $checkPrefix;

		$fields['item_type'] = Page::class;	
		$fields['item_id'] = $page_id;	
		$fields['lang'] = $key;	
		$fields['prefix'] =  $prefix;
		$Model = Content::create($fields);

		return $Model;
	}

	/**
	* Save related items to database
	*/
	public function storeFields($data, $page_id) 
	{
		CustomField::where('model_type', Page::class)->where('model_id', $page_id)->delete();
		if ($data)
		{
			if (is_string($data))
			{
				$data = json_decode($data);
			}
			foreach ($data as $key => $value)
			{
				$fields = array();
				$fields['model_type'] = Page::class;	
				$fields['model_id'] = $page_id;	
				$fields['code'] = $key;	
				$fields['value'] = $value;	

				$Model = CustomField::create($fields);
			}
	
			return $Model;		
		}
	}




 
}