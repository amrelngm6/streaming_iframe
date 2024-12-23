<?php

namespace Medians\Transactions\Application;
use \Shared\dbaser\CustomController;

use Medians\Transactions\Infrastructure\TransactionRepository;
use Medians\Invoices\Infrastructure\InvoiceRepository;
use Medians\Customers\Domain\Customer;

class PaymentService
{

	
	public $payment_method;
	public $service;
	public $transactionRepo;
	

	function __construct($payment_method)
	{
		$this->payment_method = $payment_method;	
		$this->loadService();
	}

	function loadService() 
	{
		$this->service = new PaypalService();
	}

	
	public function verify($params)
	{
		$verify = $this->service->verify((array) $params['transaction']);
		
		if (isset($verify->status) && $verify->status == true)
		{
			$invoiceRepo = new InvoiceRepository();

			$invoice = $invoiceRepo->find($params['invoice_id']);

			$transaction = $this->storeTransaction($params, $invoice, $verify);

			$subscription = $this->updatePackageSubscription($params, $invoice, $transaction);

			return $transaction;
		}
	}

	public function storeTransaction($params, $invoice, $verifyResponse)
	{
		try {

			// Get the paid amount and currency based on 
			// the verification response from API
			$amountCurrency = $this->service->getAmountCurrency($verifyResponse);
			
			$params['amount'] = $amountCurrency['amount'];
			$params['currency'] = $amountCurrency['currency'];
			$params['status'] = $amountCurrency['status'];

			$this->transactionRepo = new TransactionRepository();
			
			$transaction = array() ;
			$transaction['invoice_id'] = $invoice->invoice_id;
			$transaction['customer_id'] = $invoice->customer_id;
			$transaction['subscription_id'] = $invoice->subscription_id;
			$transaction['date'] = date('Y-m-d');
			$transaction['payment_method'] = $this->payment_method;
			$transaction['amount'] = $params['amount'];
			$transaction['currency_code'] = $params['currency'];
			$transaction['status'] = $params['status'];
			$transaction['field'] = $params['transaction'];
		
			return  $this->transactionRepo->store($transaction);
			
		} catch (\Throwable $th) {
			return array('error'=>$th->getMessage());
		}
	}


    public function updatePackageSubscription($params, $invoice, $transaction)
	{
		try {

			if ($transaction->amount == $invoice->total_amount)
			{
                
                $invoice->status = 'paid';
                $updateInvoice = $invoice->save();
                
                $invoice->update(['status'=>'paid']);
                $updateSubscription = $invoice->item->update(['payment_status'=>'paid' , 'status'=>'active']);

                return true;
			}
			
		} catch (\Throwable $th) {
			return array('error'=>$th->getMessage());
		}
		
	}
	
}