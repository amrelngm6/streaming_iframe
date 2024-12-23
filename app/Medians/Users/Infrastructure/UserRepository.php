<?php

namespace Medians\Users\Infrastructure;

use Medians\Users\Domain\User;
use Medians\CustomFields\Domain\CustomField;


class UserRepository 
{




	public function find($id)
	{
		return User::with('Role')->find($id);
	}

	public function findItem($id)
	{
		return User::with('Role')->find($id);
	}

	public function findByEmail($email)
	{
		return User::with('Role')->where('email', $email)->first();
	}

	public function findByActivationCode($code)
	{
		return User::with('custom_fields')->whereHas('custom_fields', function($q) use ($code) {
			$q->where('code','activation_token')->where('value',$code);
		})->first();
	}

	public function findByToken($token, $code = 'API_token')
	{
		return User::with('custom_fields')->whereHas('custom_fields', function($q) use ($token, $code) {
			$q->where('code',$code)->where('value',$token);
		})->first();

	}

	public function checkDuplicate($param)
	{
		return $this->validateEmail($param['email']);
	}


	public function getByID($customerId)
	{

		return User::find($customerId);

	}


	public function getByEmail($email)
	{

		return  User::where('email', $email)->first();
	}


	public function checkLogin($email, $password)
	{

		return User::where('password', $password)->where('email' , $email)->first();
	}

	public function get($limit = 100)
	{
		return User::with('Role')->limit($limit)->get();
	}


	public function getAll()
	{
		return User::with('Role')->get();
	}

	public function getModerators()
	{
		return User::where('role_id', '>', 3)->with('Role')->get();
	}


	

	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new User();

		$validateEmail = $this->validateEmail($data['email']);
		if ($validateEmail) {
			return $validateEmail;	
		}

		$Model = $Model->firstOrCreate($data);

    	
    	$data['id'] = $Model->id;
    	$this->checkUpdatePassword($data);

    	/**
		* Set token for activation by User
		*/
		$value = User::encrypt(strtotime(date('YmdHis')).$data['id']);
    	$this->setCustomCode((object) $data, 'activation_token', $value);

		// Return the Model object with the new data
    	return $this->find($Model->id);

	}
	

	/**
	* Update item to database
	*/
	public function updateStatus($data) 
	{
		try {
			
			$Object = User::find($data['id']);
			
    		$Object->update(['active'=>$data['active'] ? 'on' : null]);	

    		return $Object;	

		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}
	

	/**
	* Update item to database
	*/
	public function update($data) 
	{
		try {
			
			$Object = User::find($data['id']);
			
			if (!$Object) {
				return translate('this user not found');	
			}

			$validateEmail = isset($data['email']) ? $this->validateEmail($data['email'], $Object->id) : null;
			if ($validateEmail) {
				return $validateEmail;	
			}

			$data['active'] = isset($data['active']) ? 'on' : null;

			// Return the Model object with the new data
	    	$Object->update( (array) $data);
	    	
	    	$data['id'] = $Object->id;
	    	$this->checkUpdatePassword($data);

    		return $Object;	

		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	* validate Email 
	*/
	public function validateEmail($email, $id = 0) 
	{
		if (!empty($email))
		{
			$check = User::where('email', $email)->where('id', '!=', $id)->first();
		}

		if (isset($check->id) && $check->id != $id)
		{
			return translate('EMAIL_FOUND');
		}
	}

	/**
	* Update item to database
	*/
	public static function checkUpdatePassword($data) 
	{
		if (isset($data['id']))
		{
			$Object = User::find($data['id']);
		}

		if (!empty($data['password']))
		{
			// Return the Model object with the new data
    		$Object->password =  User::encrypt($data['password']);
    		$Object->save();
		}
    	
    	return isset($Object) ? $Object : null;	
	}

	/**
	* Set Custom field for User
	*/
	public function setCustomCode($data, $customCode, $value) 
	{

		$fillable = ['code'=>$customCode,'model_type'=>User::class, 'model_id'=>$data->id, 'value'=>$value];

		CustomField::firstOrCreate($fillable);
	}


    /**
     * Reset & Update password 
     */
    public function resetChangePassword($data)
    {
		$Auth = new \Medians\Auth\Application\AuthService;

		$Object = $this->findByToken($data['reset_token'], 'reset_token');
		
		if (!$Object)
		{
			return translate('Sent token is not valid');
		}

		$newPassword = $Auth->encrypt($data['password']);

		// Return the  object with the new data
    	$Object->update( ['password'=> $newPassword]);

    	return 1;
    }

	
	/**
	* Save item to database
	*/
	public function resetPassword($data) 
	{

		$Model = new User();
		
		$findByEmail = $this->findByEmail($data['email']);

		if (empty($findByEmail))
			return translate('User not found');
		
		$deleteOld = CustomField::where('model_type', $Model::class)->where('model_id', $findByEmail->id)->where('code', 'reset_token')->delete();
		
		$fields = [];
		$fields['model_type'] = $Model::class;	
		$fields['model_id'] = $findByEmail->id;	
		$fields['code'] = 'reset_token';	
		$fields['value'] = $Model->randomPassword();

		$Model = CustomField::create($fields);
		
		$sendMail = new \Medians\Mail\Application\MailService($findByEmail->email, $findByEmail->parent_name, 'Your token for reset password', "Here is the attached code \n\n ".$fields['value']);
		$sendMail->sendMail();

		return  1;
    }
    	
		


}
