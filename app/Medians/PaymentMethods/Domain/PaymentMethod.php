<?php

namespace Medians\PaymentMethods\Domain;

use Shared\dbaser\CustomModel;

/**
 * Transaction class database queries
 */
class PaymentMethod extends CustomModel
{

	/*
	/ @var String
	*/
	protected $table = 'payment_methods';

    protected $primaryKey = 'payment_method_id';

	protected $fillable = [
    	'name',
    	'code',
		'description',
		'picture',
    	'status',
    	'created_by',
	];


    public function fields()
    {
        return $this->hasMany(PaymentMethodField::class, 'payment_method_id', 'payment_method_id');
    }


}
