<?php

namespace Medians\Templates\Application;
use Medians\Templates\Infrastructure\EmailTemplateRepository;
use Medians\Menus\Infrastructure\MenuRepository;
use Medians\Content\Infrastructure\ContentRepository;
use Shared\dbaser\CustomController;

class EmailTemplateController extends CustomController 
{

    public $app;

    public $repo;

    public $contentRepo;

    public $menuRepo;

    function __construct()
    {
        $this->app = new \Config\APP;
        $this->repo = new EmailTemplateRepository;
    }
    

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "template_id", 'text'=> "#"],
            [ 'value'=> "title", 'text'=> translate('Title'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('Status'), 'sortable'=> true ],
			[ 'value'=> 'edit', 'text'=>translate('Edit')],
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
            [ 'key'=> "template_id", 'title'=> "#", 'column_type'=>'hidden'],
            [ 'key'=> "title", 'title'=> translate('Title'), 'required'=>true, 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "content", 'title'=> translate('Content'), 'required'=>true, 'fillable'=> true, 'column_type'=>'editor' ],
            [ 'key'=> "status", 'title'=> translate('Status'), 'fillable'=> true, 'column_type'=>'checkbox' ],
        ];
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
			
		    return render('data_table', [
		        'load_vue' => true,
		        'title' => translate('Templates'),
		        'columns' => $this->columns(),
		        'fillable' => $this->fillable(),
		        'items' => $this->repo->get(),
				'object_name' => $this->repo->getObjectName(),
				'object_key' => 'template_id'
		    ]);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
	}



    
	public function store() 
	{

		$params = $this->app->params();

        try {	

        	$params['created_by'] = $this->app->auth()->id;

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

        	$params['status'] = isset($params['status']) ? $params['status'] : null;

            if ($this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>0);
            }
        

        } catch (\Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }
	}
    


	public function delete() 
	{
		$params = $this->app->params();

        try {

        	$check = $this->repo->find($params['template_id']);

            if ($this->repo->delete($params['template_id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
            }

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        }

	}

	public function validate($params) 
	{
	}
	

    
    
}