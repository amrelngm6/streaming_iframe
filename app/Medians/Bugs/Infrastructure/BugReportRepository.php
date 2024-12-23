<?php

namespace Medians\Bugs\Infrastructure;

use Medians\Bugs\Domain\BugReport;



class BugReportRepository 
{


	/**
	* Save item to database
	*/
	public function store($data) 
	{

		// Return the  object with the new data
		return BugReport::create($data);
	}




}
