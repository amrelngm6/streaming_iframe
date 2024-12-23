<?php
namespace Medians\Newsletters\Application;

use Shared\dbaser\CustomController;

use Medians\Newsletters\Infrastructure\NewsletterRepository;
use Medians\Newsletters\Infrastructure\SubscriberRepository;
use Medians\Routes\Infrastructure\RouteRepository;

class SubscriberController extends CustomController 
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $newsletterRepo;


	function __construct()
	{
		$this->app = new \config\APP;

		$this->repo = new SubscriberRepository();
		$this->newsletterRepo = new NewsletterRepository();

    }




	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "subscriber_id", 'text'=> "#"],
            [ 'value'=> "newsletter.name", 'text'=> translate('Newsletter'), 'sortable'=> true ],
            [ 'value'=> "name", 'text'=> translate('name'), 'sortable'=> true ],
            [ 'value'=> "email", 'text'=> translate('email'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('Status')],
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
            [ 'key'=> "subscriber_id", 'title'=> "#", 'column_type'=>'hidden'],
            [ 'key'=> "name", 'title'=> translate('newsletter_name'),  'sortable'=> true, 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "email", 'title'=> translate('newsletter_name'),'sortable'=> true, 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "newsletter_id", 'title'=> translate('Newsletter'),  'withLabel'=>true,
				'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'name', 'column_key' => 'newsletter_id',
				'data' => $this->newsletterRepo->get(100)  
			],
			[ 'key'=> "status", 'title'=> translate('status'), 'fillable'=> true, 'column_type'=>'checkbox' ],
			
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
		        'title' => translate('Subscribers'),
		        'columns' => $this->columns(),
		        'fillable' => $this->fillable(),
		        'items' => $this->repo->get(),
				'object_name' => 'Subscriber',
				'object_key' => 'subscriber_id'
		    ]);

        } catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
	}


	public function store() 
	{

		$params = $this->app->params();

        try {	

			try {

                $params['status'] = !empty($params['status']) ? 'on' : null;

				$this->validate($params);

			} catch (\Throwable $e) {
	        	return array('result'=>$e->getMessage(), 'error'=>1);
			}

            $returnData = (!empty($this->repo->store($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>0)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (\Exception $es) {
        	return array('result'=>$es->getMessage(), 'error'=>1);
        }

		return $returnData;
	}


	public function update()
	{
		$params = $this->app->params();

        try {

        	$params['status'] = !empty($params['status']) ? 'on' : null;

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

        	$check = $this->repo->find($params['subscriber_id']);

            if ($this->repo->delete($params['subscriber_id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
            }
            
        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);
        }
	}

	


	public function validate($params) 
	{

		if (empty($params['email']))
		{
			throw new \Exception(translate('EMAIL_EMPTY'), 0);
		}

		if (!empty($this->repo->findByEmail($params['email'])))
		{
			throw new \Exception(translate('EMAIL_FOUND'), 0);
		}

	}

}