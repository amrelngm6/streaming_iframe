<?php

namespace Shared\Plugins\Sliders;

use Medians\Gallery\Infrastructure\GalleryRepository;
use Medians\Hooks\Infrastructure\HookRepository;
use Medians\CustomFields\Domain\CustomField;
use Medians\Hooks\Domain\Hook;

class HeroSlider 
{

    protected $hookRepo;
    protected $galleryRepo;

    public $name = "Hero Slider";
    public $description = "";
    public $version = "1.0";
    public $shortcode = "";


	function __construct()
	{
		$this->galleryRepo = new GalleryRepository;
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
				// [ 'key'=> "products_limit", 'title'=> translate('Max number') , 'help_text'=> translate('Max number of loaded products'), 'fillable'=> true, 'required'=> true, 'column_type'=>'number' ],
				[ 'key'=> "gallery_id", 'title'=> translate('Gallery'), 'help_text'=> translate('Select gallery to display slides from'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'name', 'column_key'=>'gallery_id',  'multiple' => true, 'single' => true,
					'data' => $this->galleryRepo->get()  
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

			$params['gallery_id'] = json_decode($hook->field['gallery_id']);

			// print_r($params);
			$gallery = $this->galleryRepo->find($params['gallery_id'][0]);
			// print_r($gallery);
            return renderPlugin('Shared/Plugins/views/home_slider.html.twig', [
		        'gallery' => $gallery ?? '',
				'hook' => $hook
		    ],'output');

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}
	

}
