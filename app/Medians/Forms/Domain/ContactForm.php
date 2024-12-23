<?php

namespace Medians\Forms\Domain;

use Shared\dbaser\CustomModel;


class ContactForm extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'contact_messages';

    protected $primaryKey = 'message_id';
	
	public $fillable = [
		'name',
		'email',
		'phone',
		'message',
	];

}
