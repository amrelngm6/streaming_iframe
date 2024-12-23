<?php

namespace Medians\Channels\Domain;

use Shared\dbaser\CustomModel;
use Medians\Media\Domain\MediaItem;


class ChannelMedia extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'channel_media';

    protected $primaryKey = 'channel_media_id';

	public $fillable = [
		'media_id',
		'media_path',
		'channel_id',
		'title',
		'start_at',
		'duration',
		'bitrate',
		'filesize',
		'date',
		'sort'
	];

	public $appends = ['id', 'start' , 'filename', 'end'];

	public function getIdAttribute()
	{
		return $this->channel_media_id;
	}

	public function getFilenameAttribute() 
	{
		$e = explode('/', $this->media_path);
		return end($e);
	}

	public function getPictureAttribute()
	{
		return isset($this->media->picture) ? $this->media->picture : $this->channel->picture;
	}
	
	public function getStartAttribute()
	{
		return $this->date . 'T'.$this->start_at;
	}
	
	public function getEndAttribute()
	{
		return date("Y-m-d\TH:i:s", strtotime("+".$this->duration." Seconds", strtotime($this->date . ' '.$this->start_at)));
	}

	public function media()
	{
		return $this->hasOne(MediaItem::class, 'media_id', 'media_id')->with('main_file');
	}

}
