<?php

namespace Shared\Plugins\Blocks;

use Medians\Packages\Infrastructure\PackageRepository;
use Medians\Hooks\Infrastructure\HookRepository;
use Medians\CustomFields\Domain\CustomField;
use Medians\Hooks\Domain\Hook;


class Pricing 
{

	
    private $packageRepo;
    private $hookRepo;
    public $name = "Pricing";
    public $description = "";
    public $version = "1.0";
    public $shortcode = "";
	

	function __construct()
	{
		$this->packageRepo = new PackageRepository;
		$this->hookRepo = new HookRepository;
	}


	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
			'info'=> [	
				[ 'key'=> "intro_text", 'title'=> translate('Intro text') , 'help_text'=> translate('Text to show before the table'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "packages", 'title'=> translate('Packages'), 'help_text'=> translate('Select packages to display at table'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'name', 'column_key'=>'package_id', 'multiple' => true,
					'data' => $this->packageRepo->getActive()  
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
		return render('', [
		        'load_vue' => true,
		        'fillable' => $this->fillable(),
	    ]);
	} 


    /**
     * Update Lead
     */
    public function update($data, $Object)
    {
		
		$clear = CustomField::where('model_id', $Object->id)->where('model_type', Hook::class)->delete();

		if ($data) {
			
			foreach ($data as $key => $value)
			{
				$fields = [];
				$fields['model_id'] = $Object->id;	
				$fields['model_type'] = Hook::class;	
				$fields['code'] = $key;
				$fields['title'] = '';
				$fields['value'] = (is_array($value) || is_object($value)) ? json_encode($value) : $value;

				$Model = CustomField::firstOrCreate($fields);
			}
	
			return $Model ?? '';		
		}

    	return $Object;

    } 

	/**
	 * Customers index page
	 * 
	 */ 
	public function view($params ) 
	{

		try {

			$hook = $this->hookRepo->find($params['id']);

            return renderPlugin('Shared/Plugins/views/pricing.html.twig', [
				'hook' => $hook,
				'packages' => $this->packageRepo->getActive(),
		    ],'output');

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}
	
}
