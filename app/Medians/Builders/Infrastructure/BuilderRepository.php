<?php

namespace Medians\Builders\Infrastructure;

use Medians\Builders\Domain\Builder;
use Medians\Builders\Domain\EmailBuilder;
use Medians\Content\Domain\Content;

class BuilderRepository 
{
	

	public function find($id)
	{
		return Builder::find($id); 
	}	

	public function findEmailBlock($id)
	{
		return EmailBuilder::find($id); 
	}	

	public function get()
	{
		$save = [];

		foreach (Builder::groupBy('category')->with(['childs'=>function($e){
			// $e->whereIn('category', ['content', 'columns'])->whereIn('id', [92])->select('id', 'content', 'category');
		}])->get() as $key => $value) 
		{
			if (count($value->childs))
			{
				$save[$value->category] = $value->childs;
			}
		}
		return  $save ? $save : 0;
	}	


	
	public function getEmailBlocks()
	{
		$data = [];

		foreach (EmailBuilder::groupBy('category')->with(['childs'=>function($e){
		}])->get() as $key => $value) 
		{
			if (count($value->childs))
			{
				$data[$value->category] = $value->childs;
			}
		}
		return $data;
	}	

	public function store($object)
	{
		$save = Builder::firstOrCreate((array) $object);
		return  $save ? $save : 0;
	}	


	public function updateMeta($request)
	{

		$check = Content::where('prefix', $request->get('prefix'))->first();
		$check->prefix = ($request->get('seoName') == $request->get('prefix')) ? $request->get('prefix') : Content::generatePrefix($request->get('seoName'));
		$check->title = $request->get('title');
		$check->seo_title = $request->get('seo_title');
		$check->seo_keywords = $request->get('seo_keywords');
		$check->seo_desc = $request->get('seo_desc');
		$save = $check->save();
		if ($save)
		{
			echo translate('Done');
		}
		return true;
	}	

	
	
}
