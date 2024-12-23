<?php

namespace Medians\Notifications\Application;
use \Shared\dbaser\CustomController;

use Medians\Notifications\Infrastructure\NotificationEventRepository;
use Medians\Templates\Infrastructure\EmailTemplateRepository;

class NotificationEventController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $emailTemplatesRepo;

	function __construct()
	{
		$this->repo = new NotificationEventRepository();
		$this->emailTemplatesRepo = new EmailTemplateRepository();
	}



	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [
                'value'=> "id",
                'text'=> '#',
                'sortable'=> false,
				'width' => 30,
            ],
            [
				'value'=> "title",
                'text'=> translate('name'),
                'sortable'=> false,
            ],
            [
                'value'=> "model_title",
                'text'=> translate('model'),
                'sortable'=> true,
            ],
            [
                'value'=> "receiver_title", 'text'=> translate('receiver_model'), 'width' => 100, 'sortable'=> true,
            ],
            [
                'value'=> "template.title", 'text'=> translate('temmplate'), 'sortable'=> true,
            ],
            [
                'value'=> "subject", 'text'=> translate('subject'), 'width' => 100, 'sortable'=> true,
            ],
            [
                'value'=> "status",  'text'=> translate('status'), 'width' => 50, 'sortable'=> true,
			],
			
            [ 'value'=> "edit", 'text'=> translate('edit'), 'width'=>50  ],
            [ 'value'=> "delete", 'text'=> translate('delete'), 'width'=>50  ],
        ];
	}


	
	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
            [ 'key'=> "id", 'title'=> "#",'column_type'=>'hidden'],
            [ 'key'=> "title", 'title'=> translate('title'), 'fillable'=> true, 'column_type'=>'text', 'required'=> true ],
			[ 'key'=> "receiver_model", 'title'=> translate('Receiver model'), 'withLabel'=> true, 'fillable'=> true, 'column_type'=>'select', 'required'=> true, 'text_key'=>'title', 'data'=>$this->loadReceiverModels('receiver_model') ],
			[ 'key'=> "model", 'title'=> translate('Model'), 'withLabel'=> true, 'fillable'=> true, 'column_type'=>'select', 'required'=> true, 'text_key'=>'title',  'data'=>$this->loadModels('model') ],
			
			[ 'key'=> "action", 'title'=> translate('Action'), 'help_text' => translate('When the event should be run'),'withLabel'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title',  'required'=> true, 'data'=>[
				['action'=>'create','title'=>translate('On Create')],
				['action'=>'update','title'=>translate('On Update')],
				['action'=>'delete','title'=>translate('On delete')],
			] ],
			[ 'key'=> "action_field", 'title'=> translate('Action field'), 'help_text' => translate('You can choose specified field to listen for updates or leave empty to apply on all fields'),'fillable'=> true, 'column_type'=>'text' ],
			[ 'key'=> "action_value", 'title'=> translate('Action value'), 'help_text' => translate('You can choose specified value to listen for updated field or leave empty to apply on any change'), 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "subject", 'title'=> translate('subject'), 'fillable'=> true, 'column_type'=>'text', 'required'=> true ],
			[ 'key'=> "template_id", 'title'=> translate('Email template'), 'help_text' => translate('You can manage the templates from the Email Template page'),'withLabel'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title',  'required'=> true, 
				'data'=> $this->emailTemplatesRepo->get()
			],
            [ 'key'=> "body_text", 'title'=> translate('Notification text'),  'help_text' => translate('This text used for mobile APP nofitications'), 'fillable'=> true, 'column_type'=>'textarea', 'required'=> true ],
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
	public function index() 
	{
		return render('notifications_events', [
	        'load_vue' => true,
	        'title' => translate('Notifications events'),
	        'items' => $this->repo->get(),
	        'columns' => $this->columns(),
			'fillable' => $this->fillable(),
			'fillable_grouped' => $this->groped_fillable(),
	    ]);
	}


	/**
	 * Supported models for events
	 *
	 */
	public function loadModels($key)
	{
		$models = $this->repo->loadModels();
		$data = [];
		foreach ($models as $i => $value) {
			$data[$i] = [$key=>$value, 'title'=>$i] ;
		}
		return $data;
	}  

	public function groped_fillable()
	{
		$group = [];
		foreach ($this->fillable() as $key => $value) {
			$group[$value['key']] = $value;
		}
		return $group;
	}

	/**
	 * Supported receivers models for events
	 */
	public function loadReceiverModels($key)
	{
		$models = $this->repo->loadReceiverModels();
		$data = [];
		foreach ($models as $i => $value) {
			$data[$i] = [$key=>$value, 'title'=>$i] ;
		}
		return $data;
	}  


	/**
	 * Store item to database
	 * 
	 * @return [] 
	*/
	public function store() 
	{	
		$this->app = new \config\APP;
        
		$params = $this->app->params();

        try {
        	
			$params['created_by'] = $this->app->auth()->id;

			$params['status'] = (isset($params['status']) && $params['status'] != 'false') ? 'on' : null;

            return ($this->repo->store($params))
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>translate('Error'), 'error'=>1);


        } catch (Exception $e) {
            return array('error'=>$e->getMessage());
        }
	
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

			$params['status'] = (isset($params['status']) && $params['status'] != 'false') ? 'on' : null;

           	$returnData =  ($this->repo->update($params))
           	? array('success'=>1, 'result'=>translate('Updated'), 'reload'=>1)
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

           	return  ($this->repo->delete($params['id']))
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
           	: array('error'=>translate('Not allowed'));


        } catch (Exception $e) {
            $returnData = array('error'=>$e->getMessage());
        }

        return $returnData;

	}



}
