<?php

namespace Medians\Likes\Application;

use Medians\Likes\Infrastructure\LikeRepository;

use Shared\dbaser\CustomController;

class LikeController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;
	

	

	function __construct()
	{
		$this->repo = new LikeRepository();
	}

	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{
		return [
            [ 'value'=> "like_id", 'text'=> "#"],
            [ 'value'=> "name", 'text'=> translate('name'), 'sortable'=> true ],
            [ 'value'=> "item", 'text'=> translate('Item'),  ],
            [ 'value'=> "rate", 'text'=> translate('Rating'),  ],
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
            [ 'key'=> "like_id", 'title'=> "#", 'column_type'=>'hidden'],
			[ 'key'=> "name", 'title'=> translate('name'), 'disabled'=>true,  'fillable'=> true, 'column_type'=>'text' ],
			[ 'key'=> "email", 'title'=> translate('Email'), 'disabled'=>true,  'fillable'=> true, 'column_type'=>'email' ],
			[ 'key'=> "comment", 'title'=> translate('Comment'), 'disabled'=>true,  'fillable'=> true, 'column_type'=>'text' ],
			[ 'key'=> "rate", 'title'=> translate('Rating'),  'fillable'=> true, 'column_type'=>'number' ],
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
	        'title' => translate('Items Likes'),
	        'items' => $this->repo->get(100),
	        'columns' => $this->columns(),
	        'fillable' => $this->fillable(),
			'object_name'=> 'Like',
			'object_key'=> 'like_id',
	    ]);
	}



	public function store() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	

        	$this->validate($params);

            $returnData = (!empty($this->repo->store($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (\Exception $e) {
        	return array('result'=>$e->getMessage(), 'error'=>1);
        }

		return $returnData;
	}

	public function likeMedia() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	
			
        	$this->validate($params);
			
			$params['customer_id'] = $this->app->customer->customer_id;

			if (!empty($this->repo->checkLiked($params['item_id'], $params['customer_id']))) 
			{
				return $this->delete($params);
			}

            $returnData = (!empty($this->repo->store_media($params))) 
            ? array('success'=>1, 'result'=>translate('Thanks for like'), 'reload'=>0)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (\Exception $e) {
        	return array('result'=>$e->getMessage(), 'error'=>1);
        }

		return $returnData;
	}

	public function likePlaylist() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	
			
        	$this->validate($params);
			
			$params['customer_id'] = $this->app->customer->customer_id;

			if (!empty($this->repo->checkLikedPlaylist($params['item_id'], $params['customer_id']))) 
			{
				return $this->deletePlaylist($params);
			}

            $returnData = (!empty($this->repo->store_playlist($params))) 
            ? array('success'=>1, 'result'=>translate('Thanks for like'), 'reload'=>0)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (\Exception $e) {
        	return array('result'=>$e->getMessage(), 'error'=>1);
        }

		return $returnData;
	}


	public function likeStation() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	
			
        	$this->validate($params);
			
			$params['customer_id'] = $this->app->customer->customer_id;

			if (!empty($this->repo->checkLikedStation($params['item_id'], $params['customer_id']))) 
			{
				return $this->deleteStation($params);
			}

            $returnData = (!empty($this->repo->store_station($params))) 
            ? array('success'=>1, 'result'=>translate('Thanks for like'), 'reload'=>0)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (\Exception $e) {
        	return array('result'=>$e->getMessage(), 'error'=>1);
        }

		return $returnData;
	}

	public function likeChannel() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	
			
        	$this->validate($params);
			
			$params['customer_id'] = $this->app->customer->customer_id;

			if (!empty($this->repo->checkLikedChannel($params['item_id'], $params['customer_id']))) 
			{
				return $this->deleteChannel($params);
			}

            $returnData = (!empty($this->repo->store_channel($params))) 
            ? array('success'=>1, 'result'=>translate('Thanks for like'), 'reload'=>0)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (\Exception $e) {
        	return array('result'=>$e->getMessage(), 'error'=>1);
        }

		return $returnData;
	}

	public function likeArticle() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	
			
        	$this->validate($params);
			
			$params['customer_id'] = $this->app->customer->customer_id;

			if (!empty($this->repo->checklikedArticle($params['item_id'], $params['customer_id']))) 
			{
				return $this->deleteArticle($params);
			}

            $returnData = (!empty($this->repo->store_article($params))) 
            ? array('success'=>1, 'result'=>translate('Thanks for like'), 'reload'=>0)
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
        	throw new \Exception($e->getMessage(), 1);

        	
        }

	}


	public function delete($params) 
	{

        try {

            if ($this->repo->delete($params, $this->app->customer->customer_id))
            {
                return array('success'=>1, 'result'=>translate('Removed from likes'), 'reload'=>0);
            }
            
        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);

        }
	}

	public function deletePlaylist($params) 
	{

        try {
			
            if ($this->repo->deletePlaylist($params, $this->app->customer->customer_id))
            {
                return array('success'=>1, 'result'=>translate('Removed from likes'), 'reload'=>0);
            }
            
        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);

        }
	}

	public function deleteStation($params) 
	{

        try {
			
            if ($this->repo->deleteStation($params, $this->app->customer->customer_id))
            {
                return array('success'=>1, 'result'=>translate('Removed from likes'), 'reload'=>0);
            }
            
        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);

        }
	}

	public function deleteChannel($params) 
	{

        try {
			
            if ($this->repo->deleteChannel($params, $this->app->customer->customer_id))
            {
                return array('success'=>1, 'result'=>translate('Removed from likes'), 'reload'=>0);
            }
            
        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);

        }
	}
	

	public function deleteArticle($params) 
	{

        try {
			
            if ($this->repo->deleteArticle($params, $this->app->customer->customer_id))
            {
                return array('success'=>1, 'result'=>translate('Removed from likes'), 'reload'=>0);
            }
            
        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);
        }
	}

	public function validate($params) 
	{
		$this->app->customer_auth();

		if (empty($params['item_id']))
		{
        	throw new \Exception(translate('Item is required'), 1);
		}

		if (empty($this->app->customer))
		{
        	throw new \Exception(translate('Login first'), 1);
		}


	}

}
