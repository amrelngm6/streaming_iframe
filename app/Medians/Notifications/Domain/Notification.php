<?php

namespace Medians\Notifications\Domain;

use Shared\dbaser\CustomModel;

use Medians\Users\Domain\User;
use Medians\Customers\Domain\Customer;
use Medians\Mail\Application\MailService;



/**
 * Notification class database queries
 */
class Notification extends CustomModel
{

	/**
	* @var String
	*/
	protected $table = 'notifications';


	protected $fillable = [
		'receiver_type',
		'receiver_id',
		'event_id',
		'model_type',
		'model_id',
		'subject',
		'body',
		'body_text',
		'status'
	];


	public $appends = ['receiver_name', 'notification_model', 'date', 'model_short_name', 'short_date', 'url'];

	public function getNotificationModelAttribute()
	{
		$reflect = new \ReflectionClass($this->model_type);
		return $reflect->getShortName();
	}
	
	public function getDateAttribute()
	{
		$date = date('Y-m-d', strtotime($this->created_at));

		return date( ($date == date('Y-m-d')) ? 'H:i a' : 'M d, H:i a'  , strtotime($this->created_at));

	}

	public function getShortDateAttribute()
	{
		$date = date('Y-m-d', strtotime($this->created_at));
		return date( ($date == date('Y-m-d')) ? 'H:i' : 'M d, H:i'  , strtotime($this->created_at));
	}

	public function getModelShortNameAttribute()
	{
    	return strtolower((new \ReflectionClass(new $this->model_type))->getShortName());
	}

	public function getReceiverNameAttribute()
	{
		if ($this->receiver_type == User::class)
			return $this->user->name;

	}

	public function getUrlAttribute()
	{
		switch ($this->model_short_name) 
		{
			case 'customers':
				return '/admin/customers';
				break;
			
		}
	}

	public function getFields()
	{
		return $this->fillable;
	}


	public function user()
	{
		return $this->hasOne(User::class, 'id', 'receiver_id');
	}



	/**
	 * Store notification from Event
	 * 
	 * @param $event_id int 
	 * @param $subject String 
	 * @param $body String Notification content
	 * @param $model Object Model that called the event 
	 * @param $receiver Object Receiver of the Notification 
	 */
	public static function storeEventNotification(Object $event, $model, $receiver)
	{

		if (!is_object($receiver))
		{
			return null;
		}

		$receiverPK = $receiver->getPrimaryKey();
		$modelPK =  $model->getPrimaryKey();

		if (empty($receiver->$receiverPK))
		{
			return null;
		}

    	// Store notification
		$filled['receiver_type'] = get_class($receiver);
		$filled['receiver_id'] = $receiver->$receiverPK;
		$filled['event_id'] = $event->id;
		$filled['model_type'] = get_class($model);
		$filled['model_id'] = $model->$modelPK;
    	$filled['subject'] = $event->subject;
    	$filled['body'] = $event->body;
    	$filled['body_text'] = $event->body_text;
    	$filled['status'] = 'new';

    	$notification = Notification::create($filled);

		return (new Notification)->sendNotification($notification, $receiver);
		
	}  


	/**
	 * Send notification
	 */
	public function sendNotification(Notification $notification, $receiver)
	{
		
		$sendMail = new MailService($receiver->email, $receiver->name, $notification->subject, $notification->body);

		$send = $sendMail->sendMail();

		return $send;
		
		// $id = "Customer-".$notification->receiver_id;
		// $sendOneSignalNotification = new \Shared\OneSignal\OneSignalService($id);
		// return  $sendOneSignalNotification->sendNotification($notification->subject, $notification->body_text);

	}
}
