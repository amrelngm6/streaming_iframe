<?php

namespace Medians\Categories\Domain;

use Shared\dbaser\CustomModel;
use Medians\Content\Domain\Content;
use Medians\Media\Domain\MediaItem;
use Medians\Media\Domain\MediaGenre;

class BookGenre extends Category
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
		return 'BookGenre';
	} 

	public function getNameAttribute()
	{
		return $this->lang_content->title ?? '';
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
		return BookGenre::where('model', BookGenre::class)->where('status', 'on')->get();
	}
	

	public function langs() 
	{
		return $this->morphMany(Content::class , 'item')->groupBy('lang');	
	}

	public function items() 
	{
		return $this->belongsToMany(MediaItem::class, MediaGenre::class, 'genre_id', 'media_id');	
	}
}
