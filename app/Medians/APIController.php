<?php

namespace Medians;

use \Shared\dbaser\CustomController;

use Medians\Users\Infrastructure\UserRepository;

use Medians\Bugs\Infrastructure\BugReportRepository;

use Medians\Blog\Infrastructure\BlogRepository;


class APIController extends CustomController
{

	/**
	* @var Object
	*/
	protected $repo;
	protected $app;

	protected $BugReportRepo;



	function __construct()
	{
	
	}


	/**
	 * Model object 
	 * 
	 */
	public function handle($path)
	{

		$this->app = new \config\APP;
		$request = $this->app->request();
		$return = [];
		switch ($path) 
		{
			case 'load_config':
				$return = loadConfig($request->get('component'), []);
				break;
		}

		echo json_encode($return);
	} 

	
	/**
	 * Create model 
	 * 
	 */
	public function create()
	{
		
		$app = new \config\APP;
		$request = $app->request();
		
		try {
				
			$return = [];
			switch ($request->get('type')) 
			{

				case 'Payment.paypal_payment_confirmation':
					$return = (new Payments\Application\PaymentController())->confirmPayPalPlanPayment();
					break;

				case 'User.create':
					$return = (new Users\Application\UserController())->store();
					break;

	            case 'NotificationEvent.create':
	                $return =  (new Notifications\Application\NotificationEventController())->store(); 
	                break;

	            case 'Role.create':
	                $return =  (new Roles\Application\RoleController())->store(); 
	                break;
				
				
				case 'Page.create':
					$return =  (new Pages\Application\PageController())->store(); 
					break;

				case 'Translation.create':
					$return = (new Languages\Application\TranslationController)->store();
					break;

				case 'Language.create':
					$return = (new Languages\Application\LanguageController)->store();
					break;
		
		
				case 'PaymentMethod.create':
					$return = (new PaymentMethods\Application\PaymentMethodController)->store();
					break;
		
				case 'Genre.create':
					$return = (new Categories\Application\GenreController)->store();
					break;
		
				case 'BookGenre.create':
					$return = (new Categories\Application\BookGenreController)->store();
					break;
					
				case 'VideoGenre.create':
					$return = (new Categories\Application\VideoGenreController)->store();
					break;
					
				case 'Category.create':
					$return = (new Categories\Application\GenreController)->store();
					break;
					
				case 'Mood.create':
					$return = (new Categories\Application\MoodController)->store();
					break;
					
				case 'Newsletter.create':
					$return = (new Newsletters\Application\NewsletterController)->store();
					break;
					
				case 'Subscriber.create':
					$return = (new Newsletters\Application\SubscriberController)->store();
					break;
					
				case 'Customer.create':
					$return = (new Customers\Application\CustomerController)->store();
					break;
					
				case 'WebTemplate.create':
					$return = (new Templates\Application\WebTemplateController)->store();
					break;
					
				case 'Gallery.create':
					$return = (new Gallery\Application\GalleryController)->store();
					break;
					
				case 'Hook.create':
					$return = (new Hooks\Application\HookController)->store();
					break;
					
				case 'Blog.create':
					$return = (new Blog\Application\BlogController)->store();
					break;

				case 'EmailTemplate.create':
					$return = (new Templates\Application\EmailTemplateController)->store();
					break;
					
				case 'Package.create':
					$return = (new Packages\Application\PackageController)->store();
					break;
					
		
			}

			return response(json_encode($return));

		} catch (Exception $e) {
			return $e->getMessage();
		}
	} 

	/**
	 * Update model 
	 * 
	 */
	public function update()
	{
		$app = new \config\APP;
		$request = $app->request();

		switch ($request->get('type')) 
		{
			case 'SystemSettings.update':
                $controller =  new Settings\Application\SystemSettingsController; 
				break;
				
            case 'Settings.update':
                $controller = new Settings\Application\SettingsController; 
                break;

            case 'User.update':
                $controller = new Users\Application\UserController; 
                break;

            case 'NotificationEvent.update':
                $controller =  new Notifications\Application\NotificationEventController; 
                break;

			case 'Role.update':
				$controller =  new Roles\Application\RoleController; 
				break;			

			case 'Page.update':
				$controller =  new Pages\Application\PageController; 
				break;
			
			case 'Role.updatePermissions':
				return (new Roles\Application\RoleController)->updatePermissions(); 
				break;
		
			case 'User.updateStatus':
				return (new Users\Application\UserController())->updateStatus();
				break;
	
			case 'Genre.update':
				$controller = new Categories\Application\GenreController;
				break;

			case 'BookGenre.update':
				$controller = new Categories\Application\BookGenreController;
				break;

			case 'VideoGenre.update':
				$controller = new Categories\Application\VideoGenreController;
				break;

			case 'Mood.update':
				$controller = new Categories\Application\MoodController;
				break;
			
			case 'Newsletter.update':
				$controller = new Newsletters\Application\NewsletterController;
				break;
			
			case 'Subscriber.update':
				$controller = new Newsletters\Application\SubscriberController;
				break;
			
			case 'Customer.update':
				$controller = new Customers\Application\CustomerController;
				break;
			
			case 'Language.update':
				$controller = new Languages\Application\LanguageController;
				break;
				
			case 'Translation.update':
				$controller = new Languages\Application\TranslationController; 
				break;
			
			case 'Gallery.update':
				$controller = new Gallery\Application\GalleryController; 
				break;
			
			case 'Menu.update':
				$controller = new Menus\Application\MenuController; 
				break;
	
			case 'EmailTemplate.update':
				$controller = new Templates\Application\EmailTemplateController; 
				break;

			case 'WebTemplate.update':
				$controller = new Templates\Application\WebTemplateController; 
				break;

			case 'Plugin.update':
				$controller = new Plugins\Application\PluginController; 
				break;

			case 'Hook.update':
				$controller = new Hooks\Application\HookController; 
				break;
			
			case 'Blog.update':
				$controller = new Blog\Application\BlogController; 
				break;
			
			case 'EmailTemplate.update':
				$controller = new Templates\Application\EmailTemplateController; 
				break;
			
			case 'Package.update':
				$controller = new Packages\Application\PackageController; 
				break;
			
			case 'PackageSubscription.update':
				$controller = new Packages\Application\PackageSubscriptionController; 
				break;
			
		}

		return response(isset($controller) ? json_encode($controller->update()) : []);
	} 

	

	/**
	 * delete model 
	 * 
	 */
	public function delete()
	{

		$app = new \config\APP;
		$request = $app->request();

		try {
			
			$return = [];
			switch ($request->get('type')) 
			{

				case 'User.delete':
					return response((new Users\Application\UserController())->delete());
					break;

				case 'Role.delete':
					return response((new Roles\Application\RoleController())->delete());
					break;

				case 'NotificationEvent.delete':
					return response((new Notifications\Application\NotificationEventController())->delete());
					break;
			
				case 'Page.delete':
					return response((new Pages\Application\PageController())->delete());
					break;
		
				case 'City.delete':
					return response((new Locations\Application\CityController())->delete());
					break;

				case 'PaymentMethod.delete':
					return response((new PaymentMethods\Application\PaymentMethodController())->delete());
					break;
			
				case 'Newsletter.delete':
					return response((new Newsletters\Application\NewsletterController())->delete());
					break;
			
				case 'Subscriber.delete':
					return response((new Newsletters\Application\SubscriberController())->delete());
					break;
			
				case 'Customer.delete':
					return response((new Customers\Application\CustomerController())->delete());
					break;
			
				case 'WebTemplate.delete':
					return response((new Templates\Application\WebTemplateController())->delete());
					break;
			
				case 'Hook.delete':
					return response((new Hooks\Application\HookController())->delete());
					break;
			
				case 'Blog.delete':
					return response((new Blog\Application\BlogController())->delete());
					break;
			
				case 'MediaItem.delete':
					return response((new Media\Application\MediaItemController())->delete());
					break;
			
				case 'EmailTemplate.delete':
					return response((new Templates\Application\EmailTemplateController())->delete());
					break;
			
				case 'Genre.delete':
					return response((new Categories\Application\GenreController())->delete());
					break;
			
				case 'BookGenre.delete':
					return response((new Categories\Application\BookGenreController())->delete());
					break;

				case 'VideoGenre.delete':
					return response((new Categories\Application\VideoGenreController())->delete());
					break;
					
				case 'Package.delete':
					return response((new Packages\Application\PackageController())->delete());
					break;
			
			}

		} catch (Exception $e) {
			throw new Exception("Error Processing Request", 1);
					
		}
	} 


	/**
	 * Debug function that takes screenshot 
	 * and save at the server with txt file 
	 * with full log
	 */
	public function bug_report()
	{
		$this->app = new \config\APP;

		$info = $_POST['info'];
		$err = $_POST['err'];
		$root_path_info = pathinfo(dirname(__DIR__));
		$output = date('YmdHis').'-'.$this->app->auth()->id.'-'.$info;
		file_put_contents($root_path_info['dirname'].'/uploads/bugs/'.$output.'.jpg', file_get_contents($_POST['image']));

		$txtLog = $root_path_info['dirname'].'/uploads/bugs/error_bugs.txt'; 
		file_put_contents($txtLog, file_get_contents($txtLog)."\r\n".$output . $err);

		$this->BugReportRepo = new BugReportRepository;

		$data = array();
		$data['user_id'] = $this->app->auth()->id;
		$data['file_name'] = $output.'.jpg';
		$data['info'] =  $info;
		$data['error'] =  $err;
		$this->BugReportRepo->store($data);

	}

}
