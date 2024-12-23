<?php

namespace Medians\PaymentMethods\Domain;

use Shared\dbaser\CustomModel;

/**
 * Transaction class database queries
 */
class PaymentMethodField extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'payment_method_fields';

    protected $primaryKey = 'field_id';

	protected $fillable = [
    	'title',
    	'code',
		'type',
		'payment_method_id',
    	'created_by',
	];



}
