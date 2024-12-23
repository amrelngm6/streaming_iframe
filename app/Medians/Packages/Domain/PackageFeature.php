<?php

namespace Medians\Packages\Domain;

use Shared\dbaser\CustomModel;

use Medians\Customers\Domain\StudentApplicant;
use Medians\CustomFields\Domain\CustomField;

/**
 * Subscription class database queries
 */
class PackageFeature extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'package_features';

    protected $primaryKey = 'feature_id';

	protected $fillable = [
    	'package_id',
		'code',
        'value',
	];


    public function package()
    {
        return $this->hasOne(Package::class, 'package_id', 'package_id');
    }


}
