<?php

namespace Medians\Packages\Domain;

use Shared\dbaser\CustomModel;

/**
 * Transaction class database queries
 */
class PackageTransaction extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'package_transactions';

    protected $primaryKey = 'package_transactions_id';

	protected $fillable = [

    	'subscription_id',
    	'package_id',
		'model_id',
		'model_type',
        'payment_method',
        'amount',
        'date',
    	'status',
        'notes',
    	'created_by',
	];



}
