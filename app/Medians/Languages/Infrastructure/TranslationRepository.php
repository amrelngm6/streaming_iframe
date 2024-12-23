<?php

namespace Medians\Languages\Infrastructure;

use Medians\Languages\Domain\Translation;

class TranslationRepository 
{


	public function find($id)
	{
		return Translation::find($id);
	}

	public function findByCode($code)
	{
		return Translation::where('code', $code)->first();
	}

	public function findByLang($languageCode)
	{
		return Translation::where('language_code', $languageCode)->groupBy('code')->get()->pluck(null, 'code')->toArray();
	}
	
	public function findByCodeLang($code, $languageCode)
	{
		if (empty($code))
			return;

		$code =  strtolower(str_replace([' ', '/', '&', '?','؟' , '@', '#', '$', '%', '(', ')', '-', '='], '_', $code)) ;
		return Translation::where('language_code', $languageCode)->where('code', $code)->first();
	}

	public function get($limit = 2000)
	{
		$return = Translation::with('items','language')->limit($limit)->groupBy('code')->orderBy('updated_at','DESC')->get();

		foreach ($return as $k => $row) {
			$return[$k]->translation =  isset($row->items) ? array_column((array) json_decode($row->items), 'value', 'language_code') : '';
		}
		return $return;
	}

	/**
	* Save multi Items to database
	*/
	public function storeItems($data) 
	{
		$row = [];

		foreach ($data['translation'] as $key => $value) 
		{
			$row['language_code'] = $key;
			$row['code'] = $data['code'];
			$row['value'] = $value;
			$save = $this->store($row);
		}

		return $save;
	}

	/**
	* Save multi Items to database
	*/
	public function updateItems($data) 
	{
		$row = [];

		foreach ($data['translation'] as $key => $value) 
		{
			$row['language_code'] = $key;
			$row['code'] = $data['code'];
			$row['value'] = $value;
			$save = $this->update($row);
		}

		return $save;
	}


	/**
	* Save item to database
	*/
	public function store($data) 
	{

		$Model = new Translation();
		
		foreach ($data as $key => $value) 
		{
			if (in_array($key, $Model->getFields()))
			{
				$dataArray[$key] = $value;
			}
		}		


		// Return the  object with the new data
    	$Object = Translation::firstOrCreate($dataArray);
		

    	return $Object;
    }
    	

    	
    /**
     * Update Lead
     */
    public function update($data)
    {
		$Model = new Translation();

		$Object = $this->findByCodeLang($data['code'], $data['language_code']);
		
		// Return the  object with the new data
    	$Object ? $Object->update( (array) $data) : $Model->create($data);

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
			
			$delete = Translation::find($id)->delete();

			return true;

		} catch (\Exception $e) {

			throw new \Exception("Error Processing Request " . $e->getMessage(), 1);
			
		}
	}

 
}