<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

/**
 * This file is part of Social Media Statistics module and used for cron jobs;
 * Author: Dinesh Devkota
 * www.dineshdevkota.com
 */
class Front_media extends Public_Controller {
	public function __construct() {
		// Add data array
		$this->data = new stdClass ();
		
		parent::__construct ();
		
		$this->lang->load ( 'social' );
		
		// load php library
		$this->load->library ( 'googleAnalyticsAPI' );
	}
	public function api($value = "") {
		$this->load->model ( 'Creds' );
		$this->Creds->get_gacreds ();
		
		$valuesdb = $this->session->userdata ( 'creds' );
		
		if ($value == $valuesdb ['sender_crud_code']) {
			$ga = new GoogleAnalyticsAPI ();
			
			// need to get this from database Will be same and need to get from user.
			// use model creds to set values to userdata and feed to the following
			$this->load->model ( 'Creds' );
			$this->Creds->get_gacreds ();
			
			$valuesdb = $this->session->userdata ( 'creds' );
			
			$ga->auth->setClientId ( $valuesdb ['ClientId'] );
			$ga->auth->setClientSecret ( $valuesdb ['Clientsecret'] );
			$ga->auth->setRedirectUri ( base_url () . "admin/media_stats/queryapi" );
			// from database Will be same and generated from API.
			$accessToken = $valuesdb ['ga_accessToken'];
			
			$ga->setAccessToken ( $accessToken );
			
			// should be constant all the time.
			$tokenExpires = $valuesdb ["ga_tokenExpires"];
			// new in every cron job
			$tokenCreated = $valuesdb ["ga_tokenCreated"];
			// store those values back to database so that we can use it later on.
			// $ga->auth->refreshAccessToken ( );
			
			if ((time () - $tokenCreated) >= $tokenExpires) {
				
				$auth = $ga->auth->refreshAccessToken ( $valuesdb ["ga_refreshToken"] );
				$accessToken = $auth ['access_token'];
				$refreshToken = $auth ['refresh_token'];
				$tokenExpires = $auth ['expires_in'];
				
				$entry_data = array (
						'ga_accessToken' => $accessToken,
						'ga_refreshToken' => $refreshToken,
						'ga_tokenCreated' => time (),
						'ga_tokenExpires' => $tokenExpires 
				);
				$entry = $this->streams->entries->update_entry ( 1, $entry_data, 'social_media_creds', 'social_media' );
				$this->api ( $valuesdb ["sender_crud_code"] );
			}
			
			$profiles = $ga->getProfiles ();
			$accounts = array ();
			
			foreach ( $profiles ['items'] as $item ) {
				$id = "ga:{$item['id']}";
				
				$name = $item ['name'];
				$accounts [$id] = $name;
			}
			$ga->setAccountId ( $id );
			
			$defaults = array (
					'start-date' => date ( 'Y-m-d' ),
					'end-date' => date ( 'Y-m-d' ) 
			);
			$ga->setDefaultQueryParams ( $defaults );
			$visits = $ga->query ();
			$totalvisits = $visits ['totalsForAllResults'] ["ga:visits"];
			
			$referralTraffic = $ga->getReferralTraffic ();
			// /dump($referralTraffic);
			
			$googleplustraffic = $facebooktraffic = $googletraffic = $redittraffic = $twittertraffic = $bingtraffic = $pininteresttraffic = $linkedintraffic = 0;
			if (array_key_exists ( "rows", $referralTraffic )) {
				foreach ( $referralTraffic ["rows"] as $key => $values ) {
					// dump($values);
					$pieces = explode ( ".", $values [0] );
					switch ($pieces [0]) {
						case "facebook" :
						case "l" :
						case "lm" :
							$facebooktraffic = $facebooktraffic + intval ( $values [1] );
							break;
						case "google" :
						case "draft" :
							$googletraffic = $googletraffic + intval ( $values [1] );
							break;
						case "linkedin" :
							$linkedintraffic = $linkedintraffic + intval ( $values [1] );
							break;
						case "reddit" :
							$redittraffic = $redittraffic + intval ( $values [1] );
							break;
						case "bing" :
							$bingtraffic = $bingtraffic + intval ( $values [1] );
							break;
						case "plus" :
							$googleplustraffic = $googleplustraffic + intval ( $values [1] );
							break;
						case "pinterest" :
							$pininteresttraffic = $pininteresttraffic + intval ( $values [1] );
							break;
						case "t" :
							$twittertraffic = $twittertraffic + intval ( $values [1] );
							break;
					}
				}
				echo "<br>" . $facebooktraffic;
				echo "<br>" . $googletraffic;
				echo "<br> linkedintraffic" . $linkedintraffic;
				echo "<br> redittraffic" . $redittraffic;
				echo "<br> bingtraffic " . $bingtraffic;
				echo "<br>googleplustraffic " . $googleplustraffic;
				echo "<br>pininteresttraffic " . $pininteresttraffic;
				echo "<br> twittertraffic" . $twittertraffic;
			}
			
			if ($referralTraffic ["http_code"] == 200) {
				$entry_data = array (
						'raw_data' => json_encode ( $referralTraffic ),
						'total_visit' => $totalvisits,
						'vbd_fb' => $facebooktraffic,
						'vbd_google' => $googletraffic,
						'vbd_reddit' => $redittraffic,
						'vbd_Twitter' => $twittertraffic,
						'vbd_bing' => $bingtraffic,
						'vbd_pininterest' => $pininteresttraffic,
						'vbd_linkedin' => $linkedintraffic 
				)
				;
				
				$params = array (
						'stream' => 'google_analytics',
						'namespace' => 'social_media',
						'year' => intval ( date ( 'Y' ) ),
						'day' => intval ( date ( 'd' ) ),
						'month' => intval ( date ( 'm' ) ) 
				);
				$databasevalue = $this->streams->entries->get_entries ( $params );
				$id = intval ( $databasevalue ["entries"] [0] ["id"] );
				
				if (sizeof ( $databasevalue ['entries'] ) == 1) {
					$entry = $this->streams->entries->update_entry ( $id, $entry_data, 'google_analytics', 'social_media' );
				} 

				else {
					
					$this->streams->entries->insert_entry ( $entry_data, 'google_analytics', 'social_media' );
				}
			}
			// var_dump($visitsByCountry);
			// var_dump($visitsByLanguages = $ga->getVisitsByLanguages(array('start-date' => '2013-01-01' )));//Overwrite this from the defaultQueryParams)));
			
			// $visitsByOs = $ga->getVisitsBySystemOs(array('max-results' => 100));
			// var_dump($visitsByOs);
		} else {
			$this->load->model ( 'Creds' );
			$this->Creds->get_gacreds ();
			
			$valuesdb = $this->session->userdata ( 'creds' );
			$to = $valuesdb ["created_by"] ["email"];
			echo "<h1>Authentication Failed. This attempt has been reported to Developer.</h1>";
			
			$subject = 'Unsuccessful Attempt';
			$message = "Hey Developer," . "\r\n" . "Cron job failed from IP Address :" . $this->get_ip () . "\r\n" . "Please check the cron code in server if you tried accessing it.";
			
			mail ( $to, $subject, $message );
		}
	}
	function get_ip() {
		
		// Just get the headers if we can or else use the SERVER global
		if (function_exists ( 'apache_request_headers' )) {
			
			$headers = apache_request_headers ();
		} else {
			
			$headers = $_SERVER;
		}
		
		// Get the forwarded IP if it exists
		if (array_key_exists ( 'X-Forwarded-For', $headers ) && filter_var ( $headers ['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )) {
			
			$the_ip = $headers ['X-Forwarded-For'];
		} elseif (array_key_exists ( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var ( $headers ['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )) {
			
			$the_ip = $headers ['HTTP_X_FORWARDED_FOR'];
		} else {
			
			$the_ip = filter_var ( $_SERVER ['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}
		
		return $the_ip;
	}
}
?>