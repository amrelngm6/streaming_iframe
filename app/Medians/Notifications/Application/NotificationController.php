<?php

namespace Medians\Notifications\Application;
use \Shared\dbaser\CustomController;

use Medians\Notifications\Infrastructure\NotificationRepository;

class NotificationController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;
	


	function __construct()
	{
		$this->repo = new NotificationRepository();
	}


	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            // [ 'value'=> "id", 'text'=> "#",'sortable'=> true ],
            [ 'value'=> "date", 'text'=> translate('date'), 'sortable'=> true ],
            [ 'value'=> "subject", 'text'=> translate('subject'), 'sortable'=> true ],
            [ 'value'=> "body_text", 'text'=> translate('Message'), 'sortable'=> true ],
            [ 'value'=> "delete", 'text'=> translate('delete') ],
        ];
	}

	

	/**
	 * Admin index items
	 * 
	 */ 
	public function index() 
	{
		$app = new \Config\APP;
		return render('notifications', [
	        'load_vue' => true,
	        'title' => translate('Notifications'),
	        'items' => $this->repo->get($app->auth()),
	        'columns' => $this->columns(),
			'no_create'=> true

	    ]);
	}

	/**
	 * Delete item from database
	 * 
	 * @return [] 
	*/
	public function delete() 
	{
		
		$app = new \config\APP;

		$params = $app->request()->get('params');

        try {

           	return  ($this->repo->delete($params['id']))
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
           	: array('error'=>translate('Not allowed'));


        } catch (Exception $e) {
            $returnData = array('error'=>$e->getMessage());
        }

        return $returnData;

	}

	/**
	 * Update item status at database 
	 * 
	 * from Web
	 * 
	 * @return [] 
	*/
	public function read_notification() 
	{
		
		$app = new \config\APP;

		$params = $app->request()->get('params');

        try {
           	
           	response($this->repo->update(['id'=>$params['id'],'status' => 'read'])
            ? array('success'=>1, 'result'=>translate('updated'))
           	: array('error'=>translate('Not allowed')));

        } catch (Exception $e) {
            return array('error'=>$e->getMessage());
        }

	}



	/**
	 * Delete item from database
	 * 
	 * @return [] 
	*/
	public function check_notification() 
	{
		
		$app = new \config\APP;

		$params = $app->request()->get('params');

        try {

        	$check = $this->repo->get($app->auth(), $params['last_id']);

           	echo  (count($check))
            ? json_encode($this->loadLatestNotifications($check))
           	: 0;


        } catch (Exception $e) {
            return array('error'=>$e->getMessage());
        }

	}



	/**
	 * Admin index items
	 * 
	 * @param Silex\Application $app
	 * @param \Twig\Environment $twig
	 * 
	 */ 
	public function latest_notifications($last_id=0) 
	{
		$app = new \config\APP;

		$items = $this->repo->get($app->auth(), 50, $last_id);

		return render('notifications', $this->loadLatestNotifications($items));
	}

	/**
	 * Load latest notifications based on role and limit
	 * 
	 */
	public function loadLatestNotifications($items=0)
	{
		$firstitem = $items->first();
		
		return [
	        'load_vue' => true,
	        'title' => translate('Notifications'),
	        'last_id' => !empty($firstitem) ? $firstitem->id : 0,
	        'items' => $items,
	        'total_count' => $items->count(),
	        'new_count' => $items->where('status', 'new')->count(),
	    ];
	}  



	/**
	 * Load latest notifications at mobile
	 * 
	 */
	public function loadLatestMobileNotifications()
	{
		$app = new \config\APP;
		$user = $app->auth();

		if (isset($user->customer_id))
		{
			$items = $this->repo->loadParentNotifications($user->customer_id, 10, 0);
		}
		
		
		if (empty($items))
			return null;

		$firstitem = $items->first();
		return [
	        'last_id' => !empty($firstitem) ? $firstitem->id : 0,
	        'items' => $items,
	        'total_count' => $items->count(),
	        'new_count' => $items->where('status', 'new')->count(),
	    ];
	}  









	/**
	 * Update item to database
	 * 
	 * @return [] 
	*/
	public function update() 
	{

		$app = new \config\APP;

		$params = $this->app->params();

        try {

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
	 * from App
	 * 
	 * @return [] 
	*/
	public function delete_notification() 
	{
		
		$app = new \config\APP;

		$params = $this->app->params();

		$user = $app->auth();

        try {

           	$returnData =  ($this->repo->delete($params['id']))
           	? array('success'=>1, 'result'=>translate('Updated'), 'reload'=>1)
           	: array('error'=>'Not allowed');


        } catch (Exception $e) {
            $returnData = array('error'=>$e->getMessage());
        }

        return $returnData;
	}

}
