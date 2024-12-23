<?php

namespace Medians\Settings\Application;
use \Shared\dbaser\CustomController;

use Medians\Settings\Infrastructure\SystemSettingsRepository;

class AudioSettingsController extends CustomController
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
				[ 'key'=> "enable_audio", 'title'=> translate('Allow audio feature'), 'help_text'=>translate('You can allow / disallow with Audio at Frontend'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "view_audio_limit", 'title'=> translate('Loading Audio limit'), 'help_text'=>translate('Show x Audio as first load at frontend pages'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "audio_max_size", 'title'=> translate('Audio max size'), 'help_text'=>translate('Max size for uploaded audio files'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "audio_allowed_ext", 'title'=> translate('Allowed Audio Extensions'), 'help_text'=>translate('Allow extnsions of Audio files to upload. Separate by (,) without spaces )'), 'fillable'=> true, 'column_type'=>'text' ],
                
			],		
			'streaming'=> [	
				[ 'key'=> "audio_direct_streaming", 'title'=> translate('Streaming audio directly'), 'help_text'=>translate('Allow customers to stream audio files through direct links. ( Recommended disable to prevent download media files anonymously )'), 'fillable'=> true, 'column_type'=>'checkbox' ],
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
			'title' => translate('Audio Settings'),
	    ]);
	} 

}
