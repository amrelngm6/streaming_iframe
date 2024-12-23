<?php

namespace Medians\Auth\Domain;


class CustomerAuthModel 
{

	/**
	* @var Object
	*/
	private $data;

	/**
	* @var String
	*/
	private $code = 'CustomersAuth';



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
	 * Update data to set session
	 * 
	 * @param Object
	 */  
	public function setData($data) : void
	{
		$this->data = $data;
		$this->setSession($this->data, $this->getCode());
	}


	/**
	 * Set session 
	 * 
	 * @param Object
	 */  
	protected function setSession($data) : void
	{
		$data = (object) $data;
        setcookie($this->code, $data->customer_id, time() + (10 * 365 * 24 * 60 * 60), "/");

	}


	public function unsetSession() : void
	{
		$code = $this->getCode();
        setcookie($this->code, 0, time() + (10 * 365 * 24 * 60 * 60), "/");
		unset($this->code); 
	}



	public function checkSession() 
	{
		$code = $this->getCode();

		return !empty($_COOKIE[$code]) ? $_COOKIE[$code] : null;
	}


	protected function getCode() : String
	{
		return $this->code;
	}


}
