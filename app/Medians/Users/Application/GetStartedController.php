<?php

namespace Medians\Users\Application;

use Medians\Businesses\Infrastructure\BusinessRepository;
use Medians\Plans\Infrastructure\PlanRepository;
use Medians\Plans\Infrastructure\PlanSubscriptionRepository;
use Medians\Settings\Infrastructure\SettingsRepository;


class GetStartedController 
{


	/*
	/ @var new CustomerRepository
	*/
	private $businessRepo;

	protected $app;

	public $planRepo;
	
	public $planSubscriptionRepo;

	public $settingRepo;

	function __construct()
	{
		$this->app = new \config\APP;		

		$this->businessRepo = new BusinessRepository();

		$this->planSubscriptionRepo = new PlanSubscriptionRepository();

		$this->planRepo = new PlanRepository();

		$this->settingRepo = new SettingsRepository(null);
		
	}



	/**
	 * Get-started page 
	 * for set configuration
	 */
	public function get_started()
	{

		return render('get_started', [
			'load_vue'=> true,
			'plans' =>   $this->planRepo->get(),
	        'title' => translate('Get_started'),
	    ]);
		return render('views/admin/get-started.html.twig',['user'=>$this->app->auth()]);

	}


	/**
	*  Store setting for new user
	*/
	public function store_setting($business) 
	{
		$user = $this->app->auth();

		$params = [
			[
				'code' => 'logo',
				'value' => '/uploads/img/letters/'.strtolower(substr($user->first_name, 0, 1)).'.png'
			],
			[
				'code' => 'allow_applicants',
				'value' => 'on'
			],
			[
				'code' => 'allow_taxi_trip',
				'value' => 'on'
			],
			[
				'code' => 'email',
				'value' => $user->email
			],
			[
				'code' => 'lang',
				'value' => $this->app->lang ?? $this->app->default_lang
			]
		];

		foreach ($params as $row) 
		{
			$row['created_by'] = $user->id;
			$store = $this->settingRepo->store($row);
		}

		return $store;
	}


	/**
	*  Store business for new user
	*/
	public function saveBusiness() 
	{
		$params = (array)  $this->app->params();

		try {

			$params['status'] = 'on';
			$params['user_id'] = $this->app->auth()->id;
			$save = $this->businessRepo->store($params);

			if (isset($save->business_id))
				$this->saveActiveBusiness($save);

        	return isset($save->business_id) 
           	? array('success'=>1, 'result'=> $save, 'reload'=>1)
        	: array('error'=> $save );

        } catch (Exception $e) {
        	return array('error'=> $e->getMessage() );
        }
	}


	/**
	 * Save the created business 
	 * for the active session
	 * 
	 */ 
	public function saveActiveBusiness($business)
	{

		$user = $this->app->auth();

		$user->update(['active_business'=>$business->business_id]);

		$addDefaultSetting = $this->store_setting($business);

		return $this;
	} 



	/**
	 * Save the created business 
	 * for the active session
	 * 
	 */ 
	public function saveSelectedPlan()
	{

		try {

			$user = $this->app->auth();

			$params = $this->app->params();
			$plan = $this->planRepo->find($params['plan_id']);

			// Check if plan is exist
			if (empty($plan))
				return null;


			// Check if plan is premium 
			if ($plan->type == 'paid')
				return $this->subscribePaidPlan($plan, $params['payment_type']);

			$save = $this->savePlan($plan, $params['payment_type']);

        	return isset($save->plan_id) 
           	? array('success'=>1, 'result'=>translate('Created'))
        	: array('error'=> $save );

        } catch (Exception $e) {
            return  $e->getMessage();
        }
	} 

	/**
	 * Subscribe to paid plan
	 * 
	 */
	public function saveFreePlan()
	{
		try {

			$params = $this->app->params();
			$user = $this->app->auth();

			// Store new subscription 
			$planSubscription = [];
			$planSubscription['plan_id'] = $params['plan_id'];
			$planSubscription['payment_type'] = $params['payment_type'];
			$planSubscription['user_id'] = $user->id;
			$planSubscription['start_date'] = date('Y-m-d');
			$planSubscription['end_date'] = date('Y-m-d', strtotime('+1 '.($params['payment_type'] == 'monthly' ? 'month' : 'year'))) ;
	
			$save = $this->planSubscriptionRepo->store($planSubscription);

			return isset($save->plan_id) 
			? array('success'=>1, 'result'=>translate('Subscribed successfully'))
			: array('error'=> $save );

		} catch (Exception $e) {
			return  $e->getMessage();
		}
	} 

	/**
	 * Subscribe to paid plan
	 * 
	 */
	public function subscribePaidPlan($plan, $paymentType='monthly')
	{

	
	} 


}
