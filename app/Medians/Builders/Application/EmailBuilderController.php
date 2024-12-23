<?php

namespace Medians\Builders\Application;
use Shared\dbaser\CustomController;

use Medians\Builders\Infrastructure\BuilderRepository;
use Medians\Templates\Infrastructure\EmailTemplateRepository;
use Medians\Content\Infrastructure\ContentRepository;


class EmailBuilderController extends CustomController 
{	

	
	protected $app;
	protected $repo;
	public $contentRepo;
	public $emailTemplateRepo;

	function __construct()
	{
		$this->app = new \Config\APP;

		$this->repo = new BuilderRepository;
		$this->contentRepo = new ContentRepository;
		$this->emailTemplateRepo = new EmailTemplateRepository;

	}

	/**
	 * Index builder 
	 */ 
	public function index()
	{

		try {
			
			$request = $this->app->request();
			$check = $this->emailTemplateRepo->findByLang($request->get('template_id'), $request->get('lang'));

			return render('views/admin/builder/email.html.twig', [
				'page' => $check->content, 
				'email_template' => $check, 
				'precode' => isset($check->content) && (substr(trim($check->content), 0, 8) == '<section') ? '' : '<section id="newKeditItem" class="kedit">', 
				'postcode' => isset($check->content) && (substr(trim($check->content), 0, 8) == '<section') ? '' : '</section>', 
			]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}



	/**
	 * Load builder assets
	 */ 
	public function load()
	{

		try {
			
			$request = $this->app->request();
			$page = $request->get('page');
			switch ($page) {
				case 'blocks':
					echo json_encode($this->repo->get());
					break;
			}

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	
	/**
	 * Submit builder requests
	 */ 
	public function updateContent()
	{	

		$request = $this->app->request();
		
		if (!$request->get('contentJSON') || !$request->get('templateId'))			
			return true;

		$contentJSON = json_decode($request->get('contentJSON'));
		$check = $this->emailTemplateRepo->findByLang($request->get('templateId'), $request->get('lang'));
		$langContent = $check->content;	
		$langContent->content = str_replace('data-src', 'src', $contentJSON->contentArea);
		$langContent->save();
		echo $langContent->content;
	}



}
