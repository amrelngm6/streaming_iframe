<?php

namespace Medians\Languages\Domain;

use Shared\dbaser\CustomModel;


class Translation extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'translations';

    protected $primaryKey = 'translation_id';
	
	public $fillable = [
		'name',
		'code',
		'value',
		'language_code',
		'created_by',
	];

    public function language()
    {
        return $this->hasOne(Language::class, 'language_code', 'language_code');   
    }
	
    public function items()
    {
        return $this->hasMany(Translation::class, 'code', 'code');   
    }
	
	
}
