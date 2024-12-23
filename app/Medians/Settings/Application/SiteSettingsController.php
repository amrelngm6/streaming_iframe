<?php

namespace Medians\Settings\Application;
use \Shared\dbaser\CustomController;

use Medians\Settings\Infrastructure\SystemSettingsRepository;
use Medians\Templates\Infrastructure\WebTemplateRepository;


class SiteSettingsController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;

	protected $templateRepo;


	function __construct()
	{
		
		$this->app = new \config\APP;

		$this->repo = new SystemSettingsRepository();

		$this->templateRepo = new WebTemplateRepository();
	}

	
	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
            
			'basic'=> [	
				[ 'key'=> "is_dark", 'title'=> translate('Dark mode'), 'help_text'=>translate('Allow dark mode'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "sitename", 'title'=> translate('sitename'), 'fillable'=> true, 'required'=> true, 'column_type'=>'text' ],
				[ 'key'=> "lang", 'title'=> translate('Languange'), 'help_text'=> translate('The default language for new sessions'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title', 
					'data' => [['lang'=>'arabic','title'=>translate('Arabic')], ['lang'=>'english','title'=>translate('English')]
				]  
			],	
			[ 'key'=> "template", 'title'=> translate('Template'), 'help_text'=> translate('The default template for frontend'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title', 'column_key'=>'folder_name',
					'data' => $this->templateRepo->get()  
				],	
			],			
			'Logo'=> [	
				[ 'key'=> "logo", 'title'=> translate('logo'), 'fillable'=>true, 'column_type'=>'file' ],
	            [ 'key'=> "dark_logo", 'title'=> translate('Dark logo'), 'fillable'=>true, 'column_type'=>'file' ],
	            [ 'key'=> "backend_logo", 'title'=> translate('Dashboard Logo'), 'fillable'=>true, 'column_type'=>'file' ],
			],		
			
			'layout_options'=> [	

				[ 'key'=> "view_artists_limit", 'title'=> translate('Loading artists limit'), 'help_text'=>translate('Show x items as first load at Artists page'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "view_articles_limit", 'title'=> translate('Loading articles limit'), 'help_text'=>translate('Show x articles as first load at Blog page'), 'fillable'=> true, 'column_type'=>'number' ],
			],	
			
			'site_info'=> [	
				[ 'key'=> "footer_email", 'title'=> translate('Email'), 'help_text'=>translate('This email used for view at your frontend footer'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "footer_address", 'title'=> translate('Footer address'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "footer_phone", 'title'=> translate('Footer phone'), 'fillable'=> true, 'column_type'=>'phone' ],
			],
			
			
			'Iframes'=> [	
				
				[ 'key'=> "enable_iframe", 'title'=> translate('Allow Iframe feature'), 'help_text'=>translate('You can allow / disallow with Iframes loading at Frontend'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "view_iframe_limit", 'title'=> translate('Loading Iframes limit'), 'help_text'=>translate('Show x Iframes as first load at frontend pages'), 'fillable'=> true, 'column_type'=>'number' ],

			],
			
			// 'social_media'=> [	
			// 	[ 'key'=> "facebook_link", 'title'=> translate('Facebook link'), 'help_text'=>translate('This links used for view at your frontend footer'), 'fillable'=> true, 'column_type'=>'text' ],
			// 	[ 'key'=> "twitter_link", 'title'=> translate('Twitter link'), 'fillable'=> true, 'column_type'=>'text' ],
			// 	[ 'key'=> "youtube_link", 'title'=> translate('YouTube link'), 'fillable'=> true, 'column_type'=>'text' ],
			// 	[ 'key'=> "instagram_link", 'title'=> translate('Instagram link'), 'fillable'=> true, 'column_type'=>'text' ],
			// ],
			'cookies'=> [	
				[ 'key'=> "show_cookie_box", 'title'=> translate('Show Cookies Box'), 'help_text'=>translate('Show cookies box at the bottom of the page'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "cookie_text", 'title'=> translate('Cookies text'), 'help_text'=> translate('Text of the cookies box'), 'fillable'=> true, 'required'=> true, 'column_type'=>'text' ],
				[ 'key'=> "cookie_button", 'title'=> translate('Cookies button'), 'help_text'=> translate('Button Text of the cookies box'), 'fillable'=> true, 'required'=> false, 'column_type'=>'text' ],

			],
			'fonts'=> [	
				
				[ 'key'=> "english_headers_font", 'title'=> translate('English Headers font'),  'help_text' => translate('Choose the font style for  Headlines H1, H2,... elements in English sessions'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title' ,'column_key'=>'title', 
					'data' => $this->loadFonts()  
				],
				[ 'key'=> "english_text_font", 'title'=> translate('English text font'),  'help_text' => translate('Choose the font style for  Headlines H1, H2,... elements in English sessions'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title' ,'column_key'=>'title', 
					'data' => $this->loadFonts()  
				],

				[ 'key'=> "arabic_headers_font", 'title'=> translate('Arabic Headers font'),  'help_text' => translate('Choose the font style for Headlines H1, H2,... elements for Arabic sessions'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title' ,'column_key'=>'title', 
					'data' => $this->loadFonts()  
				],
				[ 'key'=> "arabic_text_font", 'title'=> translate('Arabic text font'),  'help_text' => translate('Choose the font style for Headlines H1, H2,... elements for Arabic sessions'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title' ,'column_key'=>'title', 
					'data' => $this->loadFonts()  
				],
			],
					
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
			'title' => translate('Frontend Settings'),
	    ]);
	} 



	public function getItem($code = null) 
	{	
		return $this->repo->getByCode($code);
	}


	public function loadFonts() 
	{	

		return [
			['title'=>'balooregular'],
			['title'=>'Amaranth'],
			['title'=>'Montserrat'],
			['title'=>'Tajawal'],
			['title'=>'Rubik'],
			['title'=>'Cairo'],
			['title'=>'Roboto'],
		];
	}


	public function getAll() 
	{	

		return (new SystemSettingsController())->getAll();
	}


	/**
	* Return the Settings
	*/
	public function update() 
	{
		return (new SystemSettingsController())->update();
	}



	/**
	* Return the Settings
	*/
	public function updateSettings($params) 
	{
		return (new SystemSettingsController())->updateSettings($params);
	}




	public function saveItem($code, $value) 
	{
		return (new SystemSettingsController())->saveItem($code, $value);
	}


	public function saveItemArray($code, $value) 
	{
		return (new SystemSettingsController())->saveItemArray($code, $value);
	}


	public function deleteItem($code) 
	{
		return (new SystemSettingsController())->deleteItem($code);
	}


}
