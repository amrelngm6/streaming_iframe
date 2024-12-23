<?php

namespace Medians\Builders\Domain;

use Shared\dbaser\CustomModel;


class EmailBuilder extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'email_blocks';

	public $fillable = [
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
		return $this->hasMany(EmailBuilder::class, 'category', 'category');
	}

}
