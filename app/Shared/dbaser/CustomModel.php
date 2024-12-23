<?php

namespace Shared\dbaser;

use \Illuminate\Database\Eloquent\Model;

use Medians\Users\Domain\User;
use Medians\Content\Domain\Content;
use Medians\Likes\Domain\Like;
use Medians\Views\Domain\View;
use Medians\Comments\Domain\Comment;
use Medians\Notifications\Domain\NotificationEvent;
use Medians\Logs\Domain\UsageLog;

use \config\APP;

class CustomModel extends Model
{

	
	public function getId()
	{
		$l = $this->getPrimaryKey();
		return isset($this->$l) ? $this->$l : 0; 
	}

	public function getPrimaryKey()
	{
		return  $this->primaryKey ?? 'id';
	}

	public function getTable()
	{
		return  $this->table;
	}

	public function getClassName()
	{
		return  get_class($this);
	}

	
	public function getFields()
	{
		return $this->fillable;
	}

	
	public function viewscount() 
	{
		return $this->morphMany(View::class , 'item')->sum('times');	
	}
	
	
	public function commentscount() 
	{
		return $this->morphMany(Comment::class , 'item')->count();	
	}
	
	public function likescount() 
	{
		return $this->morphMany(Like::class , 'item')->count();	
	}

	public function can($permission, $app)
	{
	    if (isset($app->auth->role_id))
	    {

	        if ($app->auth->role_id == 1)
	            return true;

		    if (isset($this->agent_id) && $this->agent_id == $app->auth->id)
	            return true;

		    if (isset($this->created_by) && $this->created_by == $app->auth->id)
	            return true;

		    if (get_class($this) == User::class && $this->id == $app->auth->id)
	            return true;

	    }


	    return null;
	}

	/**
	 * Password encryption method
	 * @param $value String 
	 */ 	
	public static function encrypt(String $value ) : String 
	{
		return sha1(md5($value));
	}

	
	/**
	 * Generate random password
	 */
	public function randomPassword() {
		$alphabet = '12345678900';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

	
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'created_by');
	}

	// public function content()
	// {
	// 	return $this->morphOne(Content::class, 'item')->where('lang', $_SESSION['lang']);
	// }

	// public function en()
	// {
	// 	return $this->morphOne(Content::class, 'item')->where('lang', 'en');
	// }

	// public function ar()
	// {
	// 	return $this->morphOne(Content::class, 'item')->where('lang', 'ar');
	// }


	public function sessionGuest()
	{
		if (empty($_SESSION['guest']))
		{
			$_SESSION['guest'] = sha1(md5(date('ymdhis').rand(9,99)));
		}

		return $_SESSION['guest'];
	}

	public function addView()
	{

		$view = View::firstOrCreate(['date'=> date('Y-m-d'), 'item_type'=>get_class($this), 'item_id'=>$this->getId()]);
		$view->update(['times'=> $view->times ? ($view->times+1) : 1]);
	}


    protected function finishSave(array $options)
    {

    	if ($this->wasRecentlyCreated)
    		return $this->createdEvent();

    	return $this->updatedEvent();
    }


    /**
     * Handle the event after new item 
     * has been stored 
     * 
     */
    public function createdEvent()
    {

    	if (!$this->wasRecentlyCreated)
    		return true;
		
    	// Insert activation code 
		$updateEvent = (new NotificationEvent)->handleEvent($this, 'create');

    	return  DEBUG_MODE ? UsageLog::addItem($this, 'create') : $updateEvent;
    }  

    /**
     * Handle the event after an item 
     * has been updated 
     * 
     */
    public function updatedEvent()
    {

		$PK = $this->getPrimaryKey();

    	if (empty($this->$PK)) {
    		return null;
		}

    	$fields = array_fill_keys($this->fillable,1);
    	$updatedFields = array_intersect_key($fields, $this->getDirty());
    	if (empty($updatedFields))
		{
    		return null;
		}

		$updateEvent = (new NotificationEvent)->handleEventUpdate($this, 'update', array_keys($updatedFields));

    	return  DEBUG_MODE ? UsageLog::addItem($this, 'update', json_encode($updatedFields)) : $updateEvent;

    }  

}



