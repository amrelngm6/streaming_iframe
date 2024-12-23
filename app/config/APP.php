<?php

namespace config;

use Twig\Environment;
use \Shared\RouteHandler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Medians\Settings\Infrastructure\SystemSettingsRepository;

use \Medians\Auth\Application\AuthService;
use \Medians\Auth\Application\CustomerAuthService;
use \Medians\Auth\Application\GuestAuthService;


class APP 
{

	public $default_lang = 'english';

	public $lang_code = 'english';

	public $lang;

	public $auth;

	public $customer;

	public $hasBranches = false;

	public $CONF;

	public $currentPage;

	public $capsule;

	public $session;
	
	public $setting;


	function __construct()
	{

		// $this->setLang(); // Set the active language 

		$this->currentPage = $this->request()->getPathInfo(); // Filter the request URI to get the current page

		$this->CONF = (new \config\Configuration())->getCONFArray();  // Load configuration as Array

		$this->capsule = (new \config\Configuration())->checkDB(); // Check database connection

		$this->auth(); // Check active secttion

	}

	public function setLang()
	{
		if (!empty($this->request()->headers->get('lang')))
		{
			$_SESSION['site_lang'] = $this->request()->headers->get('lang');
		}

		if (isset($_SERVER['HTTP_REFERER']))
		{
			$arr = explode('/', $_SERVER['HTTP_REFERER']);

			if (in_array(end($arr), array_column($this->Languages()->toArray(), 'language_code') ))
			{
				$_SESSION['site_lang'] = end($arr);
				$_SESSION['lang'] = end($arr);
			}
		}
		
		$_SESSION['lang'] = isset($_SESSION['site_lang']) ? $_SESSION['site_lang'] : $this->lang_code;
		$this->lang = $_SESSION['lang']; // Check active language

		return $this;
	}

	/**
	 * Is dark
	 */
	public function is_dark()
	{
		$setting = $this->SystemSetting();
		$_SESSION['is_dark'] = $_SESSION['is_dark'] ?? (!empty($setting['is_dark']));
		return !empty($_SESSION['is_dark']);
	}


	/**
	 * Is dark
	 */
	public function cookie_accepted()
	{
		$setting = $this->SystemSetting();
		return $_COOKIE['cookie_accepted'] ?? false;
	}


	/**
	 * Load Sysetem Settings
	 */ 
	public function SystemSetting()
	{
		$output = (new \Medians\Settings\Application\SystemSettingsController())->getAll();
		return $output;
	}

	/**
	 * Load languages
	 */ 
	public function Languages()
	{
		$output = (new \Medians\Languages\Application\LanguageController())->getAll();
		return $output;
	}

	/**
	 * Load Playlists
	 */ 
	public function playlists()
	{
		$this->customer = $this->customer_auth();
		$output = (new \Medians\Playlists\Infrastructure\PlaylistRepository())->getByCustomer($this->customer->customer_id ?? 0);
		return $output;
	}

	

	/**
	 * Get setting value by code 
	 * return value
	 */ 
	public function setting($code)
	{
		return (new SettingsRepository)->getByCode($code);
	}

	public function auth()
	{
		$request = Request::createFromGlobals();
		
		$this->session = !empty($this->session) ? $this->session : (new AuthService())->checkSession();

		return $this->session ? $this->session : $this->checkAPISession();
	}

	public function customer_auth()
	{
		$request = Request::createFromGlobals();
		
		$session = (new CustomerAuthService())->checkSession();

		$this->customer = $session ?? $this->checkAPISession();
		
		return  $this->customer;
	}

	public function customer_id()
	{
		return (new CustomerAuthService())->checkSessionId();
	}

	public function google_login_link()
	{
		return (new CustomerAuthService())->loginWithGoogle();
	}

	public function facebook_login_link()
	{
		return (new CustomerAuthService())->loginWithFacebook();
	}

	/**
	 * Get session for the Guests
	 */
	public function guest_auth()
	{
		return (new GuestAuthService())->guestSession();
	}

	/**
	 * Check if the request is through mobile
	 */
	public function checkAPISession()
	{
		if (!empty($this->request()->headers->get('token')))
		{
			return  (new AuthService())->checkAPISession($this->request()->headers->get('token'), $this->request()->headers->get('userType'));
		}
	}  

	/**
	 * Check if the request is through mobile
	 */
	public function checkAPICustomerSession()
	{
		if (!empty($this->request()->headers->get('token')))
		{
			return  (new CustomerAuthService())->checkAPISession($this->request()->headers->get('token'), $this->request()->headers->get('userType'));
		}
	}  


	public static function request()
	{
		return Request::createFromGlobals();
	}

	/**
	 * Load all request [params] parameter
	 * Used in most of the request
 	 */
	public function params()
	{
		$params = $this->request()->get('params');
		if (!$params)
			return;

		return sanitizeInput(is_array($params) ? $params : json_decode($params));
	}

	public static function redirect($url)
	{
		echo "<img width='100%' src='/uploads/img/redirect.gif' /><style>*{margin:0;color:#fff; overflow:hidden}</style>";
		echo new RedirectResponse($url);
		exit();
	}

	public function  run()
	{
		RouteHandler::dispatch();

		return true;
	}


	/**
	 * Template for Twig render 
	 */
	public function template()
	{
		$twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader('./app'), 
		    [
		        //'cache' => '/app/cache',
		        'debug' => true,
		    ]
		);

		$twig->addFilter(new \Twig\TwigFilter('html_entity_decode', 'html_entity_decode'));

		return $twig;
	}

	/**
	 * Template for Twig render 
	 */
	public function renderTemplate($code)
	{
		$twig = $this->template()->createTemplate($code);

		return $twig;
	}

	

	/**
	* Return Administrator menu
	* List of side menu
	*/
	public function front_menu($type = 'header')
	{
		$menuRepo = new \Medians\Menus\Infrastructure\MenuRepository;
        return $menuRepo->getMenuPages($type);
	}

	/**
	* Return Administrator menu
	* List of side menu
	*/
	public function menu()
	{
		$user = $this->auth();

		if (empty($user))
			return null;

		if ($user->role_id == 1)
			return $this->superAdminMenu();
			
		return $this->checkMenuAccess($this->superAdminMenu(), $user);
	}

	
	/**
	 * Return Superadmin menu
	 * List of side menu
	 */
	public function superAdminMenu()
	{
		
		$data = array(
			
			array('permission'=> 'Dashboard.index', 'title'=>translate('Dashboard'), 'icon'=>'airplay', 'link'=>'dashboard', 'component'=>'master_dashboard'),

			array('permission'=>'Customers.index', 'title'=>translate('Artists'),  'icon'=>'users', 'link'=>'admin/customers', 'component'=>'artists'),
			
			array('title'=>translate('Media'),  'icon'=>'music', 'link'=>'#media', 'sub'=>
			[
				array('permission'=>'Audio.index', 'title'=>translate('Music'),  'icon'=>'truck', 'link'=>'admin/audio', 'component'=>'media'),
				array('permission'=>'Audiobooks.index', 'title'=>translate('Audiobooks'),  'icon'=>'tag', 'link'=>'admin/audiobooks', 'component'=>'media'),
				array('permission'=>'Videos.index', 'title'=>translate('Videos'),  'icon'=>'truck', 'link'=>'admin/videos', 'component'=>'media'),
				array('permission'=>'ShortVideos.index', 'title'=>translate('Short Videos'),  'icon'=>'tag', 'link'=>'admin/shorts', 'component'=>'media'),
			]
			),
			
			
			array('title'=>translate('Streaming'),  'icon'=>'headphones', 'link'=>'#streaming', 'sub'=>
			[
				array('permission'=>'Stations.index', 'title'=>translate('Stations'),  'icon'=>'truck', 'link'=>'admin/stations', 'component'=>'data_table'),
				array('permission'=>'Channels.index', 'title'=>translate('Channels'),  'icon'=>'tag', 'link'=>'admin/channels', 'component'=>'data_table'),
			]
			),
						
			array('title'=>translate('Categories'),  'icon'=>'list', 'link'=>'#genres', 'sub'=>
			[
				array('permission'=>'Genres.index', 'title'=>translate('Genres'),  'icon'=>'truck', 'link'=>'admin/genres', 'component'=>'categories'),
				array('permission'=>'Genres.index', 'title'=>translate('Book Genres'),  'icon'=>'truck', 'link'=>'admin/book_genres', 'component'=>'categories'),
				array('permission'=>'Genres.index', 'title'=>translate('Video Genres'),  'icon'=>'truck', 'link'=>'admin/video_genres', 'component'=>'categories'),
			]
			),
			
			array('permission'=>'Blog.index', 'title'=>translate('Blog'),  'icon'=>'edit-3', 'link'=>'admin/blog', 'component'=>'blog'),

			array( 'title'=>translate('Frontend'),  'icon'=>'airplay', 'link'=>'#frontend', 'superadmin'=> true, 'sub'=>
			[
				array('permission'=>'Pages.index', 'title'=>translate('Front Pages'),  'icon'=>'tool', 'link'=>'admin/pages', 'component'=>'pages'),
				array('permission'=>'Menus.index', 'title'=>translate('Menus'),  'icon'=>'tool', 'link'=>'admin/menus', 'component'=>'menus'),
				array('permission'=>'Gallery.index', 'title'=>translate('Gallery'),  'icon'=>'tool', 'link'=>'admin/gallery', 'component'=>'gallery'),
			]
			),

			array( 'title'=>translate('Plugins'),  'icon'=>'gift', 'link'=>'#plugins', 'superadmin'=> true, 'sub'=>
			[
				array('permission'=>'Plugins.index', 'title'=>translate('Plugins'),  'icon'=>'tool', 'link'=>'admin/plugins', 'component'=>'plugins'),
				array('permission'=>'Hooks.index', 'title'=>translate('Hooks'),  'icon'=>'tool', 'link'=>'admin/hooks', 'component'=>'hooks'),
			]
			),
			
			array( 'title'=>translate('Invoices'),  'icon'=>'target', 'link'=>'#Invoices', 'superadmin'=> true, 'sub'=>
			[
				array('permission'=>'Invoices.index', 'title'=>translate('Paid Invoices'),  'icon'=>'tool', 'link'=>'admin/invoices?status=paid', 'component'=>'invoices'),
				array('permission'=>'Invoices.index', 'title'=>translate('Pending Invoices'),  'icon'=>'tool', 'link'=>'admin/invoices?status=unpaid', 'component'=>'invoices'),
				array('permission'=>'Invoices.index', 'title'=>translate('All Invoices'),  'icon'=>'tool', 'link'=>'admin/invoices', 'component'=>'invoices'),
				array('permission'=>'Transactions.index', 'title'=>translate('Transactions'),  'icon'=>'tool', 'link'=>'admin/transactions', 'component'=>'transactions'),
			]
			),
		
			array( 'title'=>translate('Subscriptions'),  'icon'=>'package', 'link'=>'#management', 'superadmin'=> true, 'sub'=>
			[
				array('permission'=>'Packages.index', 'title'=>translate('Packages'),  'icon'=>'tool', 'link'=>'admin/packages', 'component'=>'packages'),
				array('permission'=>'PackageSubscription.index', 'title'=>translate('Package subscriptions'),  'icon'=>'tool', 'link'=>'admin/package_subscriptions', 'component'=>'data_table'),
			]
			),
			
			array('title'=>translate('Marketing'),  'icon'=>'send', 'link'=>'#newsletters', 'sub'=>
			[
				array('permission'=>'Newsletters.index', 'title'=>translate('newsletters'),  'icon'=>'truck', 'link'=>'admin/newsletters', 'component'=>'data_table'),
				array('permission'=>'Subscribers.index', 'title'=>translate('Subscribers'),  'icon'=>'truck', 'link'=>'admin/newsletter_subscribers', 'component'=>'data_table'),
				array('permission'=>'EmailTemplate.index', 'title'=>translate('Email Templates'),  'icon'=>'tag', 'link'=>'admin/email_templates', 'component'=>'data_table'),
				array('permission'=>'NotificationEvent.index', 'title'=>translate('notifications_events'),  'icon'=>'bell', 'link'=>'admin/notifications_events', 'component'=>'notifications_events'),
			]
			),
			
			
			array( 'title'=>translate('Support'),  'icon'=>'help-circle', 'link'=>'#support', 'superadmin'=> true, 'sub'=>
			[
				array('permission'=>'Comments.index', 'title'=>translate('Comments'),  'icon'=>'help-circle', 'link'=>'admin/comments', 'component'=>'data_table'),
				array('permission'=>'ContactForm.index', 'title'=>translate('Forms messages'),  'icon'=>'tag', 'link'=>'admin/contact_forms', 'component'=>'contact_forms'),
			]
			),
			
			
			array( 'title'=>translate('Settings'),  'icon'=>'tool', 'link'=>'#setting', 'superadmin'=> true, 'sub'=>
			[
				array('permission'=> 'Settings.index', 'title'=>translate('Frontend settings'),  'icon'=>'tool', 'link'=>'admin/site_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=>translate('Payment settings'),  'icon'=>'tool', 'link'=>'admin/payment_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('System Settings'),  'icon'=>'tool', 'link'=>'admin/system_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('Streaming settings'),  'icon'=>'tool', 'link'=>'admin/streaming_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('Videos settings'),  'icon'=>'tool', 'link'=>'admin/videos_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('Short Videos settings'),  'icon'=>'tool', 'link'=>'admin/short_videos_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('Audio settings'),  'icon'=>'tool', 'link'=>'admin/audio_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('Audiobooks settings'),  'icon'=>'tool', 'link'=>'admin/audiobooks_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('Channels settings'),  'icon'=>'tool', 'link'=>'admin/channels_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('Stations settings'),  'icon'=>'tool', 'link'=>'admin/stations_settings', 'component'=>'system_settings'),
				array('permission'=> 'Settings.index', 'title'=> translate('Playlists settings'),  'icon'=>'tool', 'link'=>'admin/playlists_settings', 'component'=>'system_settings'),
			]
			),

			array( 'title'=>translate('localization'),  'icon'=>'mic', 'link'=>'#localization', 'superadmin'=> true, 'sub'=>
			[
				array('permission'=>'Language.index', 'title'=>translate('Languages'),  'icon'=>'tag', 'link'=>'admin/languages', 'component'=>'data_table'),
				array('permission'=>'Translation.index', 'title'=>translate('Translations'),  'icon'=>'tag', 'link'=>'admin/translations', 'component'=>'translations'),
			]
			),
			
			array( 'title'=>translate('Users'),  'icon'=>'tool', 'link'=>'#users', 'superadmin'=> true, 'sub'=>
			[
				array('permission'=>'User.index', 'title'=>translate('Users'),  'icon'=>'users', 'link'=>'admin/users', 'component'=>'users'),
				array('permission'=> 'Roles.index', 'title'=> translate('ROLES MANAEGMENT'),  'icon'=>'tool', 'link'=>'admin/roles', 'component'=>'roles'),
			]
			),
			
			array('permission'=>'Logout', 'title'=> translate('Logout'),  'icon'=>'log-out', 'link'=>'logout'),
		);

		return $data;
	}

	/**
	 * Check permission of the menu link
	 * @param String $permission
	 * @param Instance User $user
	 */
	public function checkMenuAccess($menu, $user)
	{
	
		$newMenu = [];
		if ($user->role_id > 1 )
		{
			foreach ($menu as $key => $item)
			{
				if (isset($item['sub'])) 
				{
					foreach ($item['sub'] as $k => $sub)
					{
						$newMenu[$key]['sub'][] = isset($user->permissions[$sub['permission']]) ? $sub : null;
					}
					$newMenu[$key]['sub'] = array_values(array_filter($newMenu[$key]['sub']));
					if (isset($newMenu[$key]['sub']))
					{
						$newMenu[$key]['title'] = $item['title'];
						$newMenu[$key]['icon'] = $item['icon'];
						$newMenu[$key]['link'] = $item['link'];
					}

				} elseif ($item) {
					$newMenu[$key] = isset($user->permissions[$item['permission']]) ? $item : null;
				}

				if (empty($newMenu[$key]['sub']) && empty($newMenu[$key]['permission']) )
					$newMenu[$key] = null;
			}

			return array_values(array_filter($newMenu));
		}
		return $menu;
	}


	
	
	/**
	 * Check permission of the menu link
	 * @param String $permission
	 * @param Instance User $user
	 */
	public function checkMenuPermissionsAccess($menu, $user)
	{
		if ($user->role_id == 3 )
		{
			return $menu;
		}

		$newMenu = [];
		if ($user->role_id > 3 )
		{
			foreach ($menu as $key => $item)
			{
				if (isset($item['sub'])) 
				{

					foreach ($item['sub'] as $k => $sub)
					{
						$newMenu[$key]['sub'][] = isset($user->permissions[$sub['permission']]) ? $sub : null;
					}
					$newMenu[$key]['sub'] = array_values(array_filter($newMenu[$key]['sub']));
					if (isset($newMenu[$key]['sub']))
					{
						$newMenu[$key]['title'] = $item['title'];
						$newMenu[$key]['icon'] = $item['icon'];
						$newMenu[$key]['link'] = $item['link'];
					}

				} else {
					$newMenu[$key] = isset($user->permissions[$item['permission']]) ? $item : null;
				}

				if (empty($newMenu[$key]['sub']) && empty($newMenu[$key]['permission']) )
					$newMenu[$key] = null;
			}

			return array_values(array_filter($newMenu));
		}
	}





	
	/**
	 * Return Superadmin menu
	 * List of side menu
	 */
	public function sideMenu()
	{
		$settings = $this->SystemSetting();

		$data = array(
			

			array('title'=>translate('Home'),  'icon'=>'home', 'link'=>'/'),

			!empty($settings['enable_audio']) ? array('title'=>translate('Discover'),  'icon'=>'cloud-bolt', 'link'=>'/discover') : null,

			!empty($settings['enable_videos']) ? array('title'=>translate('Videos'),  'icon'=>'video-play', 'link'=>'/discover/video') : null,

			!empty($settings['enable_iframe']) ? array('title'=>translate('Iframe'),  'icon'=>'link', 'link'=>'/discover/iframe') : null,

			!empty($settings['enable_short_videos']) ? array('title'=>translate('Short Videos'),  'icon'=>'videos', 'link'=>'/discover/short') : null,


			!empty($settings['enable_audiobooks']) ? array('title'=>translate('Audiobooks'),  'icon'=>'audiobook-tab', 'link'=>'/discover/audiobook') : null,

			!empty($settings['enable_stations']) ? array('title'=>translate('Stations'),  'icon'=>'station', 'link'=>'/stations') : null,

			!empty($settings['enable_channels']) ? array('title'=>translate('Channels'),  'icon'=>'channel', 'link'=>'/channels') : null,

			!empty($settings['enable_playlist']) ? array('title'=>translate('Playlists'),  'icon'=>'playlist', 'link'=>'/playlists') : null,
			
			!empty($settings['enable_audio']) ? array('title'=>translate('Genres'),  'icon'=>'audio-tab', 'link'=>'/genres') : null,
			
			array('title'=>translate('Artists'),  'icon'=>'user-tab', 'link'=>'/artists'),
			
			array('title'=>translate('Blog'),  'icon'=>'blog', 'link'=>'/blog'),

		);

		return array_filter($data);
	}
	

	/**
	 * Return Search menu
	 * List of search pages
	 */
	public function searchMenu()
	{
		$settings = $this->SystemSetting();

		$data = array(
			

			!empty($settings['enable_audio']) ? array('title'=>translate('Audio'),  'icon'=>'audio-tab', 'link'=>'/search/audio') : null,

			!empty($settings['enable_videos']) ? array('title'=>translate('Videos'),  'icon'=>'video-play', 'link'=>'/search/video') : null,

			!empty($settings['enable_short_videos']) ? array('title'=>translate('Shorts'),  'icon'=>'videos', 'link'=>'/search/short') : null,

			!empty($settings['enable_audiobooks']) ? array('title'=>translate('Audiobooks'),  'icon'=>'audiobook-tab', 'link'=>'/search/audiobook') : null,

			!empty($settings['enable_stations']) ? array('title'=>translate('Stations'),  'icon'=>'station', 'link'=>'/search/station') : null,

			!empty($settings['enable_channels']) ? array('title'=>translate('Channels'),  'icon'=>'channel', 'link'=>'/search/channel') : null,

			!empty($settings['enable_playlist']) ? array('title'=>translate('Playlists'),  'icon'=>'playlist-tab', 'link'=>'/search/playlist') : null,
			

		);

		return array_filter($data);
	}
	

	/**
	 * Return Studio menu
	 * List of Artist studio pages
	 */
	public function studioMenu()
	{
		$settings = $this->SystemSetting();

		$data = array(
			
			array('title'=>translate('Studio'),  'link'=>'/studio'),

			!empty($settings['enable_audio']) ? array('title'=>translate('Audio'),   'link'=>'/studio/audio') : null,

			!empty($settings['enable_videos']) ? array('title'=>translate('Videos'),  'link'=>'/studio/videos') : null,

			!empty($settings['enable_short_videos']) ? array('title'=>translate('Shorts'),  'link'=>'/studio/shorts') : null,

			!empty($settings['enable_audiobooks']) ? array('title'=>translate('Audiobooks'),  'link'=>'/studio/audiobooks') : null,

			!empty($settings['enable_stations']) ? array('title'=>translate('Stations'), 'link'=>'/studio/stations') : null,

			!empty($settings['enable_channels']) ? array('title'=>translate('Channels'), 'link'=>'/studio/channels') : null,

			!empty($settings['enable_playlist']) ? array('title'=>translate('Playlists'), 'link'=>'/studio/playlists') : null,
			
			array('title'=>translate('Settings'),  'link'=>'/studio/profile'),

			array('title'=>translate('Information'),  'link'=>'/studio/information'),

			array('title'=>translate('Invoices'),  'link'=>'/studio/invoices'),

			array('title'=>translate('Subscriptions'),  'link'=>'/studio/subscriptions'),

			array('title'=>translate('Profile page'),  'link'=>'/artist/'.$this->customer_id()),


		);

		return array_filter($data);
	}


	public function getPercentageBetweenDates($to, $from)
	{
		return getPercentageBetweenDates($to, $from);
	}
}