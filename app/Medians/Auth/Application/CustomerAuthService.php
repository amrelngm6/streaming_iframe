<?php

namespace Medians\Auth\Application;

use Medians\Auth\Domain\CustomerAuthModel;

use Medians\Settings\Application\SystemSettingsController;

use Medians\Mail\Application\MailService;

use Google_Service_Oauth2;

class CustomerAuthService 
{

	
	private $app;

	/**
	 * Minimum length of the user password
	 * 
	 * @var Int
	*/
	private $passLen = 5;

	/**
	* @var Instance CustomerAuthModel
	*/
	private $CustomerAuthModel;

	/**
	* @var Instance Repo
	*/
	protected $repo;


	function __construct()
	{
		$this->repo = new \Medians\Customers\Infrastructure\CustomerRepository();
	}
 

	/**
	 * Display login page 
	 */
	public function loginPage()
	{
		try {
			
			$this->app = new \config\APP;

			if (isset($this->app->customer_auth()->customer_id)) { return $this->app->redirect('/customer/dashboard'); }
            $settings = $this->app->SystemSetting();

		    // return  render('login', [
			return render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
		    	// 'load_vue' => true,
		        'title' => translate('Login page'),
		        'app' => $this->app,
		        'google_login' => $this->loginWithGoogle(),
				'layout' => 'signin'
		    ]);
		    
		} catch (Exception $e) {
        	throw new Exception("Error Processing Request", 1);
		}
	}

	
	/**
	 * Display login page 
	 */
	public function signupPage()
	{
		try {
			
			$this->app = new \config\APP;

			if (isset($this->app->customer_auth()->customer_id)) { return $this->app->redirect('/customer/dashboard'); }
            $settings = $this->app->SystemSetting();

			return render('views/front/'.($settings['template'] ?? 'default').'/pages/signup.html.twig', [
		        'title' => translate('Signup page'),
		        'app' => $this->app,
		        'google_login' => $this->loginWithGoogle(),
		    ]);
		    
		} catch (Exception $e) {
        	throw new Exception("Error Processing Request", 1);
		}
	}

	
	/**
	 * Display login page 
	 */
	public function otp()
	{
		try {
			
			$this->app = new \config\APP;

			if (isset($this->app->customer_auth()->customer_id)) { return $this->app->redirect('/customer/dashboard'); }
            $settings = $this->app->SystemSetting();

			return render('views/front/'.($settings['template'] ?? 'default').'/layout.html.twig', [
		        'title' => translate('Activate account'),
		        'app' => $this->app,
				'layout' => 'confirm-email'
		    ]);
		    
		} catch (Exception $e) {
        	throw new Exception("Error Processing Request", 1);
		}
	}
	
	
	/**
	 * Display login page 
	 */
	public function resetPasswordPage()
	{
		try {
			
			$this->app = new \config\APP;

			if (isset($this->app->customer_auth()->customer_id)) { return $this->app->redirect('/customer/dashboard'); }
            $settings = $this->app->SystemSetting();

			return render('views/front/'.($settings['template'] ?? 'default').'/pages/reset-password.html.twig', [
		        'title' => translate('Reset your password'),
		        'app' => $this->app,
		    ]);
		    
		} catch (Exception $e) {
        	throw new Exception("Error Processing Request", 1);
		}
	}
	
	/**
	 * Display login page 
	 */
	public function resetPasswordCodePage()
	{
		try {
			
			$this->app = new \config\APP;

			if (isset($this->app->customer_auth()->customer_id)) { return $this->app->redirect('/customer/dashboard'); }
            $settings = $this->app->SystemSetting();

			return render('views/front/'.($settings['template'] ?? 'default').'/pages/reset-password-code.html.twig', [
		        'title' => translate('Your reset token'),
		        'app' => $this->app,
		    ]);
		    
		} catch (Exception $e) {
        	throw new Exception("Error Processing Request", 1);
		}
	}

	/**
	 * Activate account by opening sent link
     * through Email 
	 */ 
	public function activate($code)
	{
		$this->app = new \config\APP;
		$settings = $this->app->SystemSetting();
		
        try {
            
            $checkUser = $this->repo->findByToken($code, 'activation_token');

            if (!empty($checkUser))
            {
				$updated = $checkUser->update(['status'=>'on']);
			}

			return render('views/front/'. ($settings['template'] ?? 'default') .'/layout.html.twig', [
				// 'load_vue' => true,
				'title' => translate('Activation page'),
				'app' => $this->app,
				'msg' => isset($updated) ? translate('Account activated successfully') : translate('Code not valid'),
				'valid' => isset($updated) ? true : false,
				'layout' => 'activate',
			]);

        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);
        }
	}

	/**
	 * Activate account by inserting OTP manually
	 */ 
	public function checkOTP()
	{
		$this->app = new \config\APP;
		$params = $this->app->params();

        try {
            
            $checkUser = $this->repo->findByToken($params['code'], 'otp');

            if (!empty($checkUser))
            {
				$updated = $checkUser->update(['status'=>'on']);
                return printResponse(array('success'=>1, 'result'=>translate('Account activated successfully'), 'redirect'=> '/customer/dashboard'));
			}
            
            return printResponse(array('error'=>1, 'result'=>translate('Code not valid')));

        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);
        }
	}


	public function verifyLoginWithFacebook()
	{
		
		$this->app = new \config\APP;

		try {
				
			// Get system settings for Facebook Login
			$settings = $this->app->SystemSetting();

			$Facebook = new FacebookService($settings['facebook_client_id'], $settings['facebook_client_secret']);

			return $Facebook->verifyLoginWithFacebook();

        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);
        }
	}


	public function verifyLoginWithGoogle()
	{

		$this->app = new \config\APP;

		try {
				
			// Get system settings for Google Login
			$settings = $this->app->SystemSetting();

			$Google = new GoogleService($settings['google_client_id'], $settings['google_client_secret']);

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

			$localPic = $Google->saveImageFromUrl($user_info['picture'], '/uploads/images/'.md5($user_info['picture']).'.jpg');

			// Prepare user data to store
			$params['email'] = $user_info['email'];
			$params['name'] = $user_info['name'];
			$params['picture'] = $localPic;
			$params['status'] = 'on';

			$user = $this->repo->findByEmail($params['email']);

			if (empty($user->customer_id))
				$user = $this->repo->store($params);

			// Check if user saved
			if (isset($user->customer_id)){
				$this->setSession($user);
		    	$this->repo->setCustomCode((object) $user, 'google_id', $user_info['id']);
			} else {
				return null;
			}  

			echo $this->app->redirect(isset($user->field['activation_token']) ? './activate-account/'.$user->field['activation_token'] : '/customer/dashboard');

		} catch (Exception $e) {
			return array('error'=>$e->getMessage());
		}
	}


	public function loginWithGoogle()
	{
		
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

		if (empty($settings['google_client_id']))
			return null;

		$Google = new GoogleService($settings['google_client_id'],$settings['google_client_secret']);

		return $Google->client->createAuthUrl();
	}



	public function loginWithFacebook()
	{
		
		$this->app = new \config\APP;

		$settings = $this->app->SystemSetting();

		if (empty($settings['facebook_client_id']))
			return null;

		$Facebook = new FacebookService($settings['facebook_client_id'],$settings['facebook_client_secret']);

		return $Facebook->getLoginUrl();
	}


	/**
	 * User login request
	 */ 
	public function userLogin()
	{
		$this->app = new \config\APP;
		
        $params = $this->app->params();

        try {
            
            $checkUser = $this->checkLogin($params['email'], $this->encrypt($params['password']));

            if (!empty($checkUser->customer_id))
            {
                $this->setSession($checkUser);
            	echo json_encode(array('success'=>1, 'result'=>translate('Logged in'), 'redirect'=>'/customer/dashboard'));

            } else {
	            echo json_encode(array('error'=>1, 'result'=>$checkUser));
            }

        } catch (Exception $e) {
        	throw new Exception("Error Processing Request", 1);
        }
	}


	/**
	 * User Signup request
	 */ 
	public function userSignup()
	{
		$this->app = new \config\APP;
		
        $params = $this->app->params();

        try {
            
            $checkUser = $this->checkSignup($params);

            if (empty($checkUser))
            {
				$checkUser = $this->repo->signup($params);

				if (!empty($checkUser->customer_id))
				{
					echo json_encode(array('success'=>1, 'result'=>translate('Your account created successfully'), 'redirect'=>'/customer/confirm_account'));
				}

            } else {
	            echo json_encode(array('error'=>1, 'result'=>$checkUser));
            }

        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);
        }
	}

	/**
	 * User Reset Password request
	 */ 
	public function userResetPassword()
	{
		$this->app = new \config\APP;
		
        $params = $this->app->params();

        try {
            
            $checkUser = $this->repo->resetPassword($params);

			echo ($checkUser == 1) 
			? json_encode(array('success'=>1, 'result'=>translate('Code sent through email'), 'redirect'=>$this->app->CONF['url'].'reset-password-code')) 
			: json_encode(array('error'=>$checkUser));

        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);
        }
	}

		/**
	 * User Reset Password request
	 */ 
	public function resetChangePassword()
	{
		$this->app = new \config\APP;
		
        $params = $this->app->params();

        try {
            
            $checkUser = $this->repo->resetChangePassword($params);

			echo (isset($checkUser->customer_id)) 
			? json_encode(array('success'=>1, 'result'=>translate('Password updated successfully'), 'redirect'=>$this->app->CONF['url'].'customer/login')) 
			: json_encode(array('error'=>$checkUser, 'no_reset'=>1));

        } catch (Exception $e) {
        	throw new \Exception($e->getMessage(), 1);
        }
	}


	/**
	 * Check login credentials
	 * 
	 * @return Object / String 
	 */ 
	public function checkLogin($email, $password)
	{
		$checkLogin = $this->repo->checkLogin($email, $password);

		if (empty($checkLogin->customer_id))
		{
            return translate("User credentials not valid");
		}

		if ($checkLogin->status != 'on')
		{
			return translate("User account is not active");
		}

		return $checkLogin;
	}


	/**
	 * Check Signup credentials
	 * 
	 * @return Object / String 
	 */ 
	public function checkSignup($params)
	{
		$getByEmail = $this->repo->findByEmail($params['email']);
		
		if (isset($getByEmail->customer_id))
		{
            return translate("EMAIL_FOUND");
		}
	}

	/**
	 * Change password request
	 * 
	 * @return Array 
	 */ 
	public function changePassword()
	{
		try {
			
			$this->app = new \config\APP;
			$customer = $this->app->customer_auth();
			$params = $this->app->params();

			if ($this->encrypt($params['old_password']) != $customer->password)
				throw new \Exception(translate('Password is not valid'), 1);

			if ($params['password'] != $params['confirm_password'])
				throw new \Exception(translate('New Password not matched'), 1);
				

			$check = $this->repo->changePassword($params, $customer);
			
			return  printResponse((isset($check->customer_id))
			? array('success'=>1, 'result'=>translate('Updated successfully')) 
			: array('error'=>1, 'result'=>translate('Error'), 'no_reset'=>1));

		} catch (\Throwable $th) {
			return printResponse(array('error'=> $th->getMessage()));
		}
	}


	
	/**
	 * Validate the password length
	 * 
	 */ 
	public function validateSignup($params)
	{

        if (!empty($this->repo->getByEmail($params['email'])))
			return json_encode(array('error'=>translate('Email already found')));

        if (empty($params['email']))
			return json_encode(array('error'=>translate('Email required')));

        if (empty($params['first_name']))
			return json_encode(array('error'=>translate('Name required')));

		if (strlen($params['password']) < $this->passLen)
			return translate("Password length must be $this->passLen at least ");

	} 

	/**
	 * Validate the password length
	 * 
	 */ 
	public function validatePassword($password)
	{
		if (strlen($password) < $this->passLen)
		{
			throw new \Exception("Password length must be $this->passLen at least ", 1);
		}

	} 


	/**
	 * Check session is valid or not 
	 * 
	 * @return ? CustomerAuthModel
	 */ 
	public function checkSession($code = null) 
	{
		$this->CustomerAuthModel = new CustomerAuthModel($code);
		
		$check = $this->CustomerAuthModel->checkSession($code);

		if (!empty ( $check ))
		{
			return $this->repo->find($check);
		}
	}


	/**
	 * Check session id  
	 * 
	 * @return ? CustomerAuthModel
	 */ 
	public function checkSessionId($code = null) 
	{
		$this->CustomerAuthModel = new CustomerAuthModel($code);
		
		return $this->CustomerAuthModel->checkSession($code);

	}


	/**
	 * Check session is valid or not 
	 * for Mobile / API Tokens
	 * 
	 * @return ? CustomerAuthModel
	 */ 
	public function checkAPISession($token = null, $userType = null) 
	{
				
		switch ($userType) {
			default:
				return $this->repo->findByToken($token);
				break;
		}
	}



	/**
	 * Set session  
	 */ 
	public function setSession($data, $code = null) 
	{

		$this->CustomerAuthModel = new CustomerAuthModel($code);

		if ($this->CustomerAuthModel->setData($data)) 
		{
			return $this->CustomerAuthModel->checkSession($code);
		}
	}


	/**
	 * Clear session after logout
	 */ 
	public function unsetSession() 
	{
		
		$this->CustomerAuthModel = new CustomerAuthModel();

		return $this->CustomerAuthModel->unsetSession();
	}

	/**
	 * Encryption algoritm for password storage
	 */ 
	public static function encrypt($value) : String 
	{
		return sha1(md5($value));
	}




}
