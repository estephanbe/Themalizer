<?php
namespace BoshDev\Luxury;

use BoshDev\Helper\tests;
use BoshDev\Helper\sanitizes;


class MailChimp 
{
	use tests;
	use sanitizes;

	public $api_key;
	public $list_id;

	const OPTIONS_KEY = '_';
	public $options_prefix;

	

// ======================================================================================
//																			__construct
// ======================================================================================

	function __construct($init)
	{
		$this->processArgs($init);
	}

// ======================================================================================
//																			processArgs
// ======================================================================================

	function processArgs($init)
	{
		$this->isInit_test($init);
		// $this->empty_test($page_option_name, 'Please make sure you fill the option name of the facebook page id');
		$this->options_prefix = $init->prefix . self::OPTIONS_KEY;
		$this->api_key = $this->retrive_option('mailChimpApiKey');
		$this->list_id = $this->retrive_option('mailChimpList_id');
	}

// ======================================================================================
//																			retrive_option
// ======================================================================================
	
 	function retrive_option($option)
 	{
 		$optionName = $this->options_prefix . $option;

 		return get_option($optionName);
 	}

// ======================================================================================
//																			add_subscriber
// ======================================================================================
	
 	function add_subscriber($email)
 	{
 		if (empty($email) && empty($this->api_key) && empty($this->list_id)) {
 			return;
 		}

 		$data = [
		    'email'     => $email,
		    'status'    => 'subscribed',
		];


	    $memberId = md5(strtolower($data['email']));
	    $dataCenter = substr($this->api_key,strpos($this->api_key,'-')+1);
	    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->list_id . '/members/' . $memberId;

	    $json = json_encode([
	        'email_address' => $data['email'],
	        'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
	    ]);

	    $ch = curl_init($url);

	    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $this->api_key);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 

	    $result = curl_exec($ch);
	    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
 	}

}


