<?php

namespace Medians\Bugs\Domain;

use Shared\dbaser\CustomModel;

class BugReport extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'bug_reports';

	public $fillable = [
			'user_id',
			'file_name',
			'error',
			'info'
		];

	// public $timestamps = null;


}
