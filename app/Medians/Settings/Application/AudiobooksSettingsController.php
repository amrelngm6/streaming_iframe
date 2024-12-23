<?php

namespace Medians\Settings\Application;
use \Shared\dbaser\CustomController;

use Medians\Settings\Infrastructure\SystemSettingsRepository;

class AudiobooksSettingsController extends CustomController
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
				[ 'key'=> "enable_audiobooks", 'title'=> translate('Allow audiobooks feature'), 'help_text'=>translate('You can allow / disallow with Audiobooks at Frontend'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "view_audiobooks_limit", 'title'=> translate('Loading Audiobooks limit'), 'help_text'=>translate('Show x Audiobooks as first load at frontend pages'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "audiobook_max_size", 'title'=> translate('Audiobook max size'), 'help_text'=>translate('Max size for uploaded Audiobooks'), 'fillable'=> true, 'column_type'=>'number' ],
				[ 'key'=> "audiobook_max_chapters", 'title'=> translate('Audio max chapters'), 'help_text'=>translate('Max chapters count for uploaded audiobooks'), 'fillable'=> true, 'column_type'=>'number' ],
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
			'title' => translate('Audiobooks Settings'),
	    ]);
	} 

}
