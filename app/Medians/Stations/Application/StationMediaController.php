<?php

namespace Medians\Stations\Application;

use Medians\Stations\Infrastructure\StationRepository;
use Medians\Media\Infrastructure\MediaRepository;

use Shared\dbaser\CustomController;
use getID3;

class StationMediaController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $mediaRepo;

	protected $app;
	

	

	function __construct()
	{
		$this->repo = new StationRepository();
		$this->mediaRepo = new MediaRepository();
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
            [ 'value'=> "items_count", 'text'=> translate('Items'),  ],
            [ 'value'=> "customer.name", 'text'=> translate('Customer'),  ],
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
            [ 'key'=> "station_id", 'title'=> "#", 'column_type'=>'hidden'],
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
	        'title' => translate('Stations'),
	        'items' => $this->repo->get(100),
	        'columns' => $this->columns(),
	        'fillable' => $this->fillable(),
			'object_name'=> 'Station',
			'object_key'=> 'station_id',
	    ]);
	}



	public function store() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	

        	$this->app->customer_auth();
            
			$filePath = $_SERVER['DOCUMENT_ROOT']. $params['media_path'];
			$getID3 = new getID3;
			if (substr($params['media_path'], 0, 4) == 'http' ) {
				
                $media_path = '/uploads/audio/tmp/'.md5($params['media_path']).'.mp3';
				$tempFilePath = $_SERVER['DOCUMENT_ROOT'].$media_path;
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



	public function store_record() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	

        	$this->app->customer_auth();
            
			$params['media_path'] = '/uploads/audio/record-'.uniqid().'.wav';
			$filePath = $_SERVER['DOCUMENT_ROOT']. $params['media_path'];

			if (isset($_FILES['audio']['tmp_name'])) {
				
				$move = move_uploaded_file($_FILES['audio']['tmp_name'], $filePath);

				if ($move) {
					
					$output = str_replace( '.wav', '.mp3', $filePath);
					$convert = $this->mediaRepo->convertAudioWithFfmpeg($filePath, $output);

					$params['media_path'] = str_replace('.wav', '.mp3', $params['media_path']);

					$returnData = (!empty($this->repo->store_item($params))) 
					? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
					: array('success'=>0, 'result'=>'Error', 'error'=>1);
				}
			}


        } catch (\Exception $e) {
        	return array('result'=>$e->getMessage(), 'error'=>1);
        }

		return $returnData;
	}

	
	/**
	 * Set bulk videos at specified times 
	 */
	public function bulk_store() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	

        	$this->app->customer_auth();
			$params = $this->app->params();
			foreach ($params['selected']['media_id'] as $key => $value) 
			{
				$newParams = [];
				$newParams['station_id'] = $params['station_id'];
				$newParams['date'] = $params['date'];
				$newParams['media_id'] = $params['selected']['media_id'][$key];
				$newParams['media_path'] = $params['selected']['media_path'][$key];
				$newParams['duration'] = $params['selected']['duration'][$key] ?? 0;
				$newParams['bitrate'] = $params['selected']['bitrate'][$key] ?? 0;
				$newParams['filesize'] = $params['selected']['filesize'][$key] ?? 0;
				$newParams['start_at'] = $params['selected']['start_at'][$key];
				$newParams['title'] = $params['selected']['title'][$key];

				$filePath = $_SERVER['DOCUMENT_ROOT']. $newParams['media_path'];
				$newParams = $this->appendFileInfo($newParams, $filePath); 

				$save = $this->repo->store_item($newParams);
			}

			$returnData = ($save) 
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

            if ($this->repo->deleteItem($params['station_media_id']))
            {
                return array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1);
            }
            

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}

    /**
     * Calendar edit item popup for frontend
     */
    public function media_popup()
    {
		$this->app = new \config\APP;
		
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/popups/station-edit-track.html.twig', [
				'item' => $this->repo->findItem($params['station_media_id'])
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
	
    

    /**
     * Station JSON for frontend
     */
    public function json_media()
    {
		$this->app = new \config\APP;
		
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

        $station = $this->repo->find($params['station_id']);

		try {

            return render('', 
				$station->items,
            );
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

	/** 
	 * Analyze file and get info
	 */
	public function appendFileInfo($params, $filePath)
	{
		$getID3 = new getID3;

		$fileInfo = $getID3->analyze($filePath);

		if (isset($fileInfo['playtime_seconds'])) {
			$params['duration'] = round($fileInfo['playtime_seconds'], 0);
			$params['bitrate'] = $fileInfo['bitrate'];
			$params['filesize'] = $fileInfo['filesize'];
		}

		return $params;
	}


	
}
