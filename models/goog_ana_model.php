<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
	/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of goog_ana_model
 *
 * basic example code
 */
class Goog_ana_model extends MY_Model {
	// put your code here
	public function get_account_id() {
		
		// session_start();
		$ga = new GoogleAnalyticsAPI ();
		
		$this->load->model ( 'Creds' );
		$this->Creds->get_gacreds ();
		
		$valuesdb = $this->session->userdata ( 'creds' );
		
		$ga->auth->setClientId ( $valuesdb ['ClientId'] );
		$ga->auth->setClientSecret ( $valuesdb ['Clientsecret'] );
		$ga->auth->setRedirectUri ( base_url () . "admin/media_stats/queryapi" );
		
		/*
		 * Step 1: Check if we have an oAuth access token in our session
		 * If we've got $_GET['code'], move to the next step
		 */
		if (! isset ( $_SESSION ['oauth_access_token'] ) && ! isset ( $_GET ['code'] )) {
			// Go get the url of the authentication page, redirect the client and go get that token!
			
			$url = $ga->auth->buildAuthUrl ();
			
			header ( "Location: " . $url );
		}
		
		if (! isset ( $_SESSION ['oauth_access_token'] ) && isset ( $_GET ['code'] )) {
			
			// $auth = $ga->auth->getAccessToken($_GET['code']);
			$code = $_GET ['code'];
			
			$auth = $ga->auth->getAccessToken ( $code );
			
			if ($auth ['http_code'] == 200) {
				$accessToken = $auth ['access_token'];
				$refreshToken = $auth ['refresh_token'];
				$tokenExpires = $auth ['expires_in'];
				$tokenCreated = time ();
				
				// For simplicity of the example we only store the accessToken
				// If it expires use the refreshToken to get a fresh one
				$_SESSION ['oauth_access_token'] = $accessToken;
			} else {
				die ( "Sorry, something went wrong retrieving the oAuth tokens" );
			}
		}
		
		if ((time () - $tokenCreated) >= $tokenExpires) {
			$auth = $ga->auth->refreshAccessToken ( $refreshToken );
		}
		
		$ga->setAccessToken ( $accessToken );
		
		$profiles = $ga->getProfiles ();
		$accounts = array ();
		foreach ( $profiles ['items'] as $item ) {
			$id = "ga:{$item['id']}";
			$name = $item ['name'];
			$accounts [$id] = $name;
		}
		$ga->setAccountId ( $id );
		
		$defaults = array (
				'start-date' => date ( 'Y-m-d', strtotime ( '-1 month' ) ),
				'end-date' => date ( 'Y-m-d' ) 
		);
		
		$ga->setDefaultQueryParams ( $defaults );
		
		$params = array (
				'metrics' => 'ga:visits',
				'demensions' => 'ga:date' 
		);
		
		$visits = $ga->query ( $params );
		return $visits;
	}
}
?>