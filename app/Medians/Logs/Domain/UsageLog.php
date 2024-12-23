<?php

namespace Medians\Logs\Domain;


use Shared\dbaser\CustomModel;
use Medians\Views\Domain\View;

class UsageLog extends CustomModel
{


	/*
	/ @var String
	*/
	protected $table = 'usage_log';


	protected $fillable = [
    	'user_type',
    	'user_id',
    	'model_id',
    	'model_type',
    	'action',
    	'data',
	];

	public static function addItem($model, $action, $updatedFields = null)
	{
		try {
			
			$user = (new \config\APP)->auth();
			if (in_array($model::class, [UsageLog::class, View::class]))
			{
				return null;
			}
			$pk = $model->getPrimaryKey();

			$userpk = $user ? $user->getPrimaryKey() : null;
			
			$data = array();
			$data['model_type'] = $model::class;
			$data['model_id'] = $model->$pk;
			$data['user_type'] = $userpk ? $user::class : '';
			$data['user_id'] = $userpk ? $user->$userpk : 0;
			$data['action'] = $action;
			$data['data'] = $updatedFields ?? json_encode(json_decode($model));
			$save = UsageLog::create($data);
			return $save;

		} catch (\Throwable $th) {
			return error_log($th->getMessage());
		}
	}

}
