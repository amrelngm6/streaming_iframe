<?php

namespace Medians\Media\Application;
use Shared\dbaser\CustomController;
use getID3;

use Medians\Media\Infrastructure\MediaRepository;
use Medians\Media\Infrastructure\MediaItemRepository;
use Medians\Categories\Infrastructure\CategoryRepository;
use Medians\Customers\Infrastructure\CustomerRepository;
use Medians\Playlists\Infrastructure\PlaylistRepository;


class MediaItemController extends CustomController 
{

	protected $app;
	
	protected $repo;

	protected $mediaRepo;
	protected $categoryRepo;
	protected $customerRepo;
	protected $playlistRepo;

	function __construct()
	{
        $this->app = new \config\APP;
		$this->repo = new MediaItemRepository;
		$this->mediaRepo = new MediaRepository;
		$this->categoryRepo = new CategoryRepository;
		$this->customerRepo = new CustomerRepository;
		$this->playlistRepo = new PlaylistRepository;
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
	        'title' => translate('Audio items'),
	        'items' => $this->repo->getByType('audio'),
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
                'type' => 'audio',
                'layout' => isset($this->app->customer->customer_id) ? 'upload' : 'signin',
                'sub_layout' =>  'audio/upload',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }


    /**
     * Import Audio file from external link
     */
    public function import_page()
    {
		$settings = $this->app->SystemSetting();

        $this->app->customer_auth();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'type' => 'audio',
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
    public function audio_page($media_id)
    {
		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genres' => $this->categoryRepo->getGenres(),
                'layout' => 'audio/page'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    
    /**
     * Audio page for frontend
     */
    public function embed($media_id)
    {
		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/pages/audio/embed.html.twig', [
                'item' => $item,
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
		$settings = $this->app->SystemSetting();

        $customer = $this->app->customer_auth();
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'customer' => $customer,
                'layout' => isset($this->app->customer->customer_id) ? 'studio' : 'signin'
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
        
        // $this->checkSession($customer);

        $params['limit'] = $settings['view_audio_limit'] ?? null;
        $params['author_id'] = $customer->customer_id ?? 0;
        $params['type'] = 'audio';
        $list = $this->repo->getWithFilter($params);

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'customer' => $customer,
                'list' => $list,
                'layout' => isset($this->app->customer->customer_id) ? 'audio/studio' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    /**
     * Studio media page for frontend
     */
    public function studio_playlists()
    {
		$settings = $this->app->SystemSetting();

        $customer = $this->app->customer_auth();
        
        $this->checkSession($customer);

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
				'items' => $this->playlistRepo->getByCustomer($customer->customer_id),
                'layout' => isset($this->app->customer->customer_id) ? 'studio_playlists' : 'signin'
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

        $params['limit'] = $settings['view_audio_limit'] ?? null;
        $params['type'] = 'audio';
        $list = $this->repo->getWithFilter($params);

        
        $artistRepo = new \Medians\Customers\Infrastructure\CustomerRepository;
        $query['limit'] = $settings['view_audio_limit'] ?? null;
        $artists = $artistRepo->getWithFilter($query);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'artists' => $artists,
                'genres' => $this->categoryRepo->getGenres(),
                'layout' => 'discover'
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

        $params['limit'] = $settings['view_audio_limit'] ?? null;
        $params['type'] = 'audio';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getGenres(),
                'layout' => 'search/search',
                'sub_layout' => 'audio',
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

        $params['limit'] = $settings['view_audio_limit'] ?? null;
        $params['type'] = 'audio';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/pages/popup-list.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getGenres(),
                'layout' => 'popup-list',
                'sub_layout' => 'videos/popup-videos-list',
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

        $params['limit'] = $settings['view_audio_limit'] ?? null;
        $params['likes'] = true;
        $params['customer_id'] = $this->app->customer->customer_id ?? 0;
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getGenres(),
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
                'genres' => $this->categoryRepo->getGenres(),
                'audiobook_genres' => $this->categoryRepo->getBookGenres(),
                'video_genres' => $this->categoryRepo->getVideoGenres(),
                'layout' => 'genres'
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

            $params['limit'] = $settings['view_audio_limit'] ?? null;
            $params['genre'] = $item->category_id;
            $list = $this->repo->getWithFilter($params);
            
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'list' => $list,
                'genres' => $this->categoryRepo->getGenres(),
                'layout' => 'genre'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    








    /**
     * Edit info page for frontend
     */
    public function edit_media($media_id)
    {
		$this->app->customer_auth();

		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);

        if (empty($item->main_file->path))
            return Page404();


        $filePath = $item->main_file->path;
        $ext = explode('.', $filePath);
        if (!file_exists($_SERVER['DOCUMENT_ROOT'].str_replace('.'.end($ext), '.png', $filePath))) 
        {
            $generateWave = $this->generateWave( $filePath );
        }

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genre_type' => 'genres',
                'model_type' => 'MediaItem',
                'genres' => $this->categoryRepo->getGenres(),
                'layout' => isset($this->app->customer->customer_id) ? 'audio/edit' : 'signin'
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

        $params['limit'] = $settings['view_audio_limit'] ?? null;
        $params['type'] = 'audio';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/pages/popup-list.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getVideoGenres(),
                'sub_layout' => 'audio/popup-audio-list-checkbox',
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
            if (!$customer->can_do('audio'))
            {
                throw new \Exception(translate('You need to upgrade your subscription'), 1);
            }

            if (!empty($params['link']))
            {
                
                $headers = get_headers($params['link'], 1);

                // Check if Content-Length header is available
                if (isset($headers['Content-Length'])) {
                    if ($settings['audio_max_size'] < formatSizeUnits($headers['Content-Length'], 'number')) {
                        throw new \Exception(translate('Filez size is too large max size is'). formatSizeUnits($headers['Content-Length']), 1);
                    }
                }

                $params['name'] = '';
                $params['description'] = '';
                $tempFilePath = '/uploads/audio/tmp/'.md5($params['link']).'.mp3';
                file_put_contents($_SERVER['DOCUMENT_ROOT'].$tempFilePath, fopen($params['link'], 'r'));
                $save = $this->store($params, $tempFilePath, $settings);

            } else {
                    
                $k = 0;
                foreach ($this->app->request()->files as $key => $value) {
                    if ($value && $k < 1) {

                        $file = $this->mediaRepo->upload($value, 'audio', true);
                        
                        $params['name'] = $value->getClientOriginalName();
                        $params['description'] = $value->getClientOriginalName();
                        
                        $save = $this->store($params, $this->mediaRepo->_dir.$file, $settings);
                    }
                    $k += 1;
                }
            }
            
            return array('success'=>1, 'result'=>translate('Uploaded'), 'redirect'=>"/media/edit/$save->media_id");

        } catch (\Throwable $th) {
        	throw new \Exception("Error Processing Request ".$th->getMessage(), 1);
        }

	}



    public function store($params, $filePath, $settings)
    {
        try {
            

            $getID3 = new getID3;
            // Analyze file
            $fileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT']. $filePath);

            $params['files'] = [ ['type'=> 'audio', 'storage'=> $settings['default_storage'] ?? 'local', 'path'=> $filePath] ];
            $params['author_id'] = $this->app->customer_id() ?? 0;
            
            if (isset($fileInfo['playtime_seconds']))
            {
                $params['field'][ 'duration'] = round($fileInfo['playtime_seconds'] ?? 0, 0);
                $params['field'][ 'filesize'] = round($fileInfo['filesize'] ?? 0, 0);
                $params['field'][ 'bitrate'] = round($fileInfo['bitrate'] ?? 0, 0);
                $params['field'][ 'bpm'] = $fileInfo['id3v2']['comments']['bpm'][0] ?? 0;
            }

            if (isset($fileInfo['tags']['id3v2']))
            {
                $params['name'] = $fileInfo['tags']['id3v2']['title'][0] ?? 'Unknown Title';
                $params['description'] = $fileInfo['tags']['id3v2']['comment'][0] ?? 'No Description';
            }

            if (!empty($fileInfo['id3v2']['APIC'])) {
                $imageData = $fileInfo['id3v2']['APIC'][0]['data']; // Album art data
                // Save the image to a file
                $params['picture'] = $this->mediaRepo->images_dir.str_replace(['.mp3','.wav'], '.png', (str_replace(['/uploads/audio','/tmp'], '', $filePath)));
                $outputImagePath = $_SERVER['DOCUMENT_ROOT'].$params['picture'];
                file_put_contents($outputImagePath, $imageData);
            }


            $save = $this->repo->store($params);

            // $generateWave = $this->generateWave($filePath);

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



    public function generateWave($file)
    {

		$settings = $this->app->SystemSetting();

        $ffmpeg = $settings['ffmpeg_path'];
        // $ffmpeg = 'ffmpeg';
        $filePath = $_SERVER['DOCUMENT_ROOT']. $file;
        $outputPath = $_SERVER['DOCUMENT_ROOT']. str_replace(['mp3','wav','ogg'], 'png', $file);

        $shell = file_exists($outputPath) ? $outputPath : shell_exec($ffmpeg.' -i '.$filePath.' -filter_complex "showwavespic=s=1024x200:colors=yellow|blue|green" -frames:v 1  '.$outputPath.' ');
        return $shell;
    }


    

	public function update()
	{
		

        try {
            
            $request = $this->app->request();
            $params = $request->get('params');

            foreach ($request->files as $value) {
                if ($value){
                    $file = $this->mediaRepo->upload($value, 'picture', true);
                    $params['picture']  = $this->mediaRepo->_dir.$file;
                }
            }     
            
            $item = $this->repo->find($params['media_id']);
            $params['name'] = sanitizeInput($params['name'], true);
            
            if (empty($item->field->duration))
            {
                $getID3 = new getID3;
    
                // Analyze file
                $fileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT']. $item->main_file->path);
    
                if (isset($fileInfo['playtime_seconds'])) {
                    $params['field'] = [ 'duration'=> round($fileInfo['playtime_seconds'], 0) ];
                }
            }
        
            $params['selected_genres'] = $request->get('selected_genres');
        
            if ($this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>0, 'no_reset'=> 1);
            }
  
        } catch (\Exception $e) {
        	throw new \Exception("Error 1:  " .$e->getMessage(), 1);
        }
	}


	public function delete() 
	{
		$this->app = new \config\APP;

		$params = $this->app->params();
		
        try {

            $user = $this->app->auth();

            $item = $this->repo->find($params['media_id']);
            
            if (!$user->id)
            {
                return array('error'=>1, 'result'=>translate('Not allowed'), 'reload'=>1);
            }

            if ($this->repo->delete($params['media_id']))
            {
                return array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1);
            }

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        }
	}


	public function deleteByAuthor() 
	{
		$this->app = new \config\APP;

		$params = $this->app->params();
		
        try {

            $customer = $this->app->customer_auth();

            $item = $this->repo->find($params['media_id']);
            
            if ($item->author_id != $customer->customer_id)
            {
                return array('error'=>1, 'result'=>translate('Not allowed'), 'reload'=>1);
            }

            if ($item->author_id == $customer->customer_id && $this->repo->delete($params['media_id']))
            {
                return array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1);
            }

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        }
	}


    /**
     * Check if customer session is valid
     */
    public function checkSession($customer)
    {
        if (empty($customer->customer_id))
            $this->app->redirect('/customer/login');
    }

    

}
