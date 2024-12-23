<?php

namespace Medians\Blog\Application;
use Shared\dbaser\CustomController;

use Medians\Blog\Infrastructure\BlogRepository;
use Medians\Categories\Infrastructure\CategoryRepository;


class BlogController extends CustomController 
{

	/**
	* @var Object
	*/
	protected $app;
	protected $repo;
	protected $categoryRepo;

	

	function __construct()
	{

		$this->app = new \config\APP;

		$this->repo = new BlogRepository();
		$this->categoryRepo = new CategoryRepository();

	}



	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{
		return [
            [ 'value'=> "id", 'text'=> "#"],
            [ 'value'=> "picture", 'text'=> translate('Picture'), 'sortable'=> true ],
            [ 'value'=> "lang_content.title", 'text'=> translate('Title'), 'sortable'=> true ],
            [ 'value'=> "path", 'text'=> translate('Path'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('status'), 'sortable'=> true ],
            [ 'value'=> "builder", 'text'=> translate('Page Builder'), 'sortable'=> true ],
            [ 'value'=> "edit", 'text'=> translate('edit')  ],
            [ 'value'=> "delete", 'text'=> translate('delete')  ],
        ];
	}

	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
            [ 'key'=> "id", 'title'=> "#", 'column_type'=>'hidden'],
			[ 'key'=> "title", 'title'=> translate('Title'), 'fillable'=> true, 'custom_field'=>true, 'column_type'=>'text' ],
			[ 'key'=> "author_name", 'custom_field' => true, 'title'=> translate('Author name'), 'fillable'=> true, 'custom_field'=>true, 'column_type'=>'text' ],
			[ 'key'=> "picture", 'title'=> translate('picture'), 'required'=>true, 'fillable'=> true, 'column_type'=>'picture' ],
            [ 'key'=> "status", 'title'=> translate('Status'), 'fillable'=>true, 'column_type'=>'checkbox' ],

        ];
	}


	/**
	 * Admin index items
	 * 
	 * @param Silex\Application $app
	 * @param \Twig\Environment $twig
	 * 
	 */
	public function index() 
	{
		
		return render('blog', 
		[
			'load_vue' => true,
			'title' => translate('Blog'),
			'columns' => $this->columns(),
			'fillable' => $this->fillable(),
			'items' => $this->repo->get(),
			'object_name' => 'Blog',
			'object_key' => 'id',
		]);
	}

	/**
	 * Admin index items
	 * 
	 * @param Silex\Application $app
	 * @param \Twig\Environment $twig
	 * 
	 */
	public function article($id) 
	{
		
		try {

			return render('blog_wizard', [
		        'load_vue' => true,
		        'title' => translate('Blog page'),
		        'item' => $this->repo->find($id),
		    ]);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
	}



	public function store() 
	{

		$params = $this->app->request()->get('params');

        try {	

        	$params['created_by'] = $this->app->auth()->id;
        	
			$params['status'] = !empty($params['status']) ? 'on' : 0;

			// $params['content_langs'] = ['english'=> $params, 'arabic'=>$params];
        	
            $returnData = (!empty($this->repo->store($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (Exception $e) {
        	throw new Exception(json_encode(array('result'=>$e->getMessage(), 'error'=>1)), 1);
        }

		return $returnData;
	}



	public function update()
	{
		$params = $this->app->request()->get('params');

        try {

        	$params['status'] = !empty($params['status']) ? 'on' : 0;

            if ($this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>0);
            }
        

        } catch (\Exception $e) {
        	throw new \Exception("Error Processing Request".$e->getMessage(), 1);
        	
        }

	}


	public function delete() 
	{

		$params = $this->app->request()->get('params');

        try {

        	$check = $this->repo->find($params['id']);

            if ($this->repo->delete($params['id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
            }
            

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}

	/**
	 * Front page 
	 * @var Int
	 */
	public function page($contentObject)
	{

		try {
			
			$item = $this->repo->find($contentObject->item_id);
			$item->addView();
			$settings = $this->app->SystemSetting();

			return render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
		        'item' => $item,
		        'similar_articles' => $this->repo->similar($item, 3),
		        'layout' => 'blog/article',
		    ]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	} 

	/**
	 * Front page 
	 * @var Int
	 */
	public function list()
	{
		$request =  $this->app->request();
		$params =  $this->app->params();

		try {

			$settings = $this->app->SystemSetting();
			
	        $params['limit'] = $settings['view_articles_limit'] ?? 10;
			$list = $this->repo->getWithFilter($params);

			return render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'item'=> ['name'=> translate('Top Articles'),  'description'=> translate('List of our latest articles')], 
		        'list' => $list,
		        'layout' => 'blog/blog',

		    ]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
	} 


}