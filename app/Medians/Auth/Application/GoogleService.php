<?php

namespace Medians\Auth\Application;


use Medians\Mail\Application\MailService;

use Google\Client;

use Medians\Auth\Domain\AuthModel;

use Google_Service_Oauth2;
use Medians\Settings\Application\SystemSettingsController;


class GoogleService 
{

	/**
	* @var Instance Repo
	*/
	protected $repo;

	/**
	* @var Instance App
	*/
	protected $app;
	
	public $client;

	function __construct($client_id, $client_secret)
	{

		$this->app = new \config\APP;
		
		$this->repo = new \Medians\Users\Infrastructure\UserRepository();

		$this->client = new Client();
		$this->client->setClientId($client_id);
		$this->client->setClientSecret($client_secret);
		$this->client->setRedirectUri($this->app->CONF['url'].'google_login_redirect');
		$this->client->addScope("email");
		$this->client->addScope("profile");
	}


	public function verifyLoginWithGoogle()
	{

		$this->app = new \config\APP;


		try {
				
			// Get system settings for Google Login
			$SystemSettings = new SystemSettingsController;

			$settings = $SystemSettings->getAll();

			$Google = new GoogleService($settings['google_login_key'], $settings['google_login_secret']);

			$code = $this->app->request()->get('code');

		  	$Google->client->setAccessToken($Google->client->fetchAccessTokenWithAuthCode($code));

		  	// Check if code is expired or invalid
		  	if($Google->client->isAccessTokenExpired())
		  	{
	  			return false;
		  	}


	  		// Get user data through API
			$google_oauth = new Google_Service_Oauth2($Google->client);
			$user_info = $google_oauth->userinfo->get();

			// Prepare user data to store
			$pictureName = rand(999999, 999999).date('Ymdhis').'.jpg';
			$params['email'] = $user_info['email'];
			$params['first_name'] = $user_info['givenName'];
			$params['last_name'] = $user_info['familyName'];
			$params['role_id'] = '3';
			$params['picture'] = $this->saveImageFromUrl($user_info['picture'], '/uploads/customers/'.$pictureName) ;

			// $params['field']['google_id'] = $user_info['id'];

			$user = $this->repo->getByEmail($params['email']);

			if (isset($user->id))
				$user->update(['picture' => $user_info['picture']]);
			else 
				$user = $this->repo->store($params);

			// Check if user saved
			if (isset($user->id)){
				$this->setSession($user);
		    	$this->repo->setCustomCode((object) $user, 'google_id', $user_info['id']);
			} else {
				return null;
			}  

			echo $this->app->redirect('/dashboard');

		} catch (Exception $e) {
			return array('error'=>$e->getMessage());
		}


	}



	/**
	 * Set session  
	 */ 
	public function setSession($data, $code = null) 
	{

		$AuthService = new AuthService;

		return $AuthService->setSession($data, $code);
	}


	function saveImageFromUrl($url, $localPath) 
	{
		$image = file_get_contents($url);
		if ($image !== false) {
			file_put_contents($_SERVER['DOCUMENT_ROOT']. $localPath, $image);
			return $localPath; 
		}
	}






}
