<?php

namespace Medians\Templates\Application;
use Medians\Templates\Infrastructure\WebTemplateRepository;
use Medians\Menus\Infrastructure\MenuRepository;
use Medians\Content\Infrastructure\ContentRepository;
use Shared\dbaser\CustomController;

class WebTemplateController extends CustomController 
{

    public $app;

    public $repo;

    public $contentRepo;

    public $menuRepo;

    function __construct()
    {
        $this->app = new \Config\APP;
        $this->repo = new WebTemplateRepository;
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
			[ 'value'=> 'details', 'text'=>translate('Details')],
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
            [ 'key'=> "folder_name", 'title'=> translate('Temlate Folder name'), 'required'=>true, 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "status", 'title'=> translate('Status'), 'fillable'=> true, 'column_type'=>'checkbox' ],
            [ 'key'=> "picture", 'title'=> translate('Picture'), 'required'=>true, 'fillable'=> true, 'column_type'=>'picture' ],
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
        	$params['content'] = $this->handleLangs($params);

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
		$params = $this->app->params();

        try {

        	$params['status'] = isset($params['status']) ? $params['status'] : null;
        	$params['homepage'] = isset($params['homepage']) ? $params['homepage'] : null;

            if ($this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>1);
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



    
    
}