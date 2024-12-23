<?php

namespace Medians\Comments\Application;

use Medians\Comments\Infrastructure\CommentRepository;

use Shared\dbaser\CustomController;

class CommentController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;
	

	

	function __construct()
	{
		$this->repo = new CommentRepository();
	}

	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{
		return [
            [ 'value'=> "comment_id", 'text'=> "#"],
            [ 'value'=> "customer.name", 'text'=> translate('name'), 'sortable'=> true ],
            [ 'value'=> "item.name", 'text'=> translate('Item'),  ],
            [ 'value'=> "comment", 'text'=> translate('comment'),  ],
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
            [ 'key'=> "comment_id", 'title'=> "#", 'column_type'=>'hidden'],
			[ 'key'=> "customer.name", 'title'=> translate('Email'), 'disabled'=>true,  'fillable'=> true, 'column_type'=>'email' ],
			[ 'key'=> "comment", 'title'=> translate('Comment'), 'disabled'=>true,  'fillable'=> true, 'column_type'=>'text' ],
			[ 'key'=> "status", 'title'=> translate('status'),  'fillable'=> true, 'column_type'=>'checkbox' ],
        ];
	}

	/**
	 * Admin index items
	 * 
	 * @param Silex\Application $app
	 * @param \Twig\Environment $twig
	 * 
	 */ 
	public function index(  ) 
	{
	    return render('data_table', [
	        'load_vue' => true,
	        'title' => translate('Comments'),
	        'items' => $this->repo->get(100),
	        'columns' => $this->columns(),
	        'fillable' => $this->fillable(),
			'no_create'=> true,
			'object_name'=> 'Comment',
			'object_key'=> 'comment_id',
	    ]);
	}



	public function store() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();
		$customer = $this->app->customer_auth();

        try {	

        	$this->validate($params);

			$params['customer_id'] = $customer->customer_id;

			$store = $this->repo->store($params);

			// $response = isset($params['append']) ? translate('Thanks for your comment') : $this->comment_response([$store]);
			$response = translate('Thanks for your comment');

            $returnData = (!empty($store)) 
            ? array('success'=>1, 'result'=>$response, 'reload'=>0)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (\Exception $e) {
        	return array('result'=>$e->getMessage(), 'error'=>1);
        }

		return $returnData;
	}



	public function update()
	{

		$this->app = new \config\APP;

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


		$this->app = new \config\APP;

		$params = $this->app->params();
		
        try {

            if ($this->repo->delete($params['comment_id']))
            {
                return array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1);
            }
            

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}

	public function validate($params) 
	{

		if (empty($this->app->customer))
		{
        	throw new \Exception(translate('Login first'), 1);
		}

		if (empty($params['item_id']))
		{
        	throw new \Exception(translate('Item required'), 1);
		}
		
		if (empty($params['comment']))
		{
			throw new \Exception(translate('Comment required'), 1);
		}
		
		if (strlen(str_replace(' ','', $params['comment'])) < 1)
		{
			throw new \Exception(translate('Comment required'), 1);
		}

	}


    /**
     * Discover page for frontend
     */
    public function load_stream_comments()
    {
        $this->app = new \config\APP();

        $params = $this->app->params();

		$comments = $this->repo->getStreamComments($params['item_id'], $params['last_id'], 100);

		try 
        {
            return printResponse($this->comment_response($comments));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
	
    /**
     * Station page for frontend
     */
    public function comment_response($comments)
    {
		$settings = $this->app->SystemSetting();

		try {

            return render('views/front/'.($settings['template'] ?? 'default').'/includes/comment-block.html.twig', [
				'comments' => $comments
            ], 'output');
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

}
