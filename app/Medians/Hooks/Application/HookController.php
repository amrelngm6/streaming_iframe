<?php

namespace Medians\Hooks\Application;
use Shared\dbaser\CustomController;

use Medians\Hooks\Infrastructure\HookRepository;
use Medians\Plugins\Infrastructure\PluginRepository;


class HookController extends CustomController 
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $pluginRepo;

	

	function __construct()
	{

		$this->app = new \config\APP;
		$this->repo = new HookRepository;
		$this->pluginRepo = new PluginRepository;
	}


	
	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "id", 'text'=> "#"],
            [ 'value'=> "title", 'text'=> translate('title'), 'sortable'=> false ],
            [ 'value'=> "plugin.name", 'text'=> translate('Plugin'), 'sortable'=> false ],
            [ 'value'=> "position", 'text'=> translate('position'), 'sortable'=> true ],
            [ 'value'=> "short_code", 'text'=> translate('Shortcode'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('status'), 'sortable'=> false ],
			['value'=>'edit', 'text'=>translate('View')],
			['value'=>'delete', 'text'=>translate('Delete')],
        ];
	}

	


	/**
	 * Admin index items
	 * Loads in vue 
	 */ 
	public function index() 
	{
		return render('hooks', [
			'load_vue'=> true,
	        'title' => translate('Hooks list'),
	        'items' => $this->repo->get(),
	        'columns' => $this->columns(),
			'plugins' => $this->pluginRepo->get(),

	    ]);
	}




	/**
	 * Admin hook page
	 * 
	 */ 
	public function hook( $hook_id ) 
	{
		try {

			$item = $this->repo->find($hook_id);

			if ($item->field)
			{
				$item->options = $item->field;
			}				

			return render('', [
		        'load_vue' => true,
		        'title' => translate('Hook page'),
		        'fillable' => $item->hookPlugin()->fillable(),
		        'item' => $item,
		    ]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}

	
	/**
	 * Customers index page
	 * 
	 */ 
	public function view($attributes ) 
	{
		$item = $this->repo->find($attributes['id']);

		try {
			
			return $item->hookPlugin()->view($attributes);
			
		} catch (\Throwable $th) {
			return null;
		}
	}

	public function store() 
	{

		$this->app = new \config\APP;

		$params = $this->app->params();

        try {	

            $returnData = (!empty($this->repo->store($params))) 
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

        	$params['status'] = !empty($params['status']) ? $params['status'] : null;
            if ($this->repo->update($params))
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

            if ($this->repo->delete($params['id']))
            {
                return array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1);
            }
            

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}

}