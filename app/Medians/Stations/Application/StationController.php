<?php

namespace Medians\Stations\Application;

use Medians\Stations\Infrastructure\StationRepository;

use Shared\dbaser\CustomController;
use getID3;

class StationController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;
	

	

	function __construct()
	{
		$this->repo = new StationRepository();
	}

	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{
		return [
            [ 'value'=> "station_id", 'text'=> "#"],
            [ 'value'=> "name", 'text'=> translate('name'), 'sortable'=> true ],
            [ 'value'=> "picture", 'text'=> translate('picture') ],
            [ 'value'=> "customer.name", 'text'=> translate('Customer'),  ],
            [ 'value'=> "edit", 'text'=> translate('edit')  ],
            [ 'value'=> "delete", 'text'=> translate('delete')  ],
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
	        'title' => translate('Stations'),
	        'items' => $this->repo->get(),
	        'columns' => $this->columns(),
			'object_name'=> 'Station',
			'object_key'=> 'station_id',
			'no_create'=> true,
	    ]);
	}


	public function store() 
	{	
		$this->app = new \config\APP;

		$params = $this->app->params();

        try 
		{
			$mediaRepo = new \Medians\Media\Infrastructure\MediaRepository;

			$customer = $this->app->customer_auth();
            if (!$customer->can_do('stations'))
            {
                throw new \Exception(translate('You need to upgrade your subscription'), 1);
            }

			$params['status'] = isset($params['status']) ? 'on' : null;

            foreach ($this->app->request()->files as $key => $value) {
                $file = $mediaRepo->upload($value);
        
                $getID3 = new getID3;
                // Analyze file
                $fileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT']. $mediaRepo->_dir.$file);

                $params['picture'] = $mediaRepo->_dir.$file;
            }

			$params['customer_id'] = $this->app->customer_id() ?? 0;

			try {

				$returnData = $this->repo->store($params);

				return $returnData
				? array('success'=>1, 'result'=>translate('Added'), 'redirect'=>'/stations/edit/'.$returnData->station_id)
				: array('success'=>0, 'result'=>'Error', 'error'=>1);
	
			} catch (\Throwable $th) {
				return array('error'=>$th->getMessage());
			}

        } catch (Exception $e) {
        	return array('error'=>$e->getMessage());
        }

		return $returnData;
	}


	public function add_item() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	

        	$this->app->customer_auth();
			
			$filePath = $_SERVER['DOCUMENT_ROOT']. $params['media_path'];
			$getID3 = new getID3;
			if (substr($params['media_path'], 0, 4) == 'http' ) {
				$tempFilePath = $_SERVER['DOCUMENT_ROOT'].'/uploads/audio/tmp/'.md5($params['media_path']).'.mp3';
				file_put_contents($tempFilePath, fopen($params['media_path'], 'r'));
				$filePath = $tempFilePath;
			}
			$fileInfo = $getID3->analyze($filePath);

            if (isset($fileInfo['playtime_seconds'])) {
                $params['duration'] = round($fileInfo['playtime_seconds'], 0);
			}

			$returnData = (!empty($this->repo->store_item($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
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

		$mediaRepo = new \Medians\Media\Infrastructure\MediaRepository;

		foreach ($this->app->request()->files as $key => $value) {
			if ($value) {
				$picture = $mediaRepo->upload($value);
				$params['picture'] = $mediaRepo->_dir.$picture;
			}
		}   
		
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


	public function update_item()
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {

            if ($this->repo->update_item($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>0);
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

			$item  = $this->repo->find($params['station_id']);

			// Handle customer Session
        	$this->app->customer_auth();

			// Validate item authorization
			$this->validateDelete($item);

            if ($this->repo->delete($params['station_id']))
            {
                return array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1);
            }
            

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}

	public function deleteItem() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();
		
        try {

            if ($this->repo->deleteItem($params['station_media_id']))
            {
                return array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1);
            }
            

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}

	public function validate($params) 
	{
		if (empty($params['name']))
		{
        	throw new \Exception(translate('NAME_EMPTY'), 1);
		}

		if (empty($this->app->customer->customer_id))
		{
        	throw new \Exception(translate('Login first'), 1);
		}
	}

    /**
     * Station JSON for frontend
     */
    public function station_json($station_id)
    {

		try {

			echo json_encode($this->repo->find($station_id));
			return;
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }


	/**
     * Upload Audio Book page for frontend
     */
    public function station_upload_page()
    {
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

        $this->app->customer_auth();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'type' => 'station',
                'layout' => isset($this->app->customer->customer_id) ? 'station/upload' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

    /**
     * Station page for frontend
     */
    public function station_edit($station_id)
    {
		$this->app = new \config\APP;
		
		$settings = $this->app->SystemSetting();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
				'item' => $this->repo->find($station_id),
                'layout' => 'station/edit'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

	
    /**
     * Studio page for frontend
     */
    public function studio()
    {

		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

        $customer = $this->app->customer_auth();
        
        $params = $this->app->params();
        
        // $this->checkSession($customer);

        $params['limit'] = $settings['view_stations_limit'] ?? null;
        $params['customer_id'] = $customer->customer_id ?? 0;
        $list = $this->repo->getWithFilter($params);

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'customer' => $customer,
                'list' => $list,
                'layout' => isset($this->app->customer->customer_id) ? 'station/studio' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

    /**
     * Station page for frontend
     */
    public function station($station_id)
    {
		$this->app = new \config\APP;
		
		$settings = $this->app->SystemSetting();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
				'item' => $this->repo->find($station_id),
                'layout' => 'station/page'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

    /**
     * Stations list page for frontend
     */
    public function stations()
    {
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();
        $params = $this->app->params();

        $params['limit'] = $settings['view_stations_limit'] ?? null;
        $list = $this->repo->getWithFilter($params);

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item'=> ['name'=> translate('Top Stations'),  'description'=> translate('Listen to audio stations')], 
				'items' => $list['items'],
				'list' => $list,
                'layout' => 'station/stations'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }


	
    
    /**
     * Discover page for frontend
     */
    public function search()
    {
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

        $params['limit'] = $settings['view_stations_limit'] ?? null;
        $list = $this->repo->getWithFilter($params);

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'search_list' => $list,
                'layout' => 'search/search',
                'sub_layout' => 'station',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

	
    /**
     * Discover page for frontend
     */
    public function calendar($id)
    {
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

		$station = $this->repo->find($id);

		$datetime = new \DateTime();
		$datetime_string = $datetime->format('c');
		
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'station' => $station,
				'now' => $datetime_string,
                'layout' => 'station/calendar',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
	
}
