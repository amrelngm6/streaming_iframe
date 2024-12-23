<?php

namespace Medians\Playlists\Domain;

use Shared\dbaser\CustomModel;
use Medians\Media\Domain\MediaItem;


class PlaylistMedia extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'playlist_media';

    protected $primaryKey = 'playlist_media_id';

	public $fillable = [
		'media_id',
		'playlist_id',
		'customer_id',
		'sort'
	];

	public function media()
	{
		return $this->hasOne(MediaItem::class, 'media_id', 'media_id')->with('main_file');
	}

}
