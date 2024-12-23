<?php

namespace Medians\Blog\Domain;

use Shared\dbaser\CustomModel;
use Medians\Likes\Domain\Like;
use Medians\Views\Domain\View;
use Medians\Comments\Domain\Comment;
use Medians\CustomFields\Domain\CustomField;
use Medians\Content\Domain\Content;
use Medians\Categories\Domain\Category;


class Blog extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'blog';

	public $fillable = [
		'picture', 
		'status', 
		'created_by'
	];


	public $appends = ['title','photo','field','category_name','date', 'update_date','class_name', 'picture_name'];


	public function getPictureNameAttribute() 
	{
		$e = $this->picture ? explode('/', $this->picture) : [];
		return end($e);
	}

	public function getClassNameAttribute() 
	{
		return 'Blog';
	}

	public function getTitleAttribute() 
	{
		return !empty($this->content->title) ? $this->content->title : '';
	}

	public function getCategoryNameAttribute() 
	{
		return !empty($this->category->name) ? $this->category->name : '';
	}

	public function getFieldAttribute() 
	{
		return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
	}

	public function getPhotoAttribute() : ?String
	{
		return $this->thumbnail();
	}

	public function getDateAttribute() : ?String
	{
		return date('Y-m-d', strtotime($this->created_at));
	}
	
	public function getUpdateDateAttribute() 
	{
		return isset($this->content->updated_at) ? date('M, d Y', strtotime($this->content->updated_at)) : '';
	}

	public function photo() : String
	{
		return !empty($this->picture) ? $this->picture : '/uploads/images/default_profile.jpg';
	}

	public function getFields()
	{
		return $this->fillable;
	}

	public function category()
	{
		return $this->hasOne(Category::class, 'category_id', 'category_id')->with('content');
	}

	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}

	public function lang_content()
	{
		return $this->morphOne(Content::class, 'item')->where('lang', translate('lang'));
	}

	public function views()
	{
		return $this->morphMany(View::class, 'item');
	}

	public function comments()
	{
		return $this->morphMany(Comment::class, 'item');
	}

	public function liked($customer_id) 
	{
		return $this->morphOne(Like::class , 'item')->where('customer_id', $customer_id);	
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

	public function langs() 
	{
		return $this->morphMany(Content::class , 'item')->groupBy('lang');	
	}

	public function thumbnail() 
	{
		
    	$return = str_replace('/images/', '/thumbnails/', str_replace(['.png','.jpg','.jpeg'],'.webp', $this->picture));
    	return is_file($return) ? $return : $this->picture;
	}


	public function similar($limit = 3)
	{
		$title = str_replace([' ','-'], '%', $this->lang_content->title);

		return Blog::whereHas('lang_content', function($q) use ($title){
			foreach (explode('%', $title) as $i) {
				$q->where('title', 'LIKE', '%'.$i.'%')->orWhere('content', 'LIKE', '%'.$i.'%');
			}
		})
		->where('id', '!=', $this->id)
		->where('status', 'on')
		->with('lang_content')->limit($limit)->orderBy('updated_at', 'DESC')->get();
	}


}
