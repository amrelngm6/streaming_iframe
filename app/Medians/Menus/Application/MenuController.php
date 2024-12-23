<?php

namespace Medians\Menus\Application;
use \Shared\dbaser\CustomController;

use Medians\Menus\Infrastructure\MenuRepository;
use Medians\Pages\Infrastructure\PageRepository;
use Medians\Categories\Infrastructure\CategoryRepository;

class MenuController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $pagesRepo;

	protected $categoryRepo;


	function __construct()
	{
		$this->app = new \config\APP;
		$this->repo = new MenuRepository();
		$this->pagesRepo = new PageRepository();
		$this->categoryRepo = new CategoryRepository();
	}



	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{
		return [
            [ 'value'=> "menu_id", 'text'=> "#"],
            [ 'value'=> "name", 'text'=> translate('Name'), 'sortable'=> true ],
            [ 'value'=> "picture", 'text'=> translate('Logo'), 'sortable'=> true ],
            [ 'value'=> "description", 'text'=> translate('Description'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('status'), 'sortable'=> true ],
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
            [ 'key'=> "menu_id", 'title'=> "#", 'column_type'=>'hidden'],
			[ 'key'=> "name", 'title'=> translate('name'), 'required'=>true, 'fillable'=> true, 'column_type'=>'text' ],
			[ 'key'=> "code", 'title'=> translate('Code'), 'required'=>true, 'fillable'=> true, 'column_type'=>'text' ],
			[ 'key'=> "description", 'title'=> translate('description'), 'required'=>true, 'fillable'=> true, 'column_type'=>'textarea' ],
            [ 'key'=> "status", 'title'=> translate('Status'), 'fillable'=>true, 'column_type'=>'checkbox' ],
			[ 'key'=> "picture", 'title'=> translate('Logo'), 'required'=>true, 'fillable'=> true, 'column_type'=>'picture' ],

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
		return render('menus', [
	        'load_vue' => true,
	        'title' => translate('Menus'),
			'columns' => $this->columns(),
			'fillable' => $this->fillable(),
	        'items' => $this->repo->get(),
	        'pages' => $this->pagesRepo->get(),
	        'categories' => $this->categoryRepo->get(),
	    ]);
	}



	/**
	 * Update item to database
	 * 
	 * @return [] 
	*/
	public function update() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {

           	$returnData =  ($this->repo->update($params))
           	? array('success'=>1, 'result'=>translate('Updated'), 'reload'=>false)
           	: array('error'=>'Not allowed');


        } catch (Exception $e) {
            $returnData = array('error'=>$e->getMessage());
        }

        return $returnData;

	}

	/**
	 * Delete item from database
	 * 
	 * @return [] 
	*/
	public function delete() 
	{
		
		$this->app = new \config\APP;

		$params = $this->app->params();

        try {

           	return  ($this->repo->delete($params['menu_id']))
            ? array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1)
           	: array('error'=>translate('Not allowed'));


        } catch (Exception $e) {
            $returnData = array('error'=>$e->getMessage());
        }

        return $returnData;

	}

	public function load() 
	{
		return $this->repo->get();
		
	}

}
