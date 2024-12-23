<?php
namespace Shared\OneSignal;

use Shared\dbaser\CustomController;

class OneSignalService 
{

    /**
     * OneSignal APP ID
     */
    protected $app;

    /**
     * OneSignal APP ID
     */
    protected $APP_ID;

    /**
     * OneSignal APP KEY TOKEN
     */
    protected $APP_KEY_TOKEN;

    protected $receiver_id;

    function __construct($id)
	{

        $this->app = new \config\APP;
        $Settings = $this->app->SystemSetting();
        if ($Settings)
        {
            $this->APP_ID = $Settings['onesignal_app_id'];
            $this->APP_KEY_TOKEN = $Settings['onesignal_app_key_token'];
        }

        $this->receiver_id = $id;
	}


    public function send($subject, $messageText)
    {
        if ($this->APP_ID)
        {
            return $this->sendNotification($subject, $messageText);
        }
    }


    function sendNotification($subject, $messageText) {
        
        $headings = array(
            "en" => strip_tags($subject)
        );

        $content = array(
            "en" => strip_tags($messageText)
        );
    
        $fields = array(
            'app_id' => $this->APP_ID,
            'headings' => $headings,
            'contents' => $content,
            'target_channel' => 'push',
            'include_aliases' => ['external_id'=>[$this->receiver_id]]
        );
        
        $fields = json_encode($fields);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $this->APP_KEY_TOKEN
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
        $response = curl_exec($ch);
        curl_close($ch);
        
        $responseObject =  json_decode($response);

        if (isset($responseObject->errors))
        {
            error_log($response);
        }

        return $response;
    }
    
}