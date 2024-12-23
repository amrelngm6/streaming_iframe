<?php

namespace Shared\Plugins\Blog;

use Medians\Blog\Infrastructure\BlogRepository;
use Medians\Hooks\Infrastructure\HookRepository;
use Medians\CustomFields\Domain\CustomField;
use Medians\Hooks\Domain\Hook;


class BlogList 
{

	
    private $repo;
    private $hookRepo;
    public $name = "Blog List";
    public $description = "";
    public $version = "1.0";
    public $shortcode = "";
	

	function __construct()
	{
		$this->repo = new BlogRepository;
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
				[ 'key'=> "limit", 'title'=> translate('Page limit') , 'help_text'=> translate('Max number of articles to view at the page'), 'fillable'=> true, 'required'=> true, 'column_type'=>'number' ],
				[ 'key'=> "show_title", 'title'=> translate('Show title') , 'help_text'=> translate('Show title of the Hook'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				
			],	
            
			'styles'=> [	
				[ 'key'=> "container_style", 'title'=> translate('Container style'), 'help_text'=> translate('Select style of the Container'),
					'sortable'=> true, 'fillable'=> true, 'column_type'=>'select','text_key'=>'name', 'column_key'=>'container_style', 
					'data' => [["name"=> "Boxed", "container_style"=>"container"], ["name"=>"Full width", "container_style"=> "w-full"]]  
				],
				[ 'key'=> "show_date", 'title'=> translate('Show Article Date') , 'help_text'=> translate('Show date of the article'), 'fillable'=> true, 'column_type'=>'checkbox' ],
				[ 'key'=> "show_views", 'title'=> translate('Show Article views') , 'help_text'=> translate('Show views count of the article'), 'fillable'=> true, 'column_type'=>'checkbox' ],
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
			$items = $this->repo->paginate($hook->field['limit']);

            return renderPlugin('Shared/Plugins/views/blog.html.twig', [
		        'items' => $items,
				'hook' => $hook
		    ],'output');

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
		}
	}
	
}
