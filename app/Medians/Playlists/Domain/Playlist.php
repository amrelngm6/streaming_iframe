<?php

namespace Medians\Playlists\Domain;

use Shared\dbaser\CustomModel;

use Medians\Likes\Domain\Like;
use Medians\Views\Domain\View;
use Medians\Comments\Domain\Comment;

class Playlist extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'playlists';

    protected $primaryKey = 'playlist_id';

	public $fillable = [
		'name',
		'description',
		'customer_id',
		'status'
	];



	public function getFields()
	{
		return $this->fillable;
	}

	public function items()
	{
		return $this->hasMany(PlaylistMedia::class, 'playlist_id', 'playlist_id')->with('media');	
	}

	public function likes()
	{
		return $this->morphMany(Like::class, 'item');	
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
}
