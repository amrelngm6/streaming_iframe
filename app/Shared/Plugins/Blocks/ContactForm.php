<?php

namespace Shared\Plugins\Blocks;

use Medians\Customers\Infrastructure\CustomerRepository;
use Medians\Hooks\Infrastructure\HookRepository;
use Medians\CustomFields\Domain\CustomField;
use Medians\Hooks\Domain\Hook;


class ContactForm 
{

	
    private $artistRepo;
    private $hookRepo;
    public $name = "Contact Form";
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
				[ 'key'=> "show_name", 'title'=> translate('Show Name field') , 'help_text'=> translate('Show / hide Name field at the form'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "show_email", 'title'=> translate('Show Email field') , 'help_text'=> translate('Show / hide Email field at the form'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "show_phone", 'title'=> translate('Show Phone field') , 'help_text'=> translate('Show / hide Phone field at the form'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "show_message", 'title'=> translate('Show Message field') , 'help_text'=> translate('Show / hide Message field at the form'), 'fillable'=> true, 'column_type'=>'checkbox' ],
			],	
            
			'info'=> [	
				[ 'key'=> "address", 'title'=> translate('Contact Address') , 'help_text'=> translate('Your contact Address info to show at the side'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "email", 'title'=> translate('Contact Email') , 'help_text'=> translate('Your contact Email info to show at the side'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "phone", 'title'=> translate('Contact Phone') , 'help_text'=> translate('Your contact Phone field to show at the side'), 'fillable'=> true, 'column_type'=>'text' ],
				[ 'key'=> "form_text", 'title'=> translate('Form intro text') , 'help_text'=> translate('Text to show before the form'), 'fillable'=> true, 'column_type'=>'text' ],
			],	
            
            
			'map'=> [	
				[ 'key'=> "show_map_iframe", 'title'=> translate('Show Name field') , 'help_text'=> translate('Show / hide Map IFrame at the page'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "map_url", 'title'=> translate('Map URL') , 'help_text'=> translate('Your location URL for Google Map Iframe ( only IFrame src )'), 'fillable'=> true, 'column_type'=>'text' ],
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

            return renderPlugin('Shared/Plugins/views/contact-form.html.twig', [
				'hook' => $hook
		    ],'output');

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}
	
}
