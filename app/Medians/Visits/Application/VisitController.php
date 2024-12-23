<?php

namespace Medians\Visits\Application;
use Medians\Visits\Infrastructure\VisitRepository;
use Medians\Visits\Domain\Visit;
use Shared\dbaser\CustomController;
use Illuminate\Database\Capsule\Manager as DB;


class VisitController extends CustomController 
{

    public $app;

    public $repo;

    function __construct()
    {
        $this->app = new \Config\APP;
    }
    

	

	/**
	 * Admin index items
	 * 
	 * @param Silex\Application $app
	 * @param \Twig\Environment $twig
	 * 
	 */ 
	public function index( ) 
	{
		
		try {
			
		    return render('timeline', [
		        'load_vue' => true,
		        'title' => translate('Visitors timeline'),
				'visits_list' => Visit::totalVisits(date('Y-m-d', strtotime(' -7 days ')), date('Y-m-d', strtotime(' +1 days ')))->with('item')->orderBy('updated_at', 'desc')->limit(500)->get(),
				'visits_ip_list' => Visit::totalVisits(date('Y-m-d', strtotime(' -7 days ')), date('Y-m-d', strtotime(' +1 days ')))->select('*', DB::raw('count(*) as total'))->with('item')->orderBy('total', 'DESC')->orderBy('updated_at', 'desc')->groupBy('ip')->limit(50)->get()
		    ]);

		} catch (\Exception $e) {
			throw new \Exception($e->getMessage(), 1);
			
		}
	}

    
}