<?php

namespace Shared\Plugins\Sliders;

use Medians\Customers\Infrastructure\CustomerRepository;
use Medians\Hooks\Infrastructure\HookRepository;
use Medians\CustomFields\Domain\CustomField;
use Medians\Hooks\Domain\Hook;


class ArtistCarousel 
{

	
    private $artistRepo;
    private $hookRepo;
    public $name = "Artist carousel";
    public $description = "";
    public $version = "1.0";
    public $shortcode = "";
	

	function __construct()
	{
		$this->artistRepo = new CustomerRepository;
		$this->hookRepo = new HookRepository;
	}


	/**
	 * Columns list to view at DataTable 
	 *  
	 */ 
	public function fillable( ) 
	{

		return [
            
			'basic'=> [	
				[ 'key'=> "artists", 'title'=> translate('Artists'), 'help_text'=> translate('Select artists to display'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'name', 'column_key'=>'customer_id', 'multiple' => true,
					'data' => $this->artistRepo->getActive()  
				],	
				[ 'key'=> "show_followers", 'title'=> translate('Show followers count') , 'help_text'=> translate('Show followers count of the artist'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "show_audio_items", 'title'=> translate('Show audio count') , 'help_text'=> translate('Show audio uploads count of the artist'), 'fillable'=> true, 'column_type'=>'checkbox' ],

			],	
            
			'styles'=> [	
				[ 'key'=> "container_style", 'title'=> translate('Container style'), 'help_text'=> translate('Select style of the Container'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'name', 'column_key'=>'container_style', 
					'data' => [["name"=> "Boxed", "container_style"=>"container"], ["name"=>"Full width", "container_style"=> "w-full"]]  
				],
				[ 'key'=> "mobile_view_limit", 'title'=> translate('Mobile view items limit') , 'help_text'=> translate('Max number of products to view at the slider wrapper for Mobile view'), 'fillable'=> true, 'required'=> true, 'column_type'=>'number' ],
				[ 'key'=> "tablet_view_limit", 'title'=> translate('Tablet view items limit') , 'help_text'=> translate('Max number of products to view at the slider wrapper for Tablet view'), 'fillable'=> true, 'required'=> true, 'column_type'=>'number' ],
				[ 'key'=> "desktop_view_limit", 'title'=> translate('Desktop view items limit') , 'help_text'=> translate('Max number of products to view at the slider wrapper for desktop view'), 'fillable'=> true, 'required'=> true, 'column_type'=>'number' ],
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

			$params['artists'] = json_decode($hook->field['artists']);

			$items = $this->artistRepo->getByIds($params['artists']);

            return renderPlugin('Shared/Plugins/views/artists_carousel.html.twig', [
		        'artists' => $items,
				'hook' => $hook
		    ],'output');

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}
	
}
