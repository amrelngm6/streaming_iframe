<?php

namespace Medians\Media\Application;
use Shared\dbaser\CustomController;
use getID3;

use Medians\Media\Infrastructure\MediaRepository;
use Medians\Media\Infrastructure\MediaItemRepository;
use Medians\Categories\Infrastructure\CategoryRepository;
use Medians\Customers\Infrastructure\CustomerRepository;


class VideoController extends CustomController 
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
	        'title' => translate('Videos'),
	        'items' => $this->repo->getByType('video'),
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
                'type' => 'video',
                'layout' => isset($this->app->customer->customer_id) ? 'upload' : 'signin',
                'sub_layout' =>  'videos/upload',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }


    


    /**
     * Import Video file from external link
     */
    public function import_page()
    {

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'type' => 'video',
                'layout' => isset($this->app->customer->customer_id) ? 'import' : 'signin',
                'sub_layout' => 'videos/import',
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
    
            $item->addView();

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'videos/page'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    
    /**
     * Embed Video page for Iframe usage
     */
    public function embed($media_id)
    {
		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/pages/videos/embed.html.twig', [
                'item' => $item,
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
        
        $params['limit'] = $settings['view_videos_limit'] ?? null;
        $params['author_id'] = $customer->customer_id ?? 0;
        $params['type'] = 'video';
        $list = $this->repo->getWithFilter($params);

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'customer' => $customer,
                'list' => $list,
                'layout' => isset($this->app->customer->customer_id) ? 'videos/studio' : 'signin'
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

        $params['limit'] = $settings['view_videos_limit'] ?? null;
        $params['type'] = 'video';
        $list = $this->repo->getWithFilter($params);

        
        $artistRepo = new \Medians\Customers\Infrastructure\CustomerRepository;
        $query['limit'] = $settings['view_videos_limit'] ?? null;
        $artists = $artistRepo->getWithFilter($query);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item'=> ['name'=> translate('Top Videos'),  'description'=> translate('Watch our top videos')], 
                'video_items' => $list,
                'artists' => $artists,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'videos/discover'
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

        $params['limit'] = $settings['view_videos_limit'] ?? null;
        $params['type'] = 'video';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'search/search',
                'sub_layout' => 'video',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    /**
     * Search popoup list for frontend
     */
    public function search_popup()
    {
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

        $params['limit'] = $settings['view_videos_limit'] ?? null;
        $params['type'] = 'video';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/pages/popup-list.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'popup-list',
                'sub_layout' => 'videos/popup-videos-list',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    
    /**
     * Search popoup list for Calendar
     */
    public function search_popup_checkbox()
    {
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

        $params['limit'] = $settings['view_videos_limit'] ?? null;
        $params['type'] = 'video';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/pages/popup-list.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'videos/popup-list',
                'sub_layout' => 'videos/popup-videos-list-checkbox',
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

        $params['limit'] = $settings['view_videos_limit'] ?? null;
        $params['likes'] = true;
        $params['type'] = 'video';
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
                'layout' => 'videos/genres'
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
            $item = $this->categoryRepo->getVideoGenreByPrefix($prefix);
            
            if (empty($item->category_id))
    			throw new \Exception(translate('Page not found'), 1);

            $params['limit'] = $settings['view_videos_limit'] ?? null;
            $params['genre'] = $item->category_id;
            $params['type'] = 'video';
            $list = $this->repo->getWithFilter($params);
            
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'videos/genre'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    








    /**
     * Edit info page for frontend
     */
    public function edit_video($media_id)
    {
		$this->app->customer_auth();

		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);

        if (empty($item->main_file->path))
            return Page404();


        if (empty($item->field->duration))
        {
            $item = $this->handleFileDuration($item);
        }

        $videoPath = $_SERVER['DOCUMENT_ROOT'].$item->main_file->path;
        $outputDir = $_SERVER['DOCUMENT_ROOT']. $this->mediaRepo->videos_dir. 'screenshots/';
        $list = $this->generateScreenshots($videoPath, $outputDir, $settings);


		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genre_type' => 'genres',
                'model_type' => 'MediaItem',
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => isset($this->app->customer->customer_id) ? 'videos/edit' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    public function load_screenshots()
    {
		$this->app->customer_auth();

		$settings = $this->app->SystemSetting();

		$params = $this->app->params();

        $item = $this->repo->find($params['media_id']);

        if (empty($item->main_file->path))
            return Page404();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/includes/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'layout' => isset($this->app->customer->customer_id) ? 'videos/edit' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

    /**
     * Handle file duration 
     * Convert file into MP4
     * 
     */
    public function handleFileDuration($item)
    {
        
        $videoPath = $_SERVER['DOCUMENT_ROOT'].$item->main_file->path;
        
        $ext = explode('.', $videoPath);
        $newFile = str_replace(end($ext), 'mp4', $videoPath);
        $newPath = $this->mediaRepo->convertMediaWithFfmpeg($videoPath, $newFile);

        if ($newPath)
        {
            $item->main_file->path = str_replace( $_SERVER['DOCUMENT_ROOT'], '',  $newFile);
            $update = $item->main_file->save();
            
            $params = $this->handleFileInfo(['media_id' => $item->media_id], $item->main_file->path);
            $this->repo->update($params);
            // unlink($videoPath);
        }

        return $item;
    }


    /**
     * Download & Validate from URL
     */
    public function downloadRemoteFile($tempFileFullPath, $link)
    {
        
        if (file_exists($tempFileFullPath))
        {
            return $tempFileFullPath;
        }

        $videoUrl = $link;

        // Initialize a cURL session to fetch the video stream
        $ch = curl_init($videoUrl);

        // Tell cURL to return the transfer as a string instead of outputting it directly
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects

        // Set headers to match a browser request
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36',
            'Referer: https://www.facebook.com/',
        ]);

        // Execute the cURL session
        $response = curl_exec($ch);

        $save = file_put_contents($tempFileFullPath, $response);
        
        $filesize = filesize($tempFileFullPath);
        $filesize < 100 ? unlink($tempFileFullPath)   : null;
        return $filesize < 100 ? throw new \Exception("File size is ".$filesize, 1) : true;
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
            if (!$customer->can_do('videos'))
            {
                throw new \Exception(translate('You need to upgrade your subscription'), 1);
            }
            
            if (!empty($params['link']))
            {
                
                $params['name'] = '';
                $params['description'] = '';
                $tempFilePath = '/uploads/videos/tmp/'.md5($params['link']).'.mp4';
                $tempFileFullPath = $_SERVER['DOCUMENT_ROOT'].$tempFilePath;
                
                if ($this->downloadRemoteFile($tempFileFullPath, $_POST['params']['link']) ) 
                {
                    $save = $this->store($params, $tempFilePath, $settings);
                }

            } else {
                
                $k = 0;
                foreach ($this->app->request()->files as $key => $value) {
                    if ($value && $k < 1) {

                        $file = $this->mediaRepo->upload($value, 'video', true);
                        
                        $params['name'] = $value->getClientOriginalName();
                        $params['description'] = $value->getClientOriginalName();
                        
                        $save = $this->store($params, $this->mediaRepo->_dir.$file, $settings);
                    }
                    $k += 1;
                }
            }
            
            return array('success'=>1, 'result'=>translate('Uploaded'), 'redirect'=>"/video/edit/$save->media_id");

        } catch (\Throwable $th) {
        	throw new \Exception("Error Processing Request ".$th->getMessage(), 1);
        }

	}



    public function handleFileInfo($params, $filePath)
    {

        $getID3 = new getID3;
        // Analyze file
        $fileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT']. $filePath);
        
        $params['field'] = [ 'duration'=> round($fileInfo['playtime_seconds'], 0) ];
        $params['field']['bitrate'] = $fileInfo['bitrate'] ?? 0;
        $params['field']['filesize'] = $fileInfo['filesize'] ?? 0;
        $params['field']['bpm'] = $fileInfo['id3v2']['comments']['bpm'][0] ?? 0;

        if (isset($fileInfo['tags']['id3v2']))
        {
            $params['name'] = $fileInfo['tags']['id3v2']['title'][0] ?? ($params['name'] ?? 'Unknown Title');
            $params['description'] = $fileInfo['tags']['id3v2']['comment'][0] ?? ($params['name'] ?? 'No Description');
        }
        
        return $params;
	}



    public function store($params, $filePath, $settings)
    {
        try {
            
            $params = $this->handleFileInfo($params, $filePath);

            $params['files'] = [ ['type'=> 'video', 'title' => $params['name'] ?? '', 'storage'=> $settings['default_storage'] ?? 'local', 'path'=> $filePath] ];
            $params['author_id'] = $this->app->customer_id() ?? 0;
            
            $save = $this->repo->store($params);

            if ($settings['default_storage'] == 'google')
            {
                $service = new GoogleStorageService();
                $upload = $service->uploadFileToGCS($filePath);
            }

            return $save;

        } catch (\Throwable $th) {
            throw new \Exception("Error Processing Request ".$th->getMessage(), 1);
            
        }

	}



	public function update()
	{
		$this->app = new \config\APP;

        $params = $this->app->params();
        $item = $this->repo->find($params['media_id']);
		
        try {
            
            $params['selected_genres'] = $this->app->request()->get('selected_genres');

            if ($this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>0, 'no_reset'=>1);
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


    function generateScreenshots($videoPath, $outputDir, $settings, $screenshotCount = 10 ) {

        $path_arr = explode('/', $videoPath);
        $fileName = str_replace(['.mp4', '.ogg', '.wmv'], '.jpg', end($path_arr));
        $duration = $this->getVideoDuration($videoPath, $settings);
        $ffmpeg = $settings['ffmpeg_path'] ?? 'ffmpeg';
        
        if ($duration <= 0) {
            $duration = $this->getVideoDuration($this->reencodeVideo($videoPath) ?? $videoPath, $settings);
        }
    
        if ($duration <= 0) {
            die("Unable to get video duration.");
        }
    
        // Create output directory if it doesn't exist
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
    
        // Calculate interval between each screenshot (every 10% of the duration)
        $interval = $duration / $screenshotCount;
    
        for ($i = 1; $i <= $screenshotCount; $i++) {
            $time = $i * $interval;
    
            // Format time into hh:mm:ss for ffmpeg
            $formattedTime = gmdate("H:i:s", intval($time));
    
            $outputFile = $outputDir . "screenshot_" . $i . "_" . $fileName;
            
            // Command to capture screenshot at the specific time
            $command = "$ffmpeg -ss $formattedTime -i " . escapeshellarg($videoPath) . " -vframes 1 -q:v 2 " . escapeshellarg($outputFile) . " 2>&1";
    
            // Execute the command
            file_exists($outputFile) ? null : shell_exec($command);
            if (file_exists($outputFile))
            {
                $items[$i] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $outputFile);
            }
        }

        return $items;
    }

    function getVideoDuration($videoPath, $settings) {
        
        $ffmpeg = $settings['ffmpeg_path'] ?? 'ffmpeg';

        $command = "$ffmpeg -i " . escapeshellarg($videoPath) . " 2>&1";
        $output = shell_exec($command);
    
        preg_match('/Duration: (\d+):(\d+):(\d+\.\d+)/', $output, $matches);
        
        if (!empty($matches)) {
            $hours = $matches[1];
            $minutes = $matches[2];
            $seconds = $matches[3];
            
            return ($hours * 3600) + ($minutes * 60) + $seconds;
        }
    
        return 0; 
    }
    

    function reencodeVideo($inputVideoPath) {

        $settings = $this->app->SystemSetting();
        $ffmpeg = $settings['ffmpeg_path'] ?? 'ffmpeg';

        // FFmpeg command to re-encode the video
        $outputVideoPath = str_replace('/tmp', 'encoded_', str_replace('/shorts/', '/shorts/encoded_', $inputVideoPath));
        if (file_exists($outputVideoPath))
            return $outputVideoPath;

        $command = "$ffmpeg -i " . escapeshellarg($inputVideoPath) . " -c:v libx264 -preset fast -crf 22 -c:a aac -b:a 128k " . escapeshellarg($outputVideoPath) . " 2>&1";

        // Execute the command
        $run = shell_exec($command);
        
        // Check if the re-encoded file was created successfully
        return file_exists($outputVideoPath) && filesize($outputVideoPath) > 0 ? $outputVideoPath : null;
    }

}
