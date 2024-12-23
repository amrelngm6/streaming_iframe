<?php
namespace Medians\Forms\Application;

use Shared\dbaser\CustomController;

use Medians\Forms\Infrastructure\ContactFormRepository;

class ContactFormController extends CustomController 
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	function __construct()
	{

		$this->app = new \config\APP;

		$this->repo = new ContactFormRepository();
	}




	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "message_id", 'text'=> "#",'sortable'=> true ],
            [ 'value'=> "email", 'text'=> translate('email'), 'sortable'=> true ],
            [ 'value'=> "name", 'text'=> translate('Name'), 'sortable'=> true ],
            [ 'value'=> "phone", 'text'=> translate('phone'), 'sortable'=> true ],
            [ 'value'=> "message", 'text'=> translate('message'), 'sortable'=> true ],
            [ 'value'=> "delete", 'text'=> translate('delete') ],
        ];
	}

	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{
		return [
            [ 'key'=> "message_id", 'title'=> "#", 'column_type'=>'hidden'],
            [ 'key'=> "name", 'title'=> translate('name'), 'sortable'=> true, 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "email", 'title'=> translate('email'), 'sortable'=> true, 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "status", 'title'=> translate('status'), 'sortable'=> true, 'fillable'=>true, 'column_type'=>'checkbox', 'withlabel'=>true ],
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
		
		try 
		{
		    return render('contact_forms', 
			[
		        'load_vue' => true,
		        'title' => translate('Forms messages'),
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
		$params = $this->app->params();

        try {	

			$this->validate($params);

            $returnData = (!empty($this->repo->store($params))) 
            ? array('success'=>1, 'result'=>translate('thanks_for_sending'))
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (Exception $e) {
        	$returnData  = json_encode(array('result'=>$e->getMessage(), 'error'=>1));
        }

		return $returnData;
	}




	public function delete() 
	{

		$params = $this->app->params();

        try {

        	$check = $this->repo->find($params['message_id']);

            if ($this->repo->delete($params['message_id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
            }

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request ".$e->getMessage(), 1);
        	
        }

	}

	public function validate($params) 
	{
		if (empty($params['email']))
		{
        	throw new \Exception(json_encode(array('result'=>translate('Email EMPTY'), 'error'=>1)), 1);
		}
		
	}


}