<?php

namespace Medians\Templates\Domain;

use Shared\dbaser\CustomModel;

use Medians\Content\Domain\Content;
use Medians\Menus\Domain\Menu;
use Medians\CustomFields\Domain\CustomField;

class WebTemplate extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'templates';

    protected $primaryKey = 'template_id';

	public $fillable = [
		'title', 
		'picture', 
		'status', 
		'folder_name', 
	];


	public function content()
	{
		return $this->hasOne(Content::class, 'item_id', 'template_id')->where('item_type', WebTemplate::class);
	}

	public function langs_content()
	{
		return $this->morphMany(Content::class, 'item');
	}

	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}




}
