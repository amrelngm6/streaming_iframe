<?php

namespace Medians\Gallery\Domain;

use Shared\dbaser\CustomModel;

/**
 * Transaction class database queries
 */
class GalleryItem extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'gallery_items';

	protected $fillable = [
		'gallery_id',
		'media',
		'title',
		'subtitle',
		'text',
		'button_text',
		'link',
    	'status',
    	'created_by',
	];



}
