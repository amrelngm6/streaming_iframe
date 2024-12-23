<?php

namespace Medians\Media\Application;
use Shared\dbaser\CustomController;
use getID3;

use Medians\Media\Infrastructure\MediaRepository;
use Medians\Media\Infrastructure\MediaItemRepository;
use Medians\Categories\Infrastructure\CategoryRepository;
use Medians\Customers\Infrastructure\CustomerRepository;


class ShortVideoController extends CustomController 
{

	protected $app;
	
	protected $repo;

	protected $mediaRepo;
	protected $categoryRepo;
	protected $customerRepo;

	function __construct()
	{
        $this->app = new \config\APP;
		$this->repo = new MediaItemRepository;
		$this->mediaRepo = new MediaRepository;
		$this->categoryRepo = new CategoryRepository;
		$this->customerRepo = new CustomerRepository;
	}


    
	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{
		return [
            [ 'value'=> "media_id", 'text'=> "#"],
            [ 'value'=> "name", 'text'=> translate('name'), 'sortable'=> true ],
            [ 'value'=> "picture", 'text'=> translate('picture'), 'sortable'=> true ],
            [ 'value'=> "artist.name", 'text'=> translate('Artist'),  ],
            [ 'value'=> "field.duration", 'text'=> translate('Duration'),  ],
            [ 'value'=> "info", 'text'=> translate('Info')  ],
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
	    return render('media', [
	        'load_vue' => true,
	        'title' => translate('Short videos'),
	        'items' => $this->repo->getByType('short_video'),
	        'columns' => $this->columns(),
			'object_name'=> 'MediaItem',
			'object_key'=> 'media_id',
			'no_create'=> true,
	    ]);
	}

    
    
    /**
     * Upload Audio page for frontend
     */
    public function upload_page()
    {
		$settings = $this->app->SystemSetting();

        $this->app->customer_auth();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'type' => 'short_video',
                'layout' => isset($this->app->customer->customer_id) ? 'shorts/upload' : 'signin',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }


    


    
    
    /**
     * Audio page for frontend
     */
    public function video_page($media_id)
    {
		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'shorts/page'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    

    /**
     * Studio media page for frontend
     */
    public function studio_media()
    {
		$settings = $this->app->SystemSetting();

        $customer = $this->app->customer_auth();
        $params = $this->app->params();
        
        $params['limit'] = $settings['view_short_videos_limit'] ?? null;
        $params['author_id'] = $customer->customer_id ?? 0;
        $params['type'] = 'short_video';
        $list = $this->repo->getWithFilter($params);

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'customer' => $customer,
                'list' => $list,
                'layout' => isset($this->app->customer->customer_id) ? 'shorts/studio' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    /**
     * Discover page for frontend
     */
    public function discover()
    {
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

        $params['limit'] = $settings['view_short_videos_limit'] ?? null;
        $params['type'] = 'short_video';
        $list = $this->repo->getWithFilter($params);

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'video_items' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'shorts/discover'
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
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

        $params['limit'] = $settings['view_short_videos_limit'] ?? null;
        $params['type'] = 'short_video';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'search/search',
                'sub_layout' => 'short',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    /**
     * Discover page for frontend
     */
    public function search_popup()
    {
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

        $params['limit'] = $settings['view_short_videos_limit'] ?? null;
        $params['type'] = 'short_video';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/pages/popup-list.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'popup-list',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    /**
     * Likes page for frontend
     */
    public function likes()
    {
		$settings = $this->app->SystemSetting();
        
		$this->app->customer_auth();

        $params = $this->app->params();

        $params['limit'] = $settings['view_short_videos_limit'] ?? null;
        $params['likes'] = true;
        $params['type'] = 'short_video';
        $params['customer_id'] = $this->app->customer->customer_id ?? 0;
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => isset($this->app->customer->customer_id) ? 'likes' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    /**
     * Genres page for frontend
     */
    public function genres()
    {
		$settings = $this->app->SystemSetting();
		$this->app->customer_auth();
        $params = $this->app->params();

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'shorts/genres'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

    
    /**
     * Single Genre page for frontend
     */
    public function genre($prefix)
    {
		$settings = $this->app->SystemSetting();
		$this->app->customer_auth();
        $params = $this->app->params();

		try 
        {
            $item = $this->categoryRepo->getGenreByPrefix($prefix);
            
            if (empty($item->category_id))
    			throw new \Exception(translate('Page not found'), 1);

            $params['limit'] = $settings['view_short_videos_limit'] ?? null;
            $params['genre'] = $item->category_id;
            $list = $this->repo->getWithFilter($params);
            
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'genre'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    








    /**
     * Edit info page for frontend
     */
    public function edit($media_id)
    {
		$this->app->customer_auth();

		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);

        if (empty($item->main_file->path))
            return Page404();


		try {

            $videoPath = $_SERVER['DOCUMENT_ROOT'].$item->main_file->path;
            $outputDir = $_SERVER['DOCUMENT_ROOT']. $this->mediaRepo->videos_dir. 'screenshots/';
            $VideoController = new VideoController();
            $list = $VideoController->generateScreenshots($videoPath, $outputDir, $settings);

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => isset($this->app->customer->customer_id) ? 'shorts/edit' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    


    /**
     * Edit / Cut Video at frontend
     */
    public function edit_video($media_id)
    {
		$this->app->customer_auth();

		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);

        if (empty($item->main_file->path))
            return Page404();

        if (isset($item->field['video_generated']))
        {
            return $this->edit($media_id);
        }


		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => isset($this->app->customer->customer_id) ? 'shorts/short-editor' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    

    /**
     * Submit Upload Media page
     */
	public function upload()
	{

		$this->app = new \config\APP;
        
        $params = $this->app->params();
		$settings = $this->app->SystemSetting();
        
        try {
                
			$customer = $this->app->customer_auth();
            if (!$customer->can_do('shortvideo'))
            {
                throw new \Exception(translate('You need to upgrade your subscription'), 1);
            }

            if (!empty($params['link']))
            {
                
                $params['name'] = '';
                $params['description'] = '';
                $tempFilePath = '/uploads/shorts/tmp/'.md5($params['link']).'.mp4';
                $tempFileFullPath = $_SERVER['DOCUMENT_ROOT'].$tempFilePath;
                
                if ($this->downloadRemoteFile($tempFileFullPath, $_POST['params']['link']) ) 
                {
                    $save = $this->store($params, $tempFilePath, $settings);
                }

            } else {
                    
                foreach ($this->app->request()->files as $key => $value) {
                    if ($value) {

                        $file = $this->mediaRepo->upload($value, 'video', true);
                        
                        $params['name'] = $value->getClientOriginalName();
                        $params['description'] = $value->getClientOriginalName();
                        
                        $save = $this->store($params, $this->mediaRepo->_dir.$file, $settings);
                    }
                }
            }
            
            return array('success'=>1, 'result'=>translate('Uploaded'), 'redirect'=>"/video/edit/$save->media_id");

        } catch (\Throwable $th) {
        	throw new \Exception("Error Processing Request ".$th->getMessage(), 1);
        }

	}



    public function store()
    {
        try {
                
            $this->app = new \config\APP;

            $params = $this->app->params();
		    
            $settings = $this->app->SystemSetting();
            
            $item = $this->repo->find($params['media_id']);

            $params['name'] = $item->name;
            $params['description'] = $item->description;
            $params['picture'] = $item->picture;
            $params['author_id'] = $item->author_id;
            $params['type'] = 'short_video';
            $params['files'] = [ ['type'=> 'short_video', 'storage'=> $settings['default_storage'] ?? 'local', 'path'=> $params['media_path']] ];
            
            if ($save = $this->repo->store($params))
            {
                return array('success'=>1, 'result'=>translate('Uploaded'), 'redirect'=>"/short/edit/$save->media_id");
            }


        } catch (\Throwable $th) {
        	throw new \Exception(translate('Select media first'), 1);

            // throw new \Exception("Error Processing Request ".$th->getMessage(), 1);
            
        }

	}



	public function update()
	{
		$this->app = new \config\APP;

        $params = $this->app->params();
        $settings = $this->app->SystemSetting();
		
        try {
            
            $item = $this->repo->find($params['media_id']);

            $params['files'] = [ ['type'=> 'short_video', 'storage'=> 'local', 'path'=> $params['media_path']] ];
            
            $this->repo->clearMediaFiles($item->media_id) ;

            if ( $this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>1);
            }

        } catch (\Exception $e) {
        	throw new \Exception("Error Processing Request " .$e->getMessage(), 1);
        }
	}

    

	public function update_video()
	{
		$this->app = new \config\APP;

        $params = $this->app->params();
        $settings = $this->app->SystemSetting();

        try {
            $item = $this->repo->find($params['media_id']);

            $cuttedFile = $this->cutVideo($_SERVER['DOCUMENT_ROOT'] .$item->main_file->path, $params['start'], $params['end'], $params['duration']);
            $videoFile = str_replace($_SERVER['DOCUMENT_ROOT'], '', $cuttedFile);
            $params['files'] = [ ['type'=> 'short_video', 'storage'=> 'local', 'path'=> $videoFile] ];
            $params['field'] = [ 'video_generated'=> '1', 'duration' =>  (strtotime($params['duration']) - strtotime('TODAY')) ];
            
            $params = $this->appendFileInfo($params, $cuttedFile);
            
            $clearMedia = $this->repo->clearMediaFiles($item->media_id);

            if ( $this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'no_reset'=>1, 'reload'=>1);
            }

        } catch (\Exception $e) {
        	throw new \Exception("Error Processing Request " .$e->getMessage(), 1);
        }
	}


	public function delete() 
	{
		$this->app = new \config\APP;

		$params = $this->app->params();
		
        try {

            if ($this->repo->delete($params['media_id']))
            {
                return array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1);
            }

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        }
	}


    
    
    function cutVideo($inputVideoPath, $from, $to, $duration = null) {

        $settings = $this->app->SystemSetting();
        $ffmpeg = $settings['ffmpeg_path'] ?? 'ffmpeg';

        $n = str_replace(':', '', $from.$to);
        $outputVideoPath = strpos($inputVideoPath, '/tmp') ? str_replace('/tmp/', '/shorts/'.$n, $inputVideoPath) : str_replace('/videos/', '/videos/shorts/'.$n, $inputVideoPath);

        $from = strlen($from) == 5 ? ('00:'.$from) : $from;
        $duration = '00:'.$duration;
        // $to = strlen($to) == 5 ? ('00:'.$to) : $to;

        // $command = "$ffmpeg -ss $from -i " . escapeshellarg($inputVideoPath) . " -to $to -c copy " . escapeshellarg($outputVideoPath) . " ";
        $command = "$ffmpeg -i " . escapeshellarg($inputVideoPath) . "  -ss $from -t $duration -c:v copy -c:a copy " . escapeshellarg($outputVideoPath) . " ";

        // FFmpeg command to re-encode the video
        if (file_exists($outputVideoPath))
            return $outputVideoPath;

        // $command = "$ffmpeg -ss 00:$from -to 00:$to  -i " . escapeshellarg($inputVideoPath) . " -c:v libx264 -preset fast -crf 22 -c:a aac -b:a 128k " . escapeshellarg($outputVideoPath) . " 2>&1";

        // Execute the command
        $run = shell_exec($command);
        

        // Check if the re-encoded file was created successfully
        return file_exists($outputVideoPath) && filesize($outputVideoPath) > 0 ? $outputVideoPath : null;
        // return file_exists($outputVideoPath) && filesize($outputVideoPath) > 0 ? (new VideoController)->reencodeVideo($outputVideoPath) : null;
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
