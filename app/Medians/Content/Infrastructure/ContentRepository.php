<?php

namespace Medians\Content\Infrastructure;

use Medians\Content\Domain\Content;
use Medians\Pages\Domain\Page;
use Medians\Languages\Domain\Language;


class ContentRepository 
{

	
	/**
	 * Load app for Sessions and helpful
	 * methods for authentication and
	 */ 
	protected $app ;


	function __construct()
	{
	}


	public function find($prefix)
	{
		return Content::with('item')->where('prefix', $prefix)->first();
	}

	public function findById($id)
	{
		return Content::with('item')->find($id);
	}

	public function findPage($page_id)
	{
		return Content::where('item_id', $page_id)->where('model_type', Page::class)->first();
	}

	public function findByLang($page_id, $lang)
	{
		return Content::where('item_id', $page_id)->where('model_type', Page::class)->where('lang', $lang)->first();
	}

	public function switch_lang($item)
	{
		$lang = $item->lang ?? '';
		return Content::where('item_id', $item->item_id)->where('item_type', $item->item_type)->where('lang', $lang)->first();
	}

	public function get($model, $limit = 100)
	{
		switch ($model) 
		{
				
			default:
				return Content::where('model', $model)->limit($limit)->get();
				break;
		}
	}

	public function handleMissingContent($page, $lang)
	{
		try 
		{
			$pageRepo = new \Medians\Pages\Infrastructure\PageRepository;
			$lang = Language::where('status', 'on')->where('language_code', $lang)->first();
	
			$save = $pageRepo->storeLang(['title'=>$page->title] , $lang->language_code, $page->page_id);
			
			return $save;

		} catch (\Throwable $th) {

			return null;
		}
	}


}