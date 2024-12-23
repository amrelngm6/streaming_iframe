<?php

namespace Medians\Media\Application;
use Shared\dbaser\CustomController;
use getID3;

use Medians\Media\Infrastructure\MediaRepository;
use Medians\Media\Infrastructure\MediaItemRepository;
use Medians\Categories\Infrastructure\CategoryRepository;
use Medians\Customers\Infrastructure\CustomerRepository;
use Medians\Playlists\Infrastructure\PlaylistRepository;


class AudiobookController extends CustomController 
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
	        'title' => translate('Audiobooks'),
	        'items' => $this->repo->getByType('audiobook'),
	        'columns' => $this->columns(),
			'object_name'=> 'MediaItem',
			'object_key'=> 'media_id',
			'no_create'=> true,
	    ]);
	}


    /**
     * Studio media page for frontend
     */
    public function studio_audiobooks()
    {
		$settings = $this->app->SystemSetting();

        $customer = $this->app->customer_auth();
		$params = $this->app->params();
        
        $params['limit'] = $settings['view_audiobooks_limit'] ?? null;
        $params['author_id'] = $customer->customer_id ?? 0;
        $params['type'] = 'audiobook';
        $list = $this->repo->getWithFilter($params);

		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'customer' => $customer,
                'list' => $list,
                'layout' => isset($this->app->customer->customer_id) ? 'audiobook/studio' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    
    
    
    /**
     * Upload Audio Book page for frontend
     */
    public function audiobook_upload_page()
    {
		$settings = $this->app->SystemSetting();

        $this->app->customer_auth();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'type' => 'audiobook',
                'genres' => $this->categoryRepo->getBookGenres(),
                'layout' => isset($this->app->customer->customer_id) ? 'upload' : 'signin',
                'sub_layout' =>  'audiobook/upload',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

    
    /**
     * Import Audiobook file from external link
     */
    public function import_page()
    {
		$settings = $this->app->SystemSetting();

        $this->app->customer_auth();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'type' => 'audiobook',
                'layout' => isset($this->app->customer->customer_id) ? 'import' : 'signin',
                'sub_layout' => 'videos/import',
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

        $params['limit'] = $settings['view_audiobooks_limit'] ?? null;
        $params['type'] = 'audiobook';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getBookGenres(),
                'layout' => 'audiobook/discover'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    



    /**
     * Audio page for frontend
     */
    public function book_page($media_id)
    {
		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genres' => $this->categoryRepo->getBookGenres(),
                'layout' => '/audiobook/page'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }



    /**
     * Edit info page for frontend
     */
    public function edit_audiobook($media_id)
    {
		$this->app->customer_auth();

		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genre_type' => 'book_genres',
                'model_type' => 'audiobook',
                'genres' => $this->categoryRepo->getBookGenres(),
                'layout' => isset($this->app->customer->customer_id) ? 'audio/edit' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

    /**
     * Edit Book Chapter page for frontend
     */
    public function edit_chapters($media_id)
    {
		$this->app->customer_auth();

		$settings = $this->app->SystemSetting();

        $item = $this->repo->find($media_id);

        $mediaController = new MediaItemController();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'genre_type' => 'book_genres',
                'model_type' => 'Audiobook',
                'genres' => $this->categoryRepo->getBookGenres(),
                'layout' => isset($this->app->customer->customer_id) ? 'audiobook/chapters' : 'signin'
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
            $item = $this->categoryRepo->getBookGenreByPrefix($prefix);

            if (empty($item->category_id))
    			throw new \Exception(translate('Page not found'), 1);

            $params['limit'] = $settings['view_audiobooks_limit'] ?? null;
            $params['genre'] = $item->category_id;
            $list = $this->repo->getWithFilter($params);
            
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $item,
                'list' => $list,
                'genres' => $this->categoryRepo->getBookGenres(),
                'layout' => 'audiobook/genre'
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
        
        try 
        {
            $item = $this->repo->find($params['media_id']);
            
            if (!empty($params['link']))
            {
                
                $filePath = '/uploads/audio/tmp/'.md5($params['link']).'.mp3';
                file_put_contents($_SERVER['DOCUMENT_ROOT'].$filePath, fopen($params['link'], 'r'));
                $title = null;

            } else {

                    

                foreach ($this->app->request()->files as $key => $value) {
                
                    $file = $this->mediaRepo->upload($value, 'audio', true);
                    
                    $filePath = $this->mediaRepo->_dir.$file;
                    
                    $title = $value->getClientOriginalName();
                }
            }

            $update = $this->store_chapter($filePath, $item, $title);


            return array('success'=>1, 'result'=>translate('Uploaded'), 'reload'=>1);

        } catch (\Throwable $th) {
            throw new \Exception("Error Processing Request ".$th->getMessage(), 1);
        }
	}







    
	public function store_chapter($filePath, $item, $title)
    {	
        
		$settings = $this->app->SystemSetting();
        
        $getID3 = new getID3;
        // Analyze file

        $file = ['type'=> 'audio', 'title' => sanitizeInput($title) ?? 'Chapter', 'storage'=> $settings['default_storage'] ?? 'local', 'path'=> $filePath] ;
        $params['author_id'] = $this->app->customer_id() ?? 0;
        
        $fileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT']. $filePath);

        if (isset($fileInfo['playtime_seconds']))
        {
            $params['field'][ 'duration'] = round($fileInfo['playtime_seconds'] ?? 0, 0);
            $params['field'][ 'filesize'] = round($fileInfo['filesize'] ?? 0, 0);
            $params['field'][ 'bitrate'] = round($fileInfo['bitrate'] ?? 0, 0);
            $params['field'][ 'bpm'] = $fileInfo['id3v2']['comments']['bpm'][0] ?? 0;
        }

        if (isset($fileInfo['tags']['id3v2']))
        {
            $file['title'] = $fileInfo['tags']['id3v2']['title'][0] ?? 'Unknown Title';
        }

        $mediaController = new MediaItemController();
        $mediaController->generateWave($filePath);

        if ($settings['default_storage'] == 'google')
        {
            $service = new GoogleStorageService();
            $upload = $service->uploadFileToGCS($_SERVER['DOCUMENT_ROOT'].$filePath, $filePath);
        }   

        return $this->repo->storeFile($file, $item);
    }
    
	public function store() 
	{	

		$params = $this->app->params();

        try {	
			
			$customer = $this->app->customer_auth();

            if (!$customer->can_do('audiobooks'))
            {
                throw new \Exception(translate('You need to upgrade your subscription'), 1);
            }


            foreach ($this->app->request()->files as $key => $value) {
                $file = $this->mediaRepo->upload($value);
        
                $getID3 = new getID3;
                // Analyze file
                $fileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT']. $this->mediaRepo->_dir.$file);

                $params['picture'] = $this->mediaRepo->_dir.$file;
            }

            $params['author_id'] = $this->app->customer_id() ?? 0;
			$params['status'] = isset($params['status']) ? 'on' : null;

			try {

				$returnData = $this->repo->store($params);

				return $returnData
				? array('success'=>1, 'result'=>translate('Added'), 'redirect'=>'/audiobook/edit/'.$returnData->media_id)
				: array('success'=>0, 'result'=>'Error', 'error'=>1);
	
			} catch (\Throwable $th) {
				return array('error'=>$th->getMessage());
			}

        } catch (Exception $e) {
        	return array('error'=>$e->getMessage());
        }

		return $returnData;
	}



    

	public function update()
	{
		$this->app = new \config\APP;

		$params = $this->app->params();
		
        try {


            foreach ($this->app->request()->files as $key => $value) {
                if ($value) {
                    $picture = $this->mediaRepo->upload($value);
                    $params['picture'] = $this->mediaRepo->_dir.$picture;
                }
            }
            
            
            $item = $this->repo->find($params['media_id']);
            $params['name'] = sanitizeInput($params['name']);
            $getID3 = new getID3;
            // Analyze file
            $fileInfo = $getID3->analyze($_SERVER['DOCUMENT_ROOT']. $item->main_file->path);

            if (isset($fileInfo['playtime_seconds']))
                $params['field'] = [ 'duration'=> round($fileInfo['playtime_seconds'], 0) ];
            
            // $params['author_id'] = $this->app->customer_auth()->customer_id ?? 0;
                
            $params['selected_genres'] = $this->app->request()->get('selected_genres');

            if ($this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>0);
            }

        } catch (\Exception $e) {
        	throw new \Exception("Error Processing Request " .$e->getMessage(), 1);
        }
	}


	public function update_chapters()
	{
		$this->app = new \config\APP;

		$params = $this->app->params();
		
        try {
            
            $item = $this->repo->find($params['media_id']);

            if ($this->repo->storeChapters($params['chapters'], $item))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>1);
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

            if ($this->repo->delete($params['category_id']))
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

    
    
    /**
     * Search page for frontend
     */
    public function search()
    {
		$settings = $this->app->SystemSetting();

        $params = $this->app->params();

        $params['limit'] = $settings['view_audiobooks_limit'] ?? null;
        $params['type'] = 'audiobook';
        $list = $this->repo->getWithFilter($params);
        
		try 
        {
            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'list' => $list,
                'genres' => $this->categoryRepo->getBookGenres(),
                'layout' => 'search/search',
                'sub_layout' => 'audiobook',
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }
    

}
