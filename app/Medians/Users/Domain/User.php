<?php

namespace Medians\Users\Domain;

use Shared\dbaser\CustomModel;

use Medians\Roles\Domain\Role;
use Medians\Roles\Domain\Permission;
use Medians\CustomFields\Domain\CustomField;

class User extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'users';


	protected $fillable = [
    	'first_name',
    	'password',
    	'last_name',
    	'email',
    	'phone',
    	'picture',
    	'role_id',
    	'active',
	];

	public $appends = ['name', 'photo', 'mobile', 'password', 'field', 'permissions', 'user_id'];


	public function getMobileAttribute() 
	{
		return $this->phone;
	}
	
	public function getUserIdAttribute() 
	{
        return $this->id;
	}

	public function getPermissionsAttribute() 
	{
        return !empty($this->RolePermissions) ? array_column($this->RolePermissions->toArray(), 'access', 'action') : [];
	}

	/**
	 * Override password filed data 
	 * for queries security
	 */
	public function getPasswordAttribute() 
	{
		return null;
	}


	public function getNameAttribute() : String
	{
		return $this->name();
	}

	public function getPhotoAttribute() : ?String
	{
		return $this->photo();
	}

    public function getFieldAttribute() 
    {
        return !empty($this->custom_fields) ? array_column($this->custom_fields->toArray(), 'value', 'code') : [];
    }

	public function photo() : String
	{
		return !empty($this->picture) ? $this->picture : '/uploads/images/default_profile.png';
	}

	public function name() : String
	{
		return $this->first_name.' '.$this->last_name;
	}



	/**
	 * Relation with role 
	 */
	public function Role() 
	{
		return $this->hasOne(Role::class, 'id', 'role_id');
	}
	
	public function hasToken()
	{
        return $this->hasOne(CustomField::class, 'model_id', 'id')->where('model_type', User::class);
	}

    public function custom_fields()
    {
        return $this->morphMany(CustomField::class, 'model');
    }

    public function RolePermissions()
    {
		return $this->hasMany(Permission::class, 'role_id', 'role_id')->where('access', 1);
    }
    


	


	/**
	 * Create Custom filed for user
	 */
	public function insertCustomField($code, $value)
	{

    	// Insert activation code 
		$fillable = [
			'code'=>$code,
			'model_type'=>User::class, 
			'model_id'=>$this->id, 
			'value'=>$value
		];

		return CustomField::firstOrCreate($fillable);

	}  





    /**
     * Handle the event after an item 
     * has been updated 
     * 
     */
    public function updatedEvent()
    {
    	$fields = array_fill_keys($this->fillable,1);
    	$fields['password'] = 1;
    	$updatedFields = array_intersect_key($fields, $this->getDirty());

    	if (empty($updatedFields))
    		return null;


    	// Insert activation code 
		$this->insertCustomField('activation_token', User::encrypt(strtotime(date('YmdHis')).$this->id));
    }  

	

}
