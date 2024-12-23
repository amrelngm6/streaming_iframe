<?php

namespace Medians\Media\Domain;

use Shared\dbaser\CustomModel;

use Medians\CustomFields\Domain\CustomField;

class MediaFile extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'media_files';

    protected $primaryKey = 'media_file_id';
	
	public $fillable = [
		'media_id',
		'title',
		'type',
		'storage',
		'path',
		'sort',
	];

	public $appends = ['wave', 'filename', 'field', 'picture', 'player_object'];

	public function getWaveAttribute() 
	{
		$a = explode('/', $this->path);
		$e = explode('.', $this->path);
		return !empty(end($a)) ? str_replace(end($e), 'png', end($a)) : '';
	}

	public function getFilenameAttribute() 
	{
		$e = explode('/', $this->path);
		return end($e);
	}

	public function getPictureAttribute() 
	{
		return $this->media->picture ?? '';
	}

	
	public function getFieldAttribute() 
	{
		return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
	}


	public function media() 
	{
		return $this->hasOne(MediaItem::class , 'media_id', 'media_id');	
	}
	
	public function getPlayerObjectAttribute() 
	{
		$filename = $this->filename;
		return [
			'media_file_id' => $this->media_file_id,
			'media_id' => $this->media_id,
			'file' => $this->filename,
			'title' => $this->title ?? '',
			'artist' => isset($this->media->artist) ? $this->media->artist->name : '',
			'poster' => $this->media->picture_name ?? '',
		];
	}


	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}

}
