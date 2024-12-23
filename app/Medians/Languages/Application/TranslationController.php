<?php
namespace Medians\Languages\Application;

use Shared\dbaser\CustomController;

use Medians\Languages\Infrastructure\TranslationRepository;
use Medians\Languages\Infrastructure\LanguageRepository;

class TranslationController extends CustomController 
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $languageRepo;

	function __construct()
	{

		$this->app = new \config\APP;

		$this->repo = new TranslationRepository();
		
		$this->languageRepo = new LanguageRepository();

	}




	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            // [ 'value'=> "translation_id", 'text'=> "#",'sortable'=> true],
            [ 'value'=> "code", 'text'=> translate('Code'), 'sortable'=> true ],
            [ 'value'=> "language.name", 'text'=> translate('language'), 'sortable'=> true ],
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
		    return render('translations', 
			[
		        'load_vue' => true,
		        'title' => translate('Translations'),
		        'columns' => $this->columns(),
		        'fillable' => $this->fillable(),
		        'items' => $this->repo->get(),
		        'languages' => $this->languageRepo->getActive(),
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
			
				$params['code'] =  strtolower(str_replace([' ', '/', '&', '?','؟' , '@', '#', '$', '%', '(', ')', '-', '='], '_', $params['translation']['english'])) ;
				$this->validate($params) ;

			} catch (\Throwable $th) {
	        	return array('error'=>$th->getMessage());
			}

        	$params['created_by'] = $this->app->auth()->id;

            $returnData = (!empty($this->repo->storeItems($params))) 
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>0)
            : array('result'=>'Error', 'error'=>1);

        } catch (Exception $e) {
        	return array('error'=>$e->getMessage());
        }

		return $returnData;
	}



	public function update()
	{
		$params = $this->app->params();

        try {

            if ($this->repo->updateItems($params))
            {
                return array('success'=>1, 'result'=>translate('Updated'), 'reload'=>1);
            }
        

        } catch (\Exception $e) {
        	throw new \Exception("Error Processing Request". $e->getMessage(), 1);
        	
        }

	}


	public function delete() 
	{

		$params = $this->app->params();

        try {

        	$check = $this->repo->find($params['translation_id']);


            if ($this->repo->delete($params['translation_id']))
            {
                return json_encode(array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1));
            }
            

        } catch (Exception $e) {
        	throw new \Exception("Error Processing Request", 1);
        	
        }

	}

	public function validate($params) 
	{

		if (isset($this->repo->findByCode($params['code'])->translation_id))
		{
        	throw new \Exception(translate('COUPON_DUPLICATED'), 1);
		}

	}


}