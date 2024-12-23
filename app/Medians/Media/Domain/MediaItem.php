<?php

namespace Medians\Media\Domain;

use Medians\Views\Domain\View;
use Medians\Likes\Domain\Like;
use Medians\Categories\Domain\Genre;
use Medians\Categories\Domain\BookGenre;
use Medians\Comments\Domain\Comment;
use Medians\Content\Domain\Content;
use Medians\Reviews\Domain\Review;
use Medians\Customers\Domain\Customer;
use Medians\CustomFields\Domain\CustomField;
use Shared\dbaser\CustomModel;


class MediaItem extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'media_items';

    protected $primaryKey = 'media_id';
	
	public $fillable = [
		'name',
		'description',
		'picture',
		'author_id',
		'type',
		'iframe_url',
		'status',
		'created_by',
	];

	public $appends = ['field', 'picture_name', 'player_object'];

	
	public function getPictureNameAttribute() 
	{
		$e = $this->picture ? explode('/', $this->picture) : [];
		return end($e);
	}

	public function getFieldAttribute() 
	{
		return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
	}

	public function getPlayerObjectAttribute() 
	{
		$filename = !empty($this->filepath) ? explode('/', $this->filepath->path) : '';
		return [
			'media_file_id' => $this->media_file_id,
			'media_id' => $this->media_id,
			'title' => $this->name,
			'file' => $filename ? end($filename) : '',
			'artist' => $this->artist ? $this->artist->name : '',
			'poster' => $this->picture_name ?? '',
		];
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


	/**
	 * Relations with onother Models
	 */
	public function category() 
	{
		return $this->hasOne(Category::class , 'category_id', 'category_id')->where('model', MediaItem::class)->with('parent');	
	}
	
	public function artist() 
	{
		return $this->hasOne(Customer::class , 'customer_id', 'author_id');	
	}
	 
	public function customer()
	{
		return $this->hasOne(Customer::class, 'customer_id', 'author_id');	
	}

	public function genres() 
	{
		return $this->belongsToMany(Genre::class, MediaGenre::class, 'media_id', 'genre_id');	
	}
	 
	public function book_genres() 
	{
		return $this->belongsToMany(BookGenre::class, MediaGenre::class, 'media_id', 'genre_id');	
	}

	public function media_tags() 
	{
		return $this->hasMany(MediaItemTag::class , 'media_id', 'media_id');	
	}
	
	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}

	
	public function comments() 
	{
		return $this->morphMany(Comment::class , 'item');	
	}
	

	public function files() 
	{
		return $this->hasMany(MediaFile::class , 'media_id', 'media_id');	
	}
	
	public function main_file() 
	{
		return $this->hasOne(MediaFile::class , 'media_id', 'media_id');	
	}
	
	public function filepath() 
	{
		return $this->hasOne(MediaFile::class , 'media_id', 'media_id')->select('path');	
	}
	
	public function related($limit = null, $type = 'audio') 
	{
		
		$query = MediaItem::query();
		
        foreach (explode(' ', $this->name) as $word) {
			$query->where('name', 'LIKE', '%' . strtolower($word) . '%')
			->where('media_id', '!=' , $this->media_id)
			->where('type', $type)
			->orWhere('description', 'LIKE', '%' . strtolower($word) . '%')
			->where('type', $type)
			->where('media_id', '!=' , $this->media_id);
        }


		return $query->with('genres','main_file', 'artist')
		->limit($limit ?? 6)
		->get();	
	}
	
	
	public function langs() 
	{
		return $this->morphMany(Content::class , 'item')->groupBy('lang');	
	}
	
	public function lang_content() 
	{
		return $this->morphOne(Content::class , 'item')->where('lang', curLng());	
	}
	
	public function reviews() 
	{
		return $this->morphMany(Review::class , 'item')->where('status', 'on');	
	}
	
	public function views() 
	{
		return $this->morphMany(View::class , 'item');	
	}
	
	
	public function viewscount() 
	{
		return $this->morphMany(View::class , 'item')->sum('times');	
	}
	
	
	public function commentscount() 
	{
		return $this->morphMany(Comment::class , 'item')->count();	
	}
	
	public function likescount() 
	{
		return $this->morphMany(Like::class , 'item')->count();	
	}

	public function plays() 
	{
		return $this->morphMany(View::class , 'item');	
	}
	
	public function likes() 
	{
		return $this->morphMany(Like::class , 'item');	
	}
	
	public function liked($customer_id) 
	{
		return $this->morphOne(Like::class , 'item')->where('customer_id', $customer_id);	
	}
	
	public function rate() 
	{
		return $this->reviews->avg('rate');	
	}
	
}
