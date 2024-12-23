<?php

namespace Medians\Locations\Domain;

use Shared\dbaser\CustomModel;
use Medians\Students\Domain\Student;


class City extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'cities';

    protected $primaryKey = 'city_id';
	
	public $fillable = [
		'name',
		'state_id',
		'status',
		'created_by',
	];
	public $timestamps = false;


    public function country()
    {
        return $this->hasOne(Country::class, 'country_id', 'country_id');   
    }
	
    public function state()
    {
        return $this->hasOne(State::class, 'state_id', 'state_id');   
    }
	
}
