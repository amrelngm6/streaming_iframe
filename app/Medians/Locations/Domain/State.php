<?php

namespace Medians\Locations\Domain;

use Shared\dbaser\CustomModel;
use Medians\Students\Domain\Student;


class State extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'states';

    protected $primaryKey = 'state_id';
	
	public $fillable = [
		'name',
		'code',
		'country_id',
		'status',
		'created_by',
	];

	public $timestamps = false;


    public function country()
    {
        return $this->hasOne(Country::class, 'country_id', 'country_id');   
    }
	
    public function cities()
    {
        return $this->hasMany(City::class, 'state_id', 'state_id');   
    }
	
	
}
