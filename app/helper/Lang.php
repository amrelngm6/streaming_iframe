<?php

namespace helper;

use Medians\Languages\Infrastructure\TranslationRepository;

class Lang
{
	public $lang;
	
	public $repo;
	
	function __construct($lang)
	{
		$this->lang = $lang ?? 'english';
		$this->repo = new TranslationRepository();
	}

	public function load()
	{
		switch ($this->lang) 
		{
			default:
				return array_column($this->repo->findByLang($this->lang), 'value', 'code');
				break;
		}
	}

	
	public function translate($langkey)
	{
		$check = $this->repo->findByCodeLang($langkey, $this->lang);
		return  isset($check->value) ? $check->value : $langkey;
	}

}

