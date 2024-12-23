<?php

namespace Medians\Templates\Domain;

use Shared\dbaser\CustomModel;

use Medians\CustomFields\Domain\CustomField;

class EmailTemplate extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'email_templates';

    protected $primaryKey = 'template_id';

	public $fillable = [
		'title', 
		'content', 
		'status', 
		'created_by', 
	];


	public function custom_fields()
	{
		return $this->morphMany(CustomField::class, 'model');
	}




}
