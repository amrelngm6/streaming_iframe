<?php

namespace Medians\Customers\Application;
use \Shared\dbaser\CustomController;

use Medians\Customers\Infrastructure\CustomerRepository;


class CustomerController extends CustomController
{

	protected $app;

	/*
	/ @var new CustomerRepository
	*/
	protected $repo;
	
	protected $orderRepo;



	function __construct()
	{

		$this->app = new \config\APP;

		$this->repo = new CustomerRepository();

	}


	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{
		return [
            [ 'value'=> "picture", 'text'=> translate('Picture'),  ],
            [ 'value'=> "name", 'text'=> translate('name'), 'sortable'=> true ],
            [ 'value'=> "email", 'text'=> translate('Email'),  ],
            [ 'value'=> "field.phone", 'text'=> translate('Phone'),  ],
            [ 'value'=> "subscription.package.name", 'text'=> translate('Subscription'),  ],
            [ 'value'=> "info", 'text'=> translate('info')  ],
            // [ 'value'=> "delete", 'text'=> translate('delete')  ],
        ];
	}



	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
            [ 'key'=> "customer_id", 'title'=> "#", 'column_type'=>'hidden'],
			[ 'key'=> "name", 'title'=> translate('Name'), 'required'=>true,  'fillable'=> true, 'column_type'=>'text' ],
			[ 'key'=> "email", 'title'=> translate('Email'), 'required'=>true,  'fillable'=> true, 'column_type'=>'email' ],
			[ 'key'=> "mobile", 'title'=> translate('Mobile'), 'required'=>true,  'fillable'=> true, 'column_type'=>'phone' ],
			[ 'key'=> "birth_date", 'title'=> translate('Date of birth'), 'required'=>true,  'fillable'=> true, 'withLabel'=> true, 'column_type'=>'date' ],
			[ 'key'=> "gender", 'title'=> translate('Gender'),  'fillable'=> true, 'withLabel'=> true, 'column_type'=>'select', 'required'=> true, 'text_key'=>'title',  'data' => 
				[['gender'=> 'male', 'title'=>translate('Male')], ['gender'=> 'female', 'title'=>translate('Female')]]	
			],
			[ 'key'=> "picture", 'title'=> translate('Picture'), 'withLabel'=> true,   'fillable'=> true, 'column_type'=>'file'
			],
        ];
	}

	/**
	 * Index page
	 * 
	 */
	public function index()
	{
		return render('artists', [
			'items' =>  $this->repo->get(),
	        'title' => translate('Customers'),
	        'load_vue' => true,
			'columns' =>  $this->columns(),
			'fillable' =>  $this->fillable(),
			'object_name' => 'Customer',
			'object_key' => 'customer_id',

	    ]);
	} 




	/**
	*  Store item
	*/
	public function store() 
	{


		$params = $this->app->params();

		try {	

			if (empty($params['name']))
	        	return array('error'=>translate('Name is required'), 'result'=>translate('Name is required'));

			if (empty($params['mobile']))
	        	return array('error'=> translate('Phone is required'), 'result'=> translate('Phone is required'));

			if (strlen($params['mobile']) != 11)
	        	return array('error'=> translate('MOBILE_ERR'), 'result'=> translate('MOBILE_ERR') );

			$params['created_by'] = $this->app->auth()->id;
			$Item = $this->repo->store($params);

        	return array('success'=>1, 'result'=> $Item, 'reload'=>1);

        } catch (Exception $e) {
            return  array('error'=>$e->getMessage());
        }
	}



	/**
	*  Update item
	*/
	public function update() 
	{
		$customer = $this->app->customer_auth();
		$params = $this->app->params();

		$mediaRepo = new \Medians\Media\Infrastructure\MediaRepository;

		try {

            $files = $this->app->request()->files;

            foreach ($files as $key => $value) {
                if ($value) {
                    $picture = $mediaRepo->upload($value);
                    $params['picture'] = $mediaRepo->_dir.$picture;
                }
            }
            
			$params['customer_id'] = $customer->customer_id;

            return (!empty($this->repo->update($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (Exception $e) {
            return  array('error'=>$e->getMessage());
        }
	}

    

	/**
	*  Update item
	*/
	public function updatePassword() 
	{
		$customer = $this->app->customer_auth();
		$params = $this->app->params();

		try {

            $oldPassword = $params['old_password'];
            $newPassword = $params['new_password'];
            $confirmPassword = $params['confirm_password'];

			if ($confirmPassword != $newPassword)
				throw new \Exception(translate("Paswords not matched"), 1);
				

			$params['customer_id'] = $customer->customer_id;


            return (!empty($this->repo->checkUpdatePassword($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (Exception $e) {
            return  array('error'=>$e->getMessage());
        }
	}

    
    /**
     * Channels list page for frontend
     */
    public function channels()
    {
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

        $params = $this->app->params();
        $params['limit'] = $settings['view_artists_limit'] ?? null;
        $list = $this->repo->getWithFilter($params);

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item'=> ['name'=> translate('Top Artists'),  'description'=> translate('Follow our top Artists')], 
                'list' => $list,
                'layout' => 'artist/artists'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
	
    
    /**
     * search Channels list page for frontend
     */
    public function search()
    {
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

        $params = $this->app->params();
        $params['limit'] = $settings['view_artists_limit'] ?? null;
        $list = $this->repo->getWithFilter($params);

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'layout' => 'search/search',
                'sub_layout' => 'artist',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

	    /**
     * Artist page for frontend
     */
    public function artist($artist_name)
    {
        
		$settings = $this->app->SystemSetting();

		try 
        {
			$item = $this->repo->find($artist_name);
	
			$item->addView();

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'layout' => 'artist/artist'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    

    /**
     * Edit info page for frontend
     */
    public function edit_profile()
    {
		$customer = $this->app->customer_auth();

		$settings = $this->app->SystemSetting();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $customer,
                'layout' => isset($this->app->customer->customer_id) ? 'artist/profile' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }


	/**
     * Edit info page for frontend
     */
    public function edit_information()
    {
		$customer = $this->app->customer_auth();

		$settings = $this->app->SystemSetting();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $customer,
                'layout' => isset($this->app->customer->customer_id) ? 'artist/information' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

}
