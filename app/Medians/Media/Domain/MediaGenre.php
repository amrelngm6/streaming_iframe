<?php

namespace Medians\Media\Domain;

use Shared\dbaser\CustomModel;


class MediaGenre extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'media_genres';

    protected $primaryKey = 'media_genre_id';
	
	public $fillable = [
		'media_id',
		'genre_id',
	];

}
