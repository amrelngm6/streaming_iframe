<?php

namespace Medians\Settings\Application;
use \Shared\dbaser\CustomController;

use Medians\Settings\Infrastructure\SystemSettingsRepository;
use Medians\Templates\Infrastructure\WebTemplateRepository;


class StreamingSettingsController extends CustomController
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
				[ 'key'=> "pictures_allowed_ext", 'title'=> translate('Allowed Pictures Extensions'), 'help_text'=>translate('Allow extnsions of Pictures files to upload. Separate by (,) without spaces )'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "ffmpeg_path", 'title'=> translate('FFMPEG Path'), 'help_text'=>translate('Use (ffmpeg) if already installed or add Full path ex: ').$_SERVER['DOCUMENT_ROOT'].'/ffmpeg', 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "default_storage", 'title'=> translate('Default Storage'), 'help_text'=> translate('The default storage location for media files'),
                    'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'title', 'column_key' => 'default_storage',
                    'data' => [['default_storage'=>'google','title'=>translate('Google Storage')], ['default_storage'=>'local','title'=>translate('Local Server')]]  
                ],	
			],		

            'streaming'=> [	
				[ 'key'=> "direct_link_streaming", 'title'=> translate('Streaming links directly'), 'help_text'=>translate('Allow customers to stream the media files through direct files. ( Recommended disable to prevent download media files anonymously )'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "station_media_chunk", 'title'=> translate('Station Streaming Chunk limit'), 'help_text'=>translate('Limit in seconds to chunk the audio files for streaming at the Player'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "station_interval", 'title'=> translate('Station Streaming Interval'), 'help_text'=>translate('Time in seconds to Check for the active audio files for stations streaming, This option recommeded to be accurated with streaming media duration average'), 'fillable'=> true, 'column_type'=>'number' ],
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
			'title' => translate('Storage Settings'),
	    ]);
	} 



	public function getItem($code = null) 
	{	
		return $this->repo->getByCode($code);
	}


	public function loadFonts() 
	{	

		return [
			['title'=>'Tajawal'],
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
