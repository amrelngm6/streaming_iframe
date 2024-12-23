<?php

namespace Medians\Stations\Domain;

use Shared\dbaser\CustomModel;
use Medians\Media\Domain\MediaItem;


class StationMedia extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'station_media';

    protected $primaryKey = 'station_media_id';

	public $fillable = [
		'media_id',
		'media_path',
		'station_id',
		'title',
		'start_at',
		'duration',
		'date',
		'sort'
	];

	public $appends = ['id', 'start', 'end'];

	public function getIdAttribute()
	{
		return $this->station_media_id;
	}

	public function getPictureAttribute()
	{
		return isset($this->media->picture) ? $this->media->picture : $this->station->picture;
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
