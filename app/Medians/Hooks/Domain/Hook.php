<?php

namespace Medians\Hooks\Domain;

use Shared\dbaser\CustomModel;
use Medians\Content\Domain\Content;
use Medians\CustomFields\Domain\CustomField;
use Medians\Plugins\Domain\Plugin;

class Hook extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'hooks';

	public $fillable = [
		 'title', 'position', 'plugin_class', 'status', 
	];


	public $appends = ['content_langs', 'lang_content', 'field'];

	public function getFieldAttribute() 
	{
		return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
	}

	public function getContentLangsAttribute()
	{
		return $this->langs->keyBy('lang');
	} 


	public function getLangContentAttribute()
	{
		$lng = curLng();
		return isset($this->content_langs[$lng]) ? $this->content_langs[$lng] : [];
	} 

	public function getFields()
	{
		return $this->fillable;
	}

	
	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}

	public function langs() 
	{
		return $this->morphMany(Content::class , 'item')->groupBy('lang');	
	}
	
	
	public function plugin() 
	{
		return $this->hasOne(Plugin::class , 'class', 'plugin_class');	
	}
	
	public function hookPlugin() 
	{
		return class_exists("\\".$this->plugin_class) ? new $this->plugin_class : null;	
	}
	

}
