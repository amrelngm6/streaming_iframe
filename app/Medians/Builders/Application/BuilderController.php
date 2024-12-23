<?php

namespace Medians\Builders\Application;
use Shared\dbaser\CustomController;
use Shared\simple_html_dom;

use Medians\Builders\Infrastructure\BuilderRepository;
use Medians\Templates\Infrastructure\EmailTemplateRepository;
use Medians\Content\Infrastructure\ContentRepository;
use Medians\Pages\Infrastructure\PageRepository;
use Medians\Hooks\Infrastructure\HookRepository;


class BuilderController extends CustomController 
{	

	
	protected $app;
	protected $repo;
	protected $hookRepo;
	public $contentRepo;
	public $pageRepo;
	public $emailTemplateRepo;
	public $emailController;

	function __construct()
	{
		$this->app = new \Config\APP;

		$this->repo = new BuilderRepository;
		$this->contentRepo = new ContentRepository;
		$this->pageRepo = new PageRepository;
		$this->emailTemplateRepo = new EmailTemplateRepository;
		$this->hookRepo = new HookRepository;

		$this->emailController = new EmailBuilderController;

	}

	/**
	 * Index builder 
	 */ 
	public function index()
	{

		try {
			
			$request = $this->app->request();

			$lang = $request->get('lang') ? $request->get('lang') : $this->app->setLang()->lang;
			$check = $this->pageRepo->findByLang($request->get('page_id'), $lang );

			(isset($check->page_id) && !$check->lang_content) ? $this->contentRepo->handleMissingContent($check, $lang) : null;
			$check->content = $check->lang_content;
			return render('views/admin/builder/index.html.twig', [
				'page' => $check->lang_content ?? [], 
				'item' => $check,
				'current_lang' => $lang,
				'canScrab' => false,
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
					$blocks = $this->repo->get();
					$hooks = $this->hookRepo->get();
					foreach ($hooks as $key => $value) {
						$hooks[$key]->html = $value->hookPlugin()->view(['id'=>$value->id]);
					}
					$hooksList = ['Hooks'=>$hooks];
					echo json_encode(array_merge($blocks, $hooksList));
					break;
					
				case 'email_blocks':
					echo json_encode($this->repo->getEmailBlocks());
					break;
			}

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}


	/**
	 * Load builder meta
	 */ 
	public function meta()
	{
		try {
			
			$request = $this->app->request();

			$check = $this->contentRepo->find($request->get('prefix'));

			render('views/admin/builder/templates/meta.html.twig',['page'=>$check]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	/**
	 * Load builder meta
	 */ 
	public function template_preview()
	{
		try {
			
			$request = $this->app->request();

			$check = $this->contentRepo->find($request->get('prefix'));

			render('views/email/email.html.twig',['msg'=>$check->content]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	/**
	 * Load builder meta
	 */ 
	public function languages()
	{
		try {
			
			$request = $this->app->request();

			if ($request->get('type') == 'email')
			{
				return $this->email_languages();
			}

			$lang = $request->get('lang') ? $request->get('lang') : $this->app->setLang()->lang;
			$check = $this->pageRepo->findByLang($request->get('item_id'), $lang );

			return render('views/admin/builder/templates/languages.html.twig',['page'=>$check, 'current_lang' => $lang]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	/**
	 * Load builder meta
	 */ 
	public function email_languages()
	{
		try {
			
			$request = $this->app->request();
		
			$check = $this->emailTemplateRepo->find($request->get('item_id'));
			
			return render('views/admin/builder/templates/email_languages.html.twig',['page'=>$check]);

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
		
		if (!$request->get('contentJSON') || !$request->get('id'))			
			return true;

		$contentJSON = json_decode($request->get('contentJSON'));
		$check = $this->contentRepo->findById($request->get('id'));
		$check->content = str_replace('data-src', 'src', $contentJSON->contentArea);
		$check->update(['content' => $check->content]);
		echo $check->content;
	}




	/**
	 * Update meta tags
	 */ 
	public function updateMeta()
	{	

		$request = $this->app->request();
		
		if (!$request->get('title') || !$request->get('prefix'))			
			return true;


		return $this->repo->updateMeta($request);
	}




	/**
	 * Submit builder requests
	 */ 
	public function submit()
	{


		try {
			
			$request = $this->app->request();
			$supermode = $request->get('supermode');
			switch ($supermode) 
			{
				case 'configUpdate':
					return $this->updateContent();		
					break;

				case 'configEmailUpdate':
					return $this->emailController->updateContent();		
					break;
				
				case 'updateMeta':
					return $this->updateMeta();		
					break;
				
				case 'insertContent':
					echo $this->repo->find($request->get('id'))->content;
					return true;		
					break;
				
				case 'insertHook':
					$hook = $this->hookRepo->find($request->get('id'));
					echo "[plugin_shortcode  $hook->title id='$hook->id']";
					return true;		
					break;
				
				case 'insertEmailContent':
					echo $this->repo->findEmailBlock($request->get('id'))->content;
					return true;		
					break;

			}

			if ($request->get('prefix') && $supermode == 'updateMeta')
			{

			}

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	

	/**
	 * Load builder mew block page
	 */ 
	public function new_get()
	{
		try {
			$request = $this->app->request();
			$check = $this->contentRepo->find($request->get('prefix'));

			render('views/admin/builder/templates/new.html.twig',[]);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	/**
	 * Store new block
	 */ 
	public function store() 
	{
		
		$request = $this->app->request();
		try {
			$params = [
				'content' => $request->get('content'),
				'category' => $request->get('category'),
			];

			$check = $this->repo->store($params);

			echo isset($check->id) ? "Done $check->id" : 'Failed';

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	/**
	 * Load builder scrab page
	 */ 
	public function scrab_get()
	{
		try {
			$request = $this->app->request();
			$check = $this->contentRepo->find($request->get('prefix'));

			render('views/admin/builder/templates/scrab.html.twig',['page'=>$check]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	/**
	 * Load builder scrab page
	 */ 
	public function module()
	{
		try {
			$request = $this->app->request();
			// $check = $this->contentRepo->find($request->get('prefix'));

			render('views/admin/builder/templates/app.html.twig',[]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	
	/**
	 * Extract sections from html page
	 */
	public function scrapeAndExtractSections($url) 
	{
		// Initialize cURL session
		$ch = curl_init($url);
		
		// Set cURL options
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		// Execute cURL session and get the HTML content
		$htmlContent = curl_exec($ch);
	
		// Check for cURL errors
		if (curl_errno($ch)) {
			echo 'Curl error: ' . curl_error($ch);
			curl_close($ch);
			return;
		}
	
		// Close cURL session
		curl_close($ch);
	
		// Create a SimpleHTMLDom object
		$dom = new simple_html_dom();
		
		// Load HTML content into the DOM parser
		$dom->load($htmlContent);
	
		// Find and extract section elements
		$sections = array();
		foreach ($dom->find('section') as $section) {
			// Add section content to the array
			$sections[] = $section->outertext;
		}
	
		// Clean up the DOM parser
		$dom->clear();
		
		// Output the extracted sections
		return $sections;
	}

	/**
	 * Scrab sections
	 */
	public function scrab()
	{
		$request = $this->app->request();

		$url = $request->get('url');
		$category = $request->get('category');
		$template = $request->get('template');

		$sections = $this->scrapeAndExtractSections($url);

		foreach ($sections as $key => $section) {
			$section = str_replace('assets/','https://ui-themez.smartinnovates.net/items/swoo_html/home_baby/assets/', $section);
			$this->repo->store(['content'=>$section, 'category'=>$category, 'template'=>$template]);
		}

		echo translate('Done');
	}
}
