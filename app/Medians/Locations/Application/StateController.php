<?php
namespace Medians\Locations\Application;

use Shared\dbaser\CustomController;

use Medians\Locations\Infrastructure\StateRepository;
use Medians\Locations\Infrastructure\CountryRepository;

class StateController extends CustomController 
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $countryRepo;

	function __construct()
	{

		$this->app = new \config\APP;

		$this->repo = new StateRepository();
		
		$this->countryRepo = new CountryRepository();

	}




	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "state_id", 'text'=> "#",'sortable'=> true],
            [ 'value'=> "name", 'text'=> translate('name'), 'sortable'=> true ],
            [ 'value'=> "country.name", 'text'=> translate('country'), 'sortable'=> true ],
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
            [ 'key'=> "state_id", 'title'=> "#", 'column_type'=>'hidden'],
			[ 'key'=> "country_id", 'title'=> translate('Country'), 
				'fillable'=> true, 'column_type'=>'select','text_key'=>'name', 'required'=>true, 'withLabel'=>true, 
				'data' => $this->countryRepo->get()
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
		        'title' => translate('States'),
		        'columns' => $this->columns(),
		        'fillable' => $this->fillable(),
		        'items' => $this->repo->getWithCities(),
		        'object_name' => 'State',
		        'object_key' => 'state_id',
		    ]);
			
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
	}


	public function store() 
	{

		$params = $this->app->params();

        try {	
			try {
			
				$this->validate($params) ;

			} catch (\Throwable $th) {
	        	return array('error'=>$th->getMessage());
			}

        	$params['created_by'] = $this->app->auth()->id;
        	$params['status'] = isset($params['status']) ? 'on' : null;

            $returnData = (!empty($this->repo->store($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('success'=>0, 'result'=>'Error', 'error'=>1);

        } catch (Exception $e) {
        	return array('error'=>$e->getMessage());
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

        	$check = $this->repo->find($params['state_id']);


            if ($this->repo->delete($params['state_id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
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

		if (isset($this->repo->findByName($params['name'])->state_id))
		{
        	throw new \Exception(translate('NAME_FOUND'), 1);
		}

	}


}