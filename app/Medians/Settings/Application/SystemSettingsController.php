<?php

namespace Medians\Settings\Application;
use \Shared\dbaser\CustomController;

use Medians\Settings\Infrastructure\SystemSettingsRepository;
use Medians\Languages\Infrastructure\LanguageRepository;


class SystemSettingsController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $languageRepo;
	
	public $updated = true;



	function __construct()
	{
		
		$this->app = new \config\APP;

		$this->repo = new SystemSettingsRepository();

		$this->languageRepo = new LanguageRepository();

	}

	
	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
            
			'basic'=> [	
				
				[ 'key'=> "default_dashboard_start_date", 'title'=> translate('Default dashboard date'), 'help_text'=> translate('The default start date for loading dashboard stats and charts'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title', 
					'data' => [
						['default_dashboard_start_date'=>'01-01','title'=>translate('First day of Year')], 
						['default_dashboard_start_date'=>'m-01','title'=>translate('First day of Month')],
						['default_dashboard_start_date'=>'m-d','title'=>translate('Today')]
					]  
				],	
			],	
			
			'smtp'=> [	
				[ 'key'=> "smtp_sender", 'title'=> translate('smtp_sender'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "smtp_user", 'title'=> translate('SMTP_USER'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "smtp_password", 'title'=> translate('smtp_password'), 'fillable'=> true, 'column_type'=>'password' ],
				[ 'key'=> "smtp_host", 'title'=> translate('smtp_host'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "smtp_port", 'title'=> translate('smtp_port'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "smtp_auth", 'title'=> translate('smtp_auth'), 
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title', 
					'data' => [['smtp_auth'=>'1','title'=>"True"], ['smtp_auth'=>'0','title'=>"False"]]  
				],
			],
			
			'google'=> [	
				[ 'key'=> "allow_google_login", 'title'=> translate('Login with Google'), 'help_text'=>translate('Allow users to signup with Gmail'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "google_client_id", 'title'=> translate('Google Client ID'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "google_client_secret", 'title'=> translate('Google Client secret'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "google_map_api", 'title'=> translate('Google Map API'), 'help_text'=>translate('Used for maps'),'fillable'=> true, 'column_type'=>'text' ],
			],
			
			'facebook'=> [	
				[ 'key'=> "allow_facebook_login", 'title'=> translate('Login with Facebook'), 'help_text'=>translate('Allow users to signup with Gmail'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "facebook_client_id", 'title'=> translate('Facebook Client ID'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "facebook_client_secret", 'title'=> translate('Facebook Client secret'), 'fillable'=> true, 'column_type'=>'text' ],
			],
			
			// 'twitter'=> [	
			// 	[ 'key'=> "allow_twitter_login", 'title'=> translate('Login with Twitter'), 'help_text'=>translate('Allow users to signup with Twitter ( X ) account'), 'fillable'=> true, 'column_type'=>'checkbox' ],
			// 	[ 'key'=> "twitter_api_key", 'title'=> translate('Twitter API Key'), 'fillable'=> true, 'column_type'=>'text' ],
			// 	[ 'key'=> "twitter_client_secret", 'title'=> translate('Twitter Client secret'), 'fillable'=> true, 'column_type'=>'text' ],
			// 	[ 'key'=> "twitter_redirect_link", 'title'=> translate('Twitter Redirect callback'), 'help_text'=> translate('Redirect should be scheme like (mediansparents://callback)'), 'fillable'=> true, 'column_type'=>'text' ],
			// ],
			
			// 'stripe'=> [	
			// 	[ 'key'=> "stripe_publish_key", 'title'=> translate('Stripe publishable key'), 'fillable'=> true, 'column_type'=>'text' ],
			// 	[ 'key'=> "stripe_live_key", 'title'=> translate('Stripe live key'), 'fillable'=> true, 'column_type'=>'text' ],
			// 	[ 'key'=> "stripe_mode", 'title'=> translate('Stripe mode'), 
			// 		'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title', 
			// 		'data' => [['stripe_mode'=>'live','title'=>'Live'], ['stripe_mode'=>'sandbox','title'=>'Sandbox']]  
			// 	],
			// ],
			
        ];
	}

	/**
	 * Index settings page
	 * 
	 */
	public function index()
	{
		return render('system_settings', [
		        'load_vue' => true,
		        'setting' => $this->getAll(),
		        'fillable' => $this->fillable(),
	        	'title' => translate('System_Settings'),
	    ]);
	} 



	public function getItem($code = null) 
	{	
		return $this->repo->getByCode($code);
	}


	public function getAll() 
	{	
		$data = $this->repo->getAll();
		$output = $data ? array_column(json_decode($data), 'value', 'code') :  [];
		return $output;
	}



	/**
	* Return the Settings
	*/
	public function update() 
	{
		$params = $this->app->params();

		try {

            if (isset($this->updateSettings($params)->updated)) 
            	return array('success'=>1, 'result'=>translate('Updated'));

        } catch (Exception $e) {
            return  array('error'=>$e->getMessage());
        }
	}



	/**
	* Return the Settings
	*/
	public function updateSettings($params) 
	{
		try {
			
			foreach ($params as $code => $value)
			{
				$this->deleteItem($code)->saveItem($code, $value);
			}

			$this->updated = true;
			
			return $this;

		} catch (Exception $e) {
            return  array('error'=>$e->getMessage());
		}
	}




	public function saveItem($code, $value) 
	{
		if (is_array($value))
			return $this->saveItemArray($code, $value);
		
		$data = [
			'created_by' => $this->app->auth()->id,
			'code' => $code,
			'value' => $value
		];

		return $this->repo->store($data);

	}


	public function saveItemArray($code, $value) 
	{
		foreach ($value as $k => $v) 
		{
			$data = [
				'created_by' => $this->app->auth()->id,
				'code' => $code,
				'value' => $v
			];
			
			$this->repo->store($data);
		}
	}


	public function deleteItem($code) 
	{
		$this->repo->delete($code);

		return $this;
	}


}
