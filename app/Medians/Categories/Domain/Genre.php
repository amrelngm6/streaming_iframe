<?php

namespace Medians\Categories\Domain;

use Shared\dbaser\CustomModel;
use Medians\Content\Domain\Content;

use Medians\Media\Domain\MediaItem;
use Medians\Media\Domain\MediaGenre;

class Genre extends Category
{

	/*
	/ @var String
	*/


	public $appends = ['content_langs', 'lang_content','name' ,'link', 'class_name','picture_name'];

	
	public function getPictureNameAttribute() 
	{
		$e = $this->picture ? explode('/', $this->picture) : [];
		return end($e);
	}
	

	public function getClassNameAttribute()
	{
		return 'Genre';
	} 

	public function getNameAttribute()
	{
		return isset($this->lang_content->title) ? $this->lang_content->title : '';
	} 

	public function getLinkAttribute() : ?String
	{
		return $this->lang_content->prefix ?? '';
	}

	public function getContentLangsAttribute()
	{
		return $this->langs->keyBy('lang');
	} 

	public function getLangContentAttribute()
	{
		$lng = curLng();
		return isset($this->content_langs[$lng]) ? $this->content_langs[$lng] : null;
	} 


	public static function byModel($Model)
	{
		return Genre::where('model', Genre::class)->where('status', 'on')->get();
	}
	


	public function items() 
	{
		return $this->belongsToMany(MediaItem::class, MediaGenre::class, 'genre_id', 'media_id');	
	}

	public function langs() 
	{
		return $this->morphMany(Content::class , 'item')->groupBy('lang');	
	}
}
