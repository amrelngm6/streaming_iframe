<?php
namespace Medians\Locations\Application;

use Shared\dbaser\CustomController;

use Medians\Locations\Infrastructure\CityRepository;
use Medians\Locations\Infrastructure\StateRepository;
use Medians\Locations\Infrastructure\CountryRepository;

class CityController extends CustomController 
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $countriesRepo;

	protected $stateRepo;

	function __construct()
	{

		$this->app = new \config\APP;

		$this->repo = new CityRepository();
		
		$this->countriesRepo = new CountryRepository();

		$this->stateRepo = new StateRepository();

	}




	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "city_id", 'text'=> "#", 'sortable'=> true ],
            [ 'value'=> "name", 'text'=> translate('name'), 'sortable'=> true ],
			[ 'value'=> "state.name", 'text'=> translate('State'), 'sortable'=> true ],
            [ 'value'=> "state.country.name", 'text'=> translate('country'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('status'), 'sortable'=> true ],
            [ 'value'=> "edit", 'text'=> translate('Edit') ],
            [ 'value'=> "delete", 'text'=> translate('delete') ],
        ];
	}

	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
            [ 'key'=> "city_id", 'title'=> "#", 'column_type'=>'hidden'],
			[ 'key'=> "state_id", 'title'=> translate('State'), 
				'fillable'=> true, 'column_type'=>'select','text_key'=>'name', 'required'=>true, 'withLabel'=>true, 
				'data' => $this->stateRepo->get()
			],
            [ 'key'=> "name", 'title'=> translate('name'), 'sortable'=> true, 'fillable'=> true, 'column_type'=>'text' ],
            [ 'key'=> "status", 'title'=> translate('status'), 'sortable'=> true, 'fillable'=>true, 'column_type'=>'checkbox'],
        ];
	}

	

	/**
	 * Admin index items
	 * 
	 * @param Silex\Application $app
	 * @param \Twig\Environment $twig
	 * 
	 */ 
	public function index( ) 
	{
		
		try 
		{
		    return render('data_table', 
			[
		        'load_vue' => true,
		        'title' => translate('Cities'),
		        'columns' => $this->columns(),
		        'fillable' => $this->fillable(),
		        'items' => $this->repo->get(),
		        'object_name' => 'City',
		        'object_key' => 'city_id',
		    ]);
			
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
	}


	public function store() 
	{

		$params = $this->app->params();

        try {	

        	$params['created_by'] = $this->app->auth()->id;

            $returnData = (!empty($this->repo->store($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (Exception $e) {
        	throw new Exception(json_encode(array('result'=>$e->getMessage(), 'error'=>1)), 1);
        }

		return $returnData;
	}



	public function update()
	{
		$params = $this->app->params();

        try {

        	$params['status'] = !empty($params['status']) ? 'on' : null;

            if ($this->repo->update($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>1);
            }
        

        } catch (\Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}


	public function delete() 
	{

		$params = $this->app->params();

        try {

        	$check = $this->repo->find($params['city_id']);


            if ($this->repo->delete($params['city_id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
            }
            

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}

	public function validate($params) 
	{

		if (empty($params['title']))
		{
        	throw new \Exception(json_encode(array('result'=>translate('NAME_EMPTY'), 'error'=>1)), 1);
		}

	}


}