<?php

namespace Medians;

use \Shared\dbaser\CustomController;

use Medians\Views\Domain\View;
use Medians\Likes\Domain\Like;
use Medians\Comments\Domain\Comment;

class DashboardController extends CustomController
{

	/**
	* @var Object
	*/
	public  $contentRepo;
	public  $CustomerRepository;

	protected $app;
	public $start;
	public $end;
	public $month_beginning;
	

	function __construct()
	{
		$this->app = new \config\APP;
		$user = $this->app->auth();

		$this->contentRepo = new Content\Infrastructure\ContentRepository();
		$this->CustomerRepository = new Customers\Infrastructure\CustomerRepository();

		
		$setting = $this->app->SystemSetting();
		$defaultStart = isset($setting['default_dashboard_start_date']) ? date('Y-'. $setting['default_dashboard_start_date']) : date('Y-m-d');
		$this->start = $this->app->request()->get('start_date') ? date('Y-m-d', strtotime($this->app->request()->get('start_date'))) : $defaultStart;
		$this->end = $this->app->request()->get('end_date') ? date('Y-m-d', strtotime($this->app->request()->get('end_date'))) : date('Y-m-d');
		$this->end = date('Y-m-d', strtotime($this->end. ' + 1 days'));
		$this->month_beginning = date('Y-m-01', strtotime($this->end));
	}

	/**
	 * Load dashboard page
	 * 
	 * @return response for Vue  
	 */
	public function index()
	{
		try {
			
			$user = $this->app->auth();

			// Name of the Vue components
	        return $user->role_id ?  render('master_dashboard', $this->data()) : '';
	        
		} catch (Exception $e) {
			return $e->getMessage();
		}
	} 


	/**
	 * Get the response as array and return as JSON
	 * 
	 * @return JSON of the response  
	 */
	public function json()
	{

		try {

			$user = $this->app->auth();
			
	        return json_encode($user->role_id ?  $this->data() : []);
	        
		} catch (Exception $e) {
			return $e->getMessage();
		}
	} 

	/**
	 * Dashboard response as Array  
	 * 
	 * @return Array  
	 */
	public function data()
	{

		try {

			$counts = $this->loadMasterCounts();

			$array = [
	            'title' => 'Dashboard',
		        'load_vue' => true,
				'start'=>$this->start,
				'end'=>$this->end,
	        ];

			return array_merge($counts, $array);
	        
		} catch (Exception $e) {
			return $e->getMessage();
		}
	} 



	

	
	/**
	 * Load countable statstics
	 */
	public function loadMasterCounts()
	{

		$subscriberRepo = new Newsletters\Infrastructure\SubscriberRepository();
		$mediaItemRepo = new Media\Infrastructure\MediaItemRepository();
		$transactionRepo = new Transactions\Infrastructure\TransactionRepository();
		$invoicesRepo = new Invoices\Infrastructure\InvoiceRepository();
		$stationRepo = new Stations\Infrastructure\StationRepository();
		$channelRepo = new Channels\Infrastructure\ChannelRepository();
		$mediaItem = new Media\Domain\MediaItem();

		$data = [];

        $data['latest_visits'] = View::totalViews($this->start, $this->end)->with('item')->orderBy('updated_at', 'desc')->limit(5)->get();
        $data['top_visits'] = View::totalViews($this->start, $this->end)->with('item')->orderBy('times', 'desc')->limit(5)->get();
        $data['total_visits'] = View::totalViews($this->start, $this->end)->sum('times');
        $data['total_media_views'] = View::totalViews($this->start, $this->end)->where('item_type', $mediaItem::class)->sum('times');


        $data['top_customers'] = [];
        $data['top_media'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->withSum('views','times')->withCount('comments','likes')->with('artist')->limit('5')->orderBy('views_sum_times', 'desc')->get();
		$data['media_charts'] = $mediaItemRepo->eventsByDate(['start'=>$this->month_beginning, 'end'=>$this->end])->selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->groupBy('label')->limit('10')->get();
        $data['media_count'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->count();

		$data['video_charts'] = $mediaItemRepo->eventsByDate(['start'=>$this->month_beginning, 'end'=>$this->end])->where('type', 'video')->withSum('views', 'times')->selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->groupBy('label')->limit('10')->get();
        $data['video_count'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->where('type', 'video')->count();
        $data['new_videos'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->withSum('views','times')->withCount('comments','likes')->where('type', 'video')->with('artist')->limit('5')->get();
        $data['top_videos'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->withSum('views','times')->withCount('comments','likes')->where('type', 'video')->with('artist')->limit('5')->orderBy('views_sum_times', 'desc')->get();

		$data['audio_charts'] = $mediaItemRepo->eventsByDate(['start'=>$this->month_beginning, 'end'=>$this->end])->where('type', 'audio')->selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->groupBy('label')->limit('10')->get();
        $data['audio_count'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->where('type', 'audio')->count();
		$data['new_audio'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->withSum('views','times')->withCount('comments','likes')->where('type', 'audio')->with('artist')->limit('5')->get();
        $data['top_audio'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->withSum('views','times')->withCount('comments','likes')->where('type', 'audio')->with('artist')->limit('5')->orderBy('views_sum_times', 'desc')->get();

		$data['audiobook_charts'] = $mediaItemRepo->eventsByDate(['start'=>$this->month_beginning, 'end'=>$this->end])->where('type', 'audiobook')-> selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->groupBy('label')->limit('10')->get();
        $data['audiobook_count'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->where('type', 'audiobook')->count();
		$data['new_audiobooks'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->withSum('views','times')->withCount('comments','likes')->where('type', 'audiobook')->with('artist')->limit('5')->get();
        $data['top_audiobooks'] = $mediaItemRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->withSum('views','times')->withCount('comments','likes')->where('type', 'audiobook')->with('artist')->limit('5')->orderBy('views_sum_times', 'desc')->get();

		$data['customers_count'] = $this->CustomerRepository->masterByDateCount(['start'=>$this->start, 'end'=>$this->end]);
		$data['new_customers'] = $this->CustomerRepository->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->limit(10)->get();

		$data['visits_count'] = View::totalViews($this->start, $this->end)->sum('times');
		$data['visits_charts'] = View::totalViews($this->start, $this->end)->selectRaw('date as label, SUM(times) as y, item_type')->having('y', '>', 0)->groupBy('date')->limit('30')->get();
		$data['likes_charts'] = Like::whereBetween('created_at', [$this->start, $this->end])->selectRaw('Date(created_at) as label, COUNT(*) as y, item_type')->having('y', '>', 0)->groupBy('label')->limit('30')->get();
		$data['comments_charts'] = Comment::whereBetween('created_at', [$this->start, $this->end])->selectRaw('Date(created_at) as label, COUNT(*) as y, item_type')->having('y', '>', 0)->groupBy('label')->limit('30')->get();
		
		
		$data['channels_charts'] = $channelRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->groupBy('label')->limit('30')->get();
		$data['channels_count'] = $channelRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->count();
		
		$data['stations_charts'] = $stationRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->groupBy('label')->limit('30')->get();
		$data['stations_count'] = $stationRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->count();

		// Subscribers stats
		$data['subscribers_charts'] = $subscriberRepo->eventsByDate(['start'=>$this->month_beginning, 'end'=>$this->end])->selectRaw('Date(created_at) as label, COUNT(*) as y')->having('y', '>', 0)->groupBy('label')->limit('10')->get();
		$data['subscribers_count'] = $subscriberRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->count();

		
		$data['total_earnings'] = $transactionRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->sum('amount');
		$data['transactions_count'] = $transactionRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->count();
		$data['pending_invoices_count'] = $invoicesRepo->eventsByDate(['start'=>$this->start, 'end'=>$this->end])->where('status', 'unpaid')->count();

        return $data;

	}  


	/**
	 * Load sum values 
	 */
	public function loadValues()
	{

		$data = [];

        return $data;

	}  


	/**
	 * Load Items List statstics
	 */
	public function loadList()
	{

		$data = [];

        return $data;

	}  

	
	public function switchLang($lang)
	{
		$app = new \config\APP;
		$languages = array_column($app->Languages()->toArray(), 'language_code');

		$_SESSION['site_lang'] = in_array($lang, $languages) ? $lang : 'english';
		$_SESSION['lang'] = in_array($lang, $languages) ? $lang : 'english';

		$redirectRequest = $app->request()->get('redirect') ?? null;
		$redirect = !empty($redirectRequest) ? $app->CONF['url'].$redirectRequest : ($_SERVER['HTTP_REFERER'] ?? $app->CONF['url']);
		echo $app->redirect($redirect);
	}

}
