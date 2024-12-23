<?php

namespace Medians\Uploads\Domain;


use Shared\dbaser\CustomModel;

class MediaUpload extends CustomModel
{


	/*
	/ @var String
	*/
	protected $table = 'uploads';
	
	protected $primaryKey = 'upload_id';

	protected $fillable = [
    	'user_type',
    	'user_id',
    	'path',
    	'type',
	];

	public static function addItem($filePath, $type)
	{
		try {
			
			$user = (new \config\APP)->auth();

			$userpk = $user ? $user->getPrimaryKey() : null;
			
			$data = array();
			$data['user_type'] = $userpk ? $user::class : '';
			$data['user_id'] = $userpk ? $user->$userpk : 0;
			$data['path'] = $filePath;
			$data['type'] = $type;
			$save = MediaUpload::create($data);
			return $save;

		} catch (\Throwable $th) {
			return error_log($th->getMessage());
		}
	}

}
