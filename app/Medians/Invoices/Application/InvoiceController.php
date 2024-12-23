<?php

namespace Medians\Invoices\Application;
use \Shared\dbaser\CustomController;

use Medians\Users\Application\GetStartedController;

use Medians\Invoices\Infrastructure\InvoiceRepository;

class InvoiceController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;
	protected $app;



	function __construct()
	{
        $this->app = new \config\APP;
		
		$user = $this->app->auth();
		$this->repo = new InvoiceRepository();

	}



	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function columns( ) 
	{

		return [
            [ 'value'=> "invoice_id", 'text'=> "#"],
            [ 'value'=> "customer.name", 'text'=> translate('User'), 'sortable'=> false ],
            [ 'value'=> "item.package.name", 'text'=> translate('Package'), 'sortable'=> true ],
            [ 'value'=> "total_amount", 'text'=> translate('Total Amount'), 'sortable'=> true ],
            [ 'value'=> "payment_method", 'text'=> translate('Gateway'), 'sortable'=> true ],
            [ 'value'=> "date", 'text'=> translate('Date'), 'sortable'=> true ],
            [ 'value'=> "status", 'text'=> translate('status'), 'sortable'=> false ],
            [ 'value'=> "notes", 'text'=> translate('Notes'), 'sortable'=> true ],
			['value'=>'edit', 'text'=>translate('View')],
			// ['value'=>'delete', 'text'=>translate('Delete')],
        ];
	}


	/**
	 * Admin index items
	 * Loads in vue 
	 */ 
	public function index() 
	{
		$params = sanitizeInput($this->app->request()->query->all());

		return render('invoices', [
			'load_vue'=> true,
	        'title' => translate('Invoices list'),
	        'items' => $this->repo->getByDate($params),
	        'columns' => $this->columns(),
	    ]);
	}



	/**
	 * Store item to database
	 * 
	 * @return [] 
	*/
	public function store() 
	{	
        
		$params = $this->app->params();

        try {
			
            return ($this->repo->store($params))
            ? array('success'=>1, 'result'=>translate('Added'), 'reload'=>1)
            : array('error'=>translate('Err'));


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
		$params = $this->app->params();

        try {

           	$returnData =  ($this->repo->update($params))
           	? array('success'=>1, 'result'=>translate('Updated'), 'reload'=>1)
           	: array('error'=>translate('Not allowed'));

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

		$params = $this->app->params();

        try {

           	$returnData =  $this->repo->delete($params['invoice_id'])
           	? array('success'=>1, 'result'=>translate('Deleted'), 'reload'=>1)
           	: array('error'=>translate('Not allowed'));


        } catch (Exception $e) {
            $returnData = array('error'=>$e->getMessage());
        }

        return response($returnData);

	}


	public function addInvoice($params)
	{
		
		try {
			
			return $this->repo->store($params); 

		} catch (Exception $e) {
			return array('error'=>$e->getMessage());
		}
	}


	
    /**
     * Invoice page for frontend
     */
    public function page($invoice_code)
    {
		$this->app = new \config\APP;
		
		$settings = $this->app->SystemSetting();

		$customer = $this->app->customer_auth();

		try {

			$item = $this->repo->findByCode($invoice_code);

			if (empty($item))
				return Page404();

			if (!$customer || $customer->customer_id != $item->customer_id)
				return Page404();

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
				'invoice' => $item,
                'layout' => 'invoice'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }

	
	/**
     * Invoices list page for studio frontend
     */
    public function studio()
    {
		$customer = $this->app->customer_auth();

		$settings = $this->app->SystemSetting();

		try {

            return printResponse(render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
                'app' => $this->app,
                'item' => $customer,
                'layout' => isset($this->app->customer->customer_id) ? 'studio_invoices' : 'signin'
            ], 'output'));
            
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
    }


}
