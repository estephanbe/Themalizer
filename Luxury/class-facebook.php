<?php
namespace Themalizer\Luxury;

use Helper\tests;
use Helper\sanitizes;


class Facebook 
{
	use tests;
	use sanitizes;

	public $app_id = '2739288913023233';
	public $app_secret = '064d4a5c9957d3adcc5e23e83e33e3c2';
	public $default_graph_version =  'v7.0';

	public $fb;

	const OPTIONS_KEY = '_';
	public $theme_prefix;
	public $options_prefix;

	public $page_option_name;

	const USER_OPTION_NAME = 'user_id';
	public $user_option_name;

	const LONG_LIVED_USER_TOKEN_OPTION_NAME = 'long_lived_user_token';
	public $long_lived_user_token_option_name;

	const LONG_LIVED_PAGE_TOKEN_OPTION_NAME = 'long_lived_page_token';
	public $long_lived_page_token_option_name;

// ======================================================================================
//																			__construct
// ======================================================================================

	function __construct($init, $page_option_name)
	{
		$this->processArgs($init, $page_option_name);
	}

// ======================================================================================
//																			processArgs
// ======================================================================================

	function processArgs($init, $page_option_name)
	{
		$this->isInit_test($init);
		$this->empty_test($page_option_name, 'Please make sure you fill the option name of the facebook page id');
		$this->theme_prefix = $init->prefix;
		$this->options_prefix = $this->theme_prefix . self::OPTIONS_KEY;
		
		$this->page_option_name = $this->theme_prefix . '_' . $page_option_name;
		$this->user_option_name = $this->options_prefix . self::USER_OPTION_NAME;
		$this->long_lived_user_token_option_name = $this->options_prefix . self::LONG_LIVED_USER_TOKEN_OPTION_NAME;
		$this->long_lived_page_token_option_name = $this->options_prefix . self::LONG_LIVED_PAGE_TOKEN_OPTION_NAME;
		unset($this->theme_prefix);
	}

// ======================================================================================
//																				init
// ======================================================================================

	function init()
 	{
 		$this->empty_test($this->app_id, 'Please add the App ID');
 		$this->empty_test($this->app_secret, 'Please add the App secret');
 		$this->empty_test($this->default_graph_version, 'Please add the App default_graph_version');

 		if ($this->fb instanceof \Facebook\Facebook) {
 			return;
 		}

 		$this->fb = new \Facebook\Facebook([
			'app_id' => $this->app_id,
			'app_secret' => $this->app_secret,
			'default_graph_version' => $this->default_graph_version
		]);

		$this->empty_test($this->fb, 'FB was not initialized!');
 	}


// ======================================================================================
//																			update_option
// ======================================================================================

	function update_option($option, $value='', $retrived_value='')
 	{
 		$this->empty_test($value, 'please make sure to add the value of the option in update_option, and make sure it is not empty.');
 		
 		$updated = 'exists';

 		
	 	// if (empty($retrived_value)) {
 		// 	$option_value = $this->retrive_option($option);
 		// } else {
 		// 	$option_value = $retrived_value;
 		// }

 		

 		// if (!$option_value) {
 			try {

 				$optionName = $this->options_prefix . $option;
	 			$updated = update_option( $optionName, $value);

	 		} catch (\Exception $e) {
	 			return 'an error from update_option: ' . $e->getMessage();
	 		} 	
 		// }


 		$arr = [
 			'option' => $option, 
 			// 'value' => $option_value, 
 			'value' => $this->retrive_option($option), 
 			'updateStatus' => $updated
 		];

 		$final_results = (OBJECT) $arr;
 		return $final_results;
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
//															get_long_lived_page_access_token
// ======================================================================================

 	function get_long_lived_page_access_token($user_id='', $short_lived_user_access_token='')
	{
		$this->empty_test($user_id, 'Make sure to add the facebook user ID');
		$this->empty_test($short_lived_user_access_token, 'Make sure to add the facebook short_lived_user_access_token');

 		$retrived_user_id = $this->retrive_option('facebook_user_id');
		if (!$retrived_user_id || $user_id !== $retrived_user_id) {
			$user_id = $this->update_option('facebook_user_id', $user_id, $retrived_user_id)->value;
		}

		$long_lived_user_access_token = $this->get_long_lived_user_access_token($short_lived_user_access_token);
		$this->user_id = $user_id;
		$this->long_lived_user_access_token = $long_lived_user_access_token;

		$this->init();

		try {
			// Returns a `FacebookFacebookResponse` object
			$response = $this->fb->get(
				$this->user_id . '/accounts?access_token=' . $this->long_lived_user_access_token,
				$this->long_lived_user_access_token
			);
		} catch(\FacebookExceptionsFacebookResponseException $e) {
			return 'Graph returned an error from get_long_lived_page_access_token: ' . $e->getMessage();
			exit;
		} catch(\FacebookExceptionsFacebookSDKException $e) {
			return 'Facebook SDK returned an error from get_long_lived_page_access_token: ' . $e->getMessage();
			exit;
		} catch (\Exception $e) {
			return 'an error from get_long_lived_page_access_token: ' . $e->getMessage();
			exit;
		}

		$graphEdge = $this->get_graphEdge($response);

		$page = (OBJECT) $graphEdge[0];

		$long_lived_page_token = $page->access_token;

		$updated_llpt = $this->update_option('facebook_long_lived_page_token', $long_lived_page_token)->value;

		return $long_lived_page_token;
	}


// ======================================================================================
//															get_long_lived_user_access_token
// ======================================================================================

	function get_long_lived_user_access_token($short_lived_user_access_token)
	{

 		$this->empty_test($short_lived_user_access_token, 'Please fill in the short_lived_user_access_token');


		$this->init();

		try {
			// Returns a `FacebookFacebookResponse` object
			$response = $this->fb->get(
				'oauth/access_token?grant_type=fb_exchange_token&client_id='.$this->app_id.'&client_secret='.$this->app_secret.'&fb_exchange_token=' . $short_lived_user_access_token,
				$short_lived_user_access_token
			);
		} catch(\FacebookExceptionsFacebookResponseException $e) {
			return 'Graph returned an error from get_long_lived_user_access_token: ' . $e->getMessage();
			exit;
		} catch(\FacebookExceptionsFacebookSDKException $e) {
			return 'Facebook SDK returned an error from get_long_lived_user_access_token: ' . $e->getMessage();
			exit;
		} catch (\Exception $e) {
			return 'an error from get_long_lived_user_access_token: ' . $e->getMessage();
			exit;
		}

		$graphNode = $this->get_graphNode($response);

		$long_lived_user_access_token = $graphNode->getField('access_token');
		$updated_llut = $this->update_option('facebook_long_lived_user_token', $long_lived_user_access_token)->value;

		return $long_lived_user_access_token;
	}

// ======================================================================================
//																	get_page_total_count
// ======================================================================================

	function get_page_total_count($page_id='', $long_lived_page_access_token='', $limit='0', $since='2010-01-01')
	{

		$this->empty_test($page_id, 'Please add the page id');
		$this->empty_test($long_lived_page_access_token, 'Please add the long_lived_page_access_token');

		if (empty($this->page_id)) {
			$this->page_id = $page_id;
		}

		if (empty($this->long_lived_page_access_token)) {
			$this->long_lived_page_access_token = $long_lived_page_access_token;
		}

		$this->init();

		try {
			// Returns a `FacebookFacebookResponse` object
			$response = $this->fb->get(
				$this->page_id . '/published_posts?limit=' . $limit . '&since=' . $since . '&summary=total_count&access_token=' . $this->long_lived_page_access_token,
				$this->long_lived_page_access_token
			);
		} catch(FacebookExceptionsFacebookResponseException $e) {
			return 'Graph returned an errorfrom get_page_total_count: ' . $e->getMessage();
			exit;
		} catch(FacebookExceptionsFacebookSDKException $e) {
			return 'Facebook SDK returned an errorfrom get_page_total_count: ' . $e->getMessage();
			exit;
		} catch (Exception $e) {
			return 'Error from get_page_total_count: ' . $e->getMessage();
			exit;
		}

		$responseObj = (OBJECT) $response->getDecodedBody();

		$page_total_count = $responseObj->summary['total_count'];

		return $page_total_count;
	}

// ======================================================================================
//																get_most_recent_page_posts
// ======================================================================================

	function get_most_recent_page_posts($page_id='', $long_lived_page_access_token='')
	{

		$this->empty_test($page_id, 'Please add the page id');
		$this->empty_test($long_lived_page_access_token, 'Please add the long_lived_page_access_token');

		if (empty($this->page_id)) {
			$this->page_id = $page_id;
		}

		if (empty($this->long_lived_page_access_token)) {
			$this->long_lived_page_access_token = $long_lived_page_access_token;
		}

		$this->init();

		try {
			// Returns a `FacebookFacebookResponse` object
			$response = $this->fb->get(
				$this->page_id . '/feed?fields=id,full_picture,created_time,attachments,message,status_type,permalink_url&access_token=' . $this->long_lived_page_access_token,
				$this->long_lived_page_access_token
			);
		} catch(FacebookExceptionsFacebookResponseException $e) {
			return 'Graph returned an errorfrom get_most_recent_page_posts: ' . $e->getMessage();
			exit;
		} catch(FacebookExceptionsFacebookSDKException $e) {
			return 'Facebook SDK returned an errorfrom get_most_recent_page_posts: ' . $e->getMessage();
			exit;
		} catch (Exception $e) {
			return 'Error from get_most_recent_page_posts: ' . $e->getMessage();
			exit;
		}

		$responseObj = (OBJECT) $response->getDecodedBody();

		return $responseObj;
	}













// ======================================================================================
//																			get_graphNode
// ======================================================================================

 	function get_graphNode($response)
	{
		try {
			// Returns a `FacebookFacebookResponse` object
			$graphNode = $response->getGraphNode();
		} catch(\FacebookExceptionsFacebookResponseException $e) {
			return 'Graph returned an error from get_graphNode: ' . $e->getMessage();
			exit;
		} catch(\FacebookExceptionsFacebookSDKException $e) {
			return 'Facebook SDK returned an error from get_graphNode: ' . $e->getMessage();
			exit;
		} catch (\Exception $e) {
			return 'an error from get_graphNode: ' . $e->getMessage();
			exit;
		}

		return $graphNode;
	}

// ======================================================================================
//																			get_graphEdge
// ======================================================================================

	function get_graphEdge($response)
	{
		$res = [];
		try {
			// Returns a `FacebookFacebookResponse` object
			$graphEdge = $response->getGraphEdge();
			foreach ($graphEdge as $edge) 
			{
				$res[] = $edge->asArray();
			}
		} catch(\FacebookExceptionsFacebookResponseException $e) {
			return 'Graph returned an error from get_graphEdge: ' . $e->getMessage();
			exit;
		} catch(\FacebookExceptionsFacebookSDKException $e) {
			return 'Facebook SDK returned an error from get_graphEdge: ' . $e->getMessage();
			exit;
		} catch (\Exception $e) {
			return 'an error from get_graphEdge: ' . $e->getMessage();
			exit;
		}

		return $res; 
	}

 	
}


