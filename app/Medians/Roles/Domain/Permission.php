<?php

namespace Medians\Roles\Domain;

use Shared\dbaser\CustomModel;

class Permission extends CustomModel
{


	/*
	/ @var String
	*/
	protected $table = 'permissions';

    protected $primaryKey = 'permission_id';

	protected $fillable = [
    	'permission_id',
    	'role_id',
    	'model',
    	'action',
    	'access',
	];

	public $timestamps = false;

}
