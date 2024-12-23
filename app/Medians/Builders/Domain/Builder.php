<?php

namespace Medians\Builders\Domain;

use Shared\dbaser\CustomModel;


class Builder extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'builder_blocks';

	public $fillable = [
		'template', 
		'content', 
		'category', 
		'created_by'
	];

	public $appends = ['html'];


	public function getHtmlAttribute()
	{
		return $this->content;
	}

	public function childs()
	{
		return $this->hasMany(Builder::class, 'category', 'category');
	}

}
