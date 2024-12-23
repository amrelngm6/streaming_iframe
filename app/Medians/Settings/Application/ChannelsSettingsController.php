<?php

namespace Medians\Settings\Application;
use \Shared\dbaser\CustomController;

use Medians\Settings\Infrastructure\SystemSettingsRepository;

class ChannelsSettingsController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;

	protected $app;


	function __construct()
	{
		
		$this->app = new \config\APP;

		$this->repo = new SystemSettingsRepository();
	}

	
	

	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
			'basic'=> [	
				[ 'key'=> "enable_channels", 'title'=> translate('Allow channels feature'), 'help_text'=>translate('You can allow / disallow channels at Frontend'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "view_channels_limit", 'title'=> translate('Loading channels limit'), 'help_text'=>translate('Show x channels as first load at channels page'), 'fillable'=> true, 'column_type'=>'number' ],
                
			],		
			'streaming'=> [	
				[ 'key'=> "autoplay_channel", 'title'=> translate('Autoplay at channel page'), 'help_text'=>translate('Auto play channel at single channel page'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "channel_direct_streaming", 'title'=> translate('Streaming Channels directly'), 'help_text'=>translate('Allow customers to stream Channels files through direct links. ( Recommended disable to prevent download media files anonymously )'), 'fillable'=> true, 'column_type'=>'checkbox' ],
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
			'setting' => (new SystemSettingsController())->getAll(),
			'fillable' => $this->fillable(),
			'title' => translate('Channels Settings'),
	    ]);
	} 

}
