<?php

namespace Shared\dbaser;


/**
 * Using this class for the common
 * functions between Controllers
 * and services inside APP layer
 * 
 */   
class CustomController 
{
	


	public function validateDelete($item) 
	{
		if ($item->customer_id != $this->app->customer->customer_id)
		{
        	throw new \Exception(translate('Not Allowed'), 1);
		}
	}

}



