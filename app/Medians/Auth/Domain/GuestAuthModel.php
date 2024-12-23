<?php

namespace Medians\Auth\Domain;


class GuestAuthModel 
{

	/**
	* @var Object
	*/
	private $data;

	/**
	* @var String
	*/
	private $code = 'GuestAuth';



	/**
	 * Custom code as session uniquename
	 * 
	 * @param String
	 */  
	function __construct($code = null)
	{
		if (!empty($code))
		{
			$this->code = $code;
		}
	}


	/**
	 * Set session 
	 * 
	 * @param Object
	 */  
	public function setSession($sessionId) : void
	{
        setcookie($this->code, $sessionId, time() + (10 * 365 * 24 * 60 * 60), "/");

	}


	public function unsetSession() : void
	{
        setcookie($this->code, 0, time() + (10 * 365 * 24 * 60 * 60), "/");
		unset($this->code); 
	}



	public function checkSession() 
	{

		return !empty($_COOKIE[$this->code]) ? $_COOKIE[$this->code] : null;
	}


	protected function getCode() : String
	{
		return $this->code;
	}


}
