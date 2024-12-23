<?php

namespace Medians\Transactions\Application;
use \Shared\dbaser\CustomController;

use Medians\Packages\Infrastructure\PackageSubscriptionRepository;
use Medians\Payments\Infrastructure\PaymentRepository;
use Medians\Transactions\Infrastructure\TransactionRepository;
use Medians\Customers\Domain\Customer;

class PaypalService
{

	
	public $app;
	

	public function verify($params)
	{
        $this->app = new \config\APP;
        $settings = $this->app->SystemSetting();

        $accessToken =  $this->getPayPalAccessToken($settings);

        $verify = $this->getTransactionDetails($params, $accessToken, $settings);

        $response = ($verify->status == 'APPROVED' || $verify->status == 'COMPLETED') ? $verify : throw new \Exception($verify, 1);
        $response->status == true;

        return $response;
    }


    function getPayPalAccessToken($settings) {
        $url = $settings['paypal_mode'] == 'sandbox' ? "https://api.sandbox.paypal.com/v1/oauth2/token" : "https://api.paypal.com/v1/oauth2/token";
    
        $headers = [
            "Accept: application/json",
            "Accept-Language: en_US",
        ];
    
        $postFields = "grant_type=client_credentials";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $settings['paypal_api_key'] . ":" . $settings['paypal_api_secret']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
    
        $responseArray = json_decode($response, true);
        return $responseArray['access_token'];
    }
    
    function getTransactionDetails($params, $accessToken, $settings) {
        
        $url = $settings['paypal_mode'] == 'sandbox' ?  ("https://api-m.sandbox.paypal.com/v2/checkout/orders/" . $params['id']) : ("https://api-m.paypal.com/v2/checkout/orders/".$params['id']) ;
    
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer $accessToken"
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        
        return json_decode($response);
    }
    
	public function getAmountCurrency($response)
    {
        $items = $response->purchase_units;
        $totalAmount = 0;
        $currency = '';
        foreach ($items as $key => $value) {
            $totalAmount += $value->amount->value;
            $currency = $value->amount->currency_code;
        }
        return ['status' => $response->status, 'amount'=>$totalAmount, 'currency'=> $currency];
    }

}