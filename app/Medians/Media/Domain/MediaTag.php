<?php

namespace Medians\Media\Domain;

use Shared\dbaser\CustomModel;


class MediaTag extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'media_tags';

    protected $primaryKey = 'tag_id';
	
	public $fillable = [
		'media_id',
		'tag',
	];

}
