<?php

namespace Medians\Pages\Application;
use Medians\Pages\Infrastructure\PageRepository;
use Medians\Menus\Infrastructure\MenuRepository;
use Medians\Content\Infrastructure\ContentRepository;
use Medians\Pages\Domain\Page;
use Medians\Blog\Domain\Blog;
use Medians\Categories\Domain\Category;
use Medians\Categories\Domain\Genre;
use Medians\Categories\Domain\BookGenre;
use Medians\Categories\Domain\VideoGenre;
use Shared\dbaser\CustomController;

class PageController extends CustomController 
{

    public $app;

    public $repo;

    public $contentRepo;

    public $menuRepo;

    function __construct()
    {
        $this->app = new \Config\APP;
        $this->repo = new PageRepository;
        $this->contentRepo = new ContentRepository;
        $this->menuRepo = new MenuRepository;
    }
    

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "page_id", 'text'=> "#"],
            [ 'value'=> "lang_content.title", 'text'=> translate('Title'), 'sortable'=> true ],
            [ 'value'=> "lang_content.prefix", 'text'=> translate('link'), 'sortable'=> true ],
            [ 'value'=> "homepage", 'text'=> translate('Is Homepage'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('Status'), 'sortable'=> true ],
			['value'=>'details', 'text'=>translate('Details')],
			['value'=>'edit', 'text'=>translate('Edit')],
			['value'=>'delete', 'text'=>translate('Delete')],
        ];
	}

	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable () 
	{

		return [
            [ 'key'=> "page_id", 'title'=> "#", 'column_type'=>'hidden'],
            [ 'key'=> "title", 'title'=> translate('Title'), 'required'=>true, 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "prefix", 'title'=> translate('prefix'), 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "homepage", 'title'=> translate('Is Homepage'), 'fillable'=> true, 'column_type'=>'checkbox' ],
            [ 'key'=> "status", 'title'=> translate('Status'), 'fillable'=> true, 'column_type'=>'checkbox' ],
        ];
	}

	

	/**
	 * Admin page by ID
	 * 
	 */ 
    public function admin_item($id)
    {
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

		$this->app->customer_auth();

		$params = $this->app->params();

		try 
        {
            $item = $this->repo->find($id);

			return render('page_wizard', [
		        'load_vue' => true,
		        'title' => translate('Frontend Pages'),
		        'columns' => $this->columns(),
		        'fillable' => $this->fillable(),
		        'item' => $item,
				'model' => 'Page',
		    ]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    


	/**
	 * Admin index items
	 * 
	 * @param Silex\Application $app
	 * @param \Twig\Environment $twig
	 * 
	 */ 
	public function index( ) 
	{
		
		try {
			
		    return render('pages', [
		        'load_vue' => true,
		        'title' => translate('Pages'),
		        'columns' => $this->columns(),
		        'fillable' => $this->fillable(),
		        'items' => $this->repo->get(),
		    ]);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
	}



    
	public function store() 
	{

		$params = (array) json_decode($this->app->request()->get('params'), true);

        try {	


        	$params['created_by'] = $this->app->auth()->id;
        	$params['content'] = $params['content_langs'];

            $returnData = (!empty($this->repo->store($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (Exception $e) {
        	$returnData = json_encode(array('result'=>$e->getMessage(), 'error'=>1));
        }

		return $returnData;
	}



	public function update()
	{

		$params = (array) json_decode($this->app->request()->get('params'), true);

        try {

        	$params['content'] = $params['content_langs'];
			
			
            if ($this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>0);
            }
        

        } catch (\Exception $e) {
        	throw new \Exception("Error Processing Request {$e->getMessage()} ", 1);
        	
        }
	}
    


	public function delete() 
	{
		$params = $this->app->params();

        try {

        	$check = $this->repo->find($params['page_id']);

            if ($this->repo->delete($params['page_id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
            }

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        }

	}

	public function validate($params) 
	{
		if (empty($params['content']['en']['title']))
		{
        	throw new \Exception(json_encode(array('result'=>translate('NAME_EMPTY'), 'error'=>1)), 1);
		}
	}
	

	public function handleLangs($params) 
	{
		$langsRepo = new \Medians\Languages\Infrastructure\LanguageRepository();
		$langs = $langsRepo->getActive();
		$fields = [];
		foreach ($langs as $row) 
		{
			$fields[$row->language_code] = ["title"=> $params['title']];
		}
		return $fields;	
	}



    
    /**
     * Homepage for frontend
     */
    public function homepage()
    {
		
        $page = $this->repo->homepage();

		$settings = $this->app->SystemSetting();

		try {
			
			$page->addView();

            return printResponse(processShortcodes(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'page' => $page,
                'item' => $page,
                'app' => $this->app,
				'layout' => 'subpage'
            ], 'output')));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
    }
    
	
	
    
    /**
     * Homepage for frontend
     */
    public function subPage($prefix)
    {
		
        $page = $this->repo->findByPrefix($prefix);
		
		$categoryRepo = new \Medians\Categories\Infrastructure\CategoryRepository;
		$mediaItemRepo = new \Medians\Media\Infrastructure\MediaItemRepository;
		$customerRepo = new \Medians\Customers\Infrastructure\CustomerRepository;

		$settings = $this->app->SystemSetting();

		try {
			
			$page->addView();

            return printResponse(processShortcodes(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'subpage' => $page,
                'item' => $page,
                'app' => $this->app,
				'genres' => $categoryRepo->getGenres(),
				'channels' => $customerRepo->get(),
				'layout' => 'subpage'
            ], 'output')));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
    }



    /**
     * Sub-pages for frontend
     */
    public function page($prefix)
    {

		try {

			$pageContent = $this->contentRepo->find(urldecode($prefix));
	
			return $this->handlePageObject($pageContent);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

	public function handlePageObject($pageContent)
	{
		if (isset($pageContent->item))
		{
			$_SESSION['lang'] = $pageContent->lang;

			switch (get_class($pageContent->item)) {
				case Blog::class:
					return (new \Medians\Blog\Application\BlogController)->page($pageContent);
					break;
		
				case Genre::class:
					return (new \Medians\Categories\Application\GenreController)->page($pageContent);
					break;
		
				case VideoGenre::class:
					return (new \Medians\Categories\Application\VideoGenreController)->page($pageContent);
					break;
				
				case Page::class:
					return $this->subPage($pageContent->prefix);
					break;
			}
			
		}

		return throw new \Exception(translate('Page not found'), 1);
		
	}
    
	
    /**
     * Discover page for frontend
     */
    public function search()
    {
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();
		$categoryRepo = new \Medians\Categories\Infrastructure\CategoryRepository;
		$mediaItemRepo = new \Medians\Media\Infrastructure\MediaItemRepository;

        $params['limit'] = $settings['view_audio_limit'] ?? null;
        $params['type'] = 'audio';
        $list = $mediaItemRepo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
				'lang' =>  new \helper\Lang($_SESSION['lang']),
				'list' => $list,
                'genres' => $categoryRepo->getGenres(),
                'layout' => 'search'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
}