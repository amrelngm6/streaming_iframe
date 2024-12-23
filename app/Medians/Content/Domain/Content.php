<?php

namespace Medians\Content\Domain;

use Shared\dbaser\CustomModel;

class Content extends CustomModel
{

	/**
	* Table name
	* @var String
	*/
	protected $table = 'cms_content';


	/**
	* Table fillable fields
	* @var Array
	*/
	public $fillable = [
		'item_id', 
		'item_type', 
		'prefix', 
		'lang', 
		'title', 
		'content', 
		'short', 
		'seo_title', 
		'seo_desc', 
		'seo_keywords', 
		'inserted_by'
	];


	/**
	 * Has timestamp or not 
	 * (craated_at & updated_at)
	 */ 
	public $timestamps = null;


	public $appends = ['data'];

	public function getDataAttribute()
	{
		// return $this->content;
	}


	public static function generatePrefix($text, $id = 0)
	{
		$text = str_replace(array(' ', '/', '\\', '"', "'", '&', '@', '#', '$', '(', ')', '=', '+'), '_', $text);
		$check = Content::where('prefix', $text)->where('item_id', '!=', $id)->first();

		return strtolower(isset($check->id) ? $text.date('Ymdhis') : $text);

	}

	public function item()
	{
		return $this->morphTo();
	}
}