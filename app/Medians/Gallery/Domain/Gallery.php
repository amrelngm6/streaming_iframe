<?php

namespace Medians\Gallery\Domain;

use Shared\dbaser\CustomModel;

/**
 * Transaction class database queries
 */
class Gallery extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'gallery';

    protected $primaryKey = 'gallery_id';

	protected $fillable = [
		'name',
		'description',
    	'created_by',
	];

	public function items()
	{
		return $this->hasMany(GalleryItem::class, 'gallery_id'); 
	}

}
