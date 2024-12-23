<?php

namespace Medians\Auth\Application;

use Medians\Auth\Domain\GuestAuthModel;

class GuestAuthService 
{

	
	private $app;


    
	/**
	 * Guest session
	 */ 
	public function guestSession()  
	{
        $AuthModel = new GuestAuthModel();
        $sessionId = $AuthModel->checkSession();
		
        return $sessionId ?? $this->setSession();
	}


	/**
	 * Set session  
	 */ 
	public function setSession()  
	{
        $sessionId = $this->encrypt(date('YmdHis').rand(9,999));

		$AuthModel = new GuestAuthModel();
        
		$AuthModel->setSession($sessionId);

        return $AuthModel->checkSession();
	}


	/**
	 * Encryption algoritm for password storage
	 */ 
	public static function encrypt($value) : String 
	{
		return sha1(md5($value));
	}

}