<?php

namespace Medians\Customers\Domain;

use Shared\dbaser\CustomModel;
use Medians\Followers\Domain\Follower;
use Medians\CustomFields\Domain\CustomField;
use Medians\Media\Domain\MediaItem;
use Medians\Playlists\Domain\Playlist;
use Medians\Stations\Domain\Station;
use Medians\Channels\Domain\Channel;
use Medians\Likes\Domain\Like;
use Medians\Views\Domain\View;
use Medians\Packages\Domain\PackageSubscription;
use Medians\Invoices\Domain\Invoice;
use Medians\Comments\Domain\Comment;


class Customer extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'customers';

	protected $primaryKey = 'customer_id';

	public $fillable = [
		'name',
		'email',
		'mobile',
		'picture',
		'gender',
		'birth_date',
		'generated_password',
		'password',
		'status'
	];



	public $appends = [ 'photo', 'not_removeable', 'field','picture_name'];

	
	public function getPictureNameAttribute() 
	{
		$e = $this->picture ? explode('/', $this->picture) : [];
		return end($e);
	}

	public function getFieldAttribute()
	{
		return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
	}

	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}

	public function getNotRemoveableAttribute() 
	{
		return true;
	}

	public function getPhotoAttribute() : ?String
	{
		return !empty($this->picture) ? $this->picture : '/uploads/images/default_profile.png';
	}


	public function getFields()
	{
		return $this->fillable;
	}

	public function invoices() 
	{
		return $this->hasMany(Invoice::class , 'customer_id', 'customer_id')->orderBy('date', 'DESC');	
	}

	public function subscriptions() 
	{
		return $this->hasMany(PackageSubscription::class , 'customer_id', 'customer_id')->orderBy('created_at', 'DESC');	
	}

	public function subscription() 
	{
		return $this->hasOne(PackageSubscription::class , 'customer_id', 'customer_id')->where('end_date', '>=', date('Y-m-d'))->where('status','active')->orderBy('end_date', 'DESC');	
	}

	public function followers() 
	{
		return $this->hasMany(Follower::class , 'customer_id', 'customer_id');	
	}

	public function media_items() 
	{
		return $this->hasMany(MediaItem::class , 'author_id', 'customer_id')->with('main_file');	
	}

	public function chartsMedia($type = 'audio', $limit = 4) 
	{
		return $this->hasMany(MediaItem::class , 'author_id', 'customer_id')->selectRaw("type, DATE_FORMAT(created_at, '%b %d') as label, COUNT(*) as y")->having('y', '>', 0)->where('type', $type)->orderBy('created_at')->groupBy('label')->limit($limit)->get();	
	}

	public function limitedMedia($type = 'audio', $limit = 4) 
	{
		return $this->hasMany(MediaItem::class , 'author_id', 'customer_id')->withSum('views', 'times')->withCount('likes', 'comments')->whereIn('type', $type)->orderBy('views_sum_times', 'DESC')->limit($limit)->get();	
	}

	public function audiobooks()
	{
		return $this->hasMany(MediaItem::class , 'author_id', 'customer_id')->withSum('views', 'times')->with('main_file')->where('type', 'audiobook')->orderBy('media_id', 'DESC');	
	}

	public function videos()
	{
		return $this->hasMany(MediaItem::class , 'author_id', 'customer_id')->withSum('views', 'times')->with('main_file')->where('type', 'video')->orderBy('media_id', 'DESC');	
	}

	public function short_videos()
	{
		return $this->hasMany(MediaItem::class , 'author_id', 'customer_id')->withSum('views', 'times')->with('main_file')->where('type', 'short_video')->orderBy('media_id', 'DESC');	
	}

	public function audio_items()
	{
		return $this->hasMany(MediaItem::class , 'author_id', 'customer_id')->withSum('views', 'times')->with('main_file')->where('type', 'audio')->orderBy('media_id', 'DESC');	
	}

	public function playlists() 
	{
		return $this->hasMany(Playlist::class , 'customer_id', 'customer_id')->with('items');	
	}

	public function stations() 
	{
		return $this->hasMany(Station::class , 'customer_id', 'customer_id');	
	}

	public function channels() 
	{
		return $this->hasMany(Channel::class , 'customer_id', 'customer_id');	
	}

	public function following($customer_id) 
	{
		return $this->hasOne(Follower::class , 'customer_id', 'customer_id')->where('follower_id', $customer_id);	
	}

	
	public function likes() 
	{
		return $this->hasMany(Like::class, 'customer_id', 'customer_id')->with('item');	
	}

	public function comments() 
	{
		return $this->hasMany(Comment::class, 'customer_id', 'customer_id');	
	}

	public function viewscount() 
	{
		return $this->morphMany(View::class , 'item')->sum('times');	
	}
	
	public function media_views() 
	{
		return $this->hasManyThrough(View::class, MediaItem::class, 'author_id', 'item_id' );	
	}
	


    public function receiverAsCustomer()
    {
		return  $this;
    }
	
	
    public function can_do($access)
    {

		if (empty($this->subscription->is_valid)) {
			return false;
		}

		$package = json_decode($this->subscription->package);
		$accessCode = $access.'_uploads_limit';
		$limit = $package->feature->$accessCode ?? 0;

		if ($limit < 1) {
			return false;
		}

		switch ($access) {
			case 'audio':
				$count = $this->audio_items->count();
				break;

			case 'audiobooks':
				$count = $this->audiobooks->count();
				break;

			case 'videos':
				$count = $this->videos->count();
				break;

			case 'shortvideo':
				$count = $this->short_videos->count();
				break;

			case 'channels':
				$count = $this->channels->count();
				break;

			case 'stations':
				$count = $this->stations->count();
				break;

			case 'playlists':
				$count = $this->playlists->count();
				break;
		}

		
		if ($limit <= $count) {
			return;
		}

		return  true;

	}
	


	
}
