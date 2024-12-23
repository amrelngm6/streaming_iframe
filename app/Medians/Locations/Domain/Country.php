<?php

namespace Medians\Locations\Domain;

use Shared\dbaser\CustomModel;


class Country extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'countries';

    protected $primaryKey = 'country_id';
	
	public $fillable = [
		'name',
		'code',
		'status',
		'created_by',
	];

	public $timestamps = false;

	
    public function states()
    {
        return $this->hasMany(State::class, 'country_id', 'country_id')->with('cities');   
    }
}
