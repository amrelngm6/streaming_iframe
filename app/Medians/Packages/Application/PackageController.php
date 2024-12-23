<?php

namespace Medians\Packages\Application;
use \Shared\dbaser\CustomController;

use Medians\Packages\Infrastructure\PackageRepository;

class PackageController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;



	function __construct()
	{
		$this->app = new \config\APP;

		$this->repo = new PackageRepository();
	}



	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{
		return [
            [ 'value'=> "package_id", 'text'=> "#"],
            [ 'value'=> "name", 'text'=> translate('Package name'), 'sortable'=> true ],
            [ 'value'=> "description", 'text'=> translate('Description'), 'sortable'=> true ],
            [ 'value'=> "payment_type", 'text'=> translate('Plan type'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('status'), 'sortable'=> true ],
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
            [ 'key'=> "audio_uploads_limit", 'title'=> translate('Audio uploads limit'), 'placeholder'=> translate('Max available Audio count for this package subscribers'), 'icon'=> 'music', 'required'=> true, 'column_type'=>'numver' ],
            [ 'key'=> "audiobooks_uploads_limit", 'title'=> translate('Audiobooks uploads limit'), 'placeholder'=> translate('Max available Audiobooks count for this package subscribers'), 'icon'=> 'book-open', 'required'=> true, 'column_type'=>'numver' ],
            [ 'key'=> "videos_uploads_limit", 'title'=> translate('Videos uploads limit'), 'placeholder'=> translate('Max available Videos count for this package subscribers'), 'icon'=> 'camera', 'required'=> true, 'column_type'=>'numver' ],
            [ 'key'=> "shortvideo_uploads_limit", 'title'=> translate('Short videos uploads limit'), 'placeholder'=> translate('Max available Short Videos count for this package subscribers'), 'icon'=> 'camera-off', 'required'=> true, 'column_type'=>'numver' ],
            [ 'key'=> "stations_uploads_limit", 'title'=> translate('Stations uploads limit'), 'placeholder'=> translate('Max available Stations count for this package subscribers'), 'icon'=> 'radio', 'required'=> true, 'column_type'=>'numver' ],
            [ 'key'=> "channels_uploads_limit", 'title'=> translate('Channels uploads limit'), 'placeholder'=> translate('Max available Channels count for this package subscribers'), 'icon'=> 'tv', 'required'=> true, 'column_type'=>'numver' ],
            [ 'key'=> "playlists_uploads_limit", 'title'=> translate('Playlists limit'), 'placeholder'=> translate('Max available Playlists count for this package subscribers'), 'icon'=> 'sliders', 'required'=> true, 'column_type'=>'numver' ],
        ];
	}


	/**
	 * Admin index items
	 * 
	 * @param Silex\Application $app
	 * @param \Twig\Environment $twig
	 * 
	 */ 
	public function index() 
	{
		return render('packages', [
	        'load_vue' => true,
	        'title' => translate('Packages'),
			'columns' => $this->columns(),
			'fillable' => $this->fillable(),
	        'items' => $this->repo->get(),
	    ]);
	}

	/**
	 * Store item to database
	 * 
	 * @return [] 
	*/
	public function store() 
	{	
		$this->app = new \config\APP;
        
		$params = $this->app->params();

        try 
		{
        	$user = $this->app->auth();

			$params['status'] = (isset($params['status']) && $params['status'] != 'false') ? 'on' : null;
			$params['created_by'] = $user->id;
            
			return ($this->repo->store($params))
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>translate('Error'), 'error'=>1);


        } catch (Exception $e) {
            return array('error'=>$e->getMessage());
        }
	
	}



	/**
	 * Update item to database
	 * 
	 * @return [] 
	*/
	public function update() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {

			$params['status'] = (isset($params['status']) && $params['status'] != 'false') ? 'on' : null;

           	$returnData =  ($this->repo->update($params))
           	? array('success'=>1, 'result'=>translate('Updated'), 'reload'=>0)
           	: array('error'=>'Not allowed');


        } catch (Exception $e) {
            $returnData = array('error'=>$e->getMessage());
        }

        return $returnData;

	}


	/**
	 * Delete item from database
	 * 
	 * @return [] 
	*/
	public function delete() 
	{
		
		$this->app = new \config\APP;

		$params = $this->app->params();

        try {

           	return  ($this->repo->delete($params['package_id']))
            ? array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1)
           	: array('error'=>translate('Not allowed'));


        } catch (Exception $e) {
            $returnData = array('error'=>$e->getMessage());
        }

        return $returnData;

	}

	
	/**
	 * Load taxi trip for Driver
	 */
	public function load_packages()
	{
		$user = $this->app->auth();

		if (empty($user)) { return null; }
		

		return $this->repo->get();

	}

	

	
    /**
     * Package subscription page for frontend
     */
    public function page($playlist_id)
    {
		$this->app = new \config\APP;
		
		$settings = $this->app->SystemSetting();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
				'item' => $this->repo->find($playlist_id),
                'layout' => 'packages/page'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }


}
