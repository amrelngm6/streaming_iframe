<?php

namespace Medians\Settings\Application;
use \Shared\dbaser\CustomController;

use Medians\Settings\Infrastructure\SystemSettingsRepository;

class VideosSettingsController extends CustomController
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
				[ 'key'=> "enable_videos", 'title'=> translate('Allow videos feature'), 'help_text'=>translate('You can allow / disallow with Videos at Frontend'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "view_videos_limit", 'title'=> translate('Loading videos limit'), 'help_text'=>translate('Show x videos as first load at videos page'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "video_max_size", 'title'=> translate('Video max size'), 'help_text'=>translate('Max size for uploaded Videos'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "video_allowed_ext", 'title'=> translate('Allowed Videos Extensions'), 'help_text'=>translate('Allow extnsions of Video files to upload. Separate by (,) without spaces )'), 'fillable'=> true, 'column_type'=>'text' ],
										
			],		
			'streaming'=> [	
				[ 'key'=> "autoplay_video", 'title'=> translate('Autoplay at single page'), 'help_text'=>translate('Auto play videos at video page'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "video_direct_streaming", 'title'=> translate('Streaming Videos directly'), 'help_text'=>translate('Allow customers to stream Video files through direct links. ( Recommended disable to prevent download media files anonymously )'), 'fillable'=> true, 'column_type'=>'checkbox' ],
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
			'title' => translate('Video Settings'),
	    ]);
	} 

}
