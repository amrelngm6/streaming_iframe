<?php
namespace Medians\Newsletters\Application;

use Shared\dbaser\CustomController;

use Medians\Newsletters\Infrastructure\NewsletterRepository;
use Medians\Newsletters\Infrastructure\SubscriberRepository;

class NewsletterController extends CustomController 
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	public $newsletterTypeRepo;
	

	function __construct()
	{

		$this->app = new \config\APP;

		$this->repo = new NewsletterRepository();
		$this->newsletterTypeRepo = new NewsletterRepository();
	}




	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "newsletter_id", 'text'=> "#"],
            [ 'value'=> "name", 'text'=> translate('newsletter_name'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('status')],
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
            [ 'key'=> "newsletter_id", 'title'=> "#", 'column_type'=>'hidden'],
            [ 'key'=> "name", 'title'=> translate('newsletter_name'), 'required'=>true, 'sortable'=> true, 'fillable'=> true, 'column_type'=>'text' ],
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
		        'title' => translate('Newsletters'),
		        'columns' => $this->columns(),
		        'fillable' => $this->fillable(),
		        'items' => $this->repo->get(),
				'object_name' => 'Newsletter',
				'object_key' => 'newsletter_id'
		    ]);
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}



	public function validate($params) 
	{

		if (empty($params['name']))
		{
			throw new \Exception(translate('NAME_EMPTY'), 0);
		}

	}



	public function store() 
	{

		$params = $this->app->params();

        try {	

			try {
				$params['status'] = !empty($params['status']) ? $params['status'] : null;

				$this->validate($params);

			} catch (\Throwable $e) {
	        	return array('result'=>$e->getMessage(), 'error'=>1);
			}


            $returnData = (!empty($this->repo->store($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
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

        	$params['status'] = !empty($params['status']) ? $params['status'] : null;

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

        	$check = $this->repo->find($params['newsletter_id']);


            if ($this->repo->delete($params['newsletter_id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
            }
            
        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        }
	}

	

	public function getNewsletter($newsletter_id)
	{

		$auth = $this->app->auth();

        try {

			if (!empty($auth))
			{
				$check = $this->repo->find($newsletter_id);
				echo json_encode($check);
			}

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        }
	}
	
	
}