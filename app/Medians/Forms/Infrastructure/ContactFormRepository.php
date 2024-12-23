<?php

namespace Medians\Forms\Infrastructure;

use Medians\Forms\Domain\ContactForm;

class ContactFormRepository 
{


	public function find($id)
	{
		return ContactForm::find($id);
	}

	public function findByEmail($email)
	{
		return ContactForm::where('email', $email)->first();
	}

	public function get($limit = 100)
	{
		return ContactForm::limit($limit)->orderBy('name','DESC')->get();
	}

	public function getActive()
	{
		return ContactForm::where('status', 'on')->orderBy('subscriber_code','DESC')->get();
	}



	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new ContactForm();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		

		// Return the  object with the new data
    	$Object = ContactForm::create($dataArray);


    	return $Object;
    }
    	

    	
    /**
     * Update Lead
     */
    public function update($data)
    {

		$Object = ContactForm::find($data['subscriber_id']);
		
		// Return the  object with the new data
    	$Object->update( (array) $data);

    	return $Object;

    }


	/**
	* Delete item to database
	*
	* @Returns Boolen
	*/
	public function delete($id) 
	{
		try {
			
			$delete = ContactForm::find($id)->delete();

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

 
}