<?php

namespace Medians\CustomFields\Domain;

use Shared\dbaser\CustomModel;

class CustomField extends CustomModel
{

	protected $table = 'custom_fields';

	protected $primaryKey = 'field_id';

	public $fillable = ['title', 'code', 'value', 'model_type', 'model_id'];

	public $timestamps = true;

}
