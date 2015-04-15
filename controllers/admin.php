<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
/**
 * Admin controller for the Events Module
 *
 * @author Dinesh Devkota
 * @package
 *
 */
class Admin extends Admin_Controller {
	/**
	 *
	 * @var string The current active section
	 */
	protected $section = 'media_stats';
	public function __construct() {
		// Add data array
		$this->data = new stdClass ();
		
		parent::__construct ();
		
		$this->lang->load ( 'social' );
		
		// load php library
		$this->load->library ( 'googleAnalyticsAPI' );
	}
	/**
	 */
	/*public function index() {
		$this->template->title ( lang ( 'media_stats:API' ) )->set_breadcrumb ( "Media Stats Page" );
		Events::trigger ( 'page_build', $this->template );
		
		$this->template->build ( 'admin/settings.php' );
	}
	
	public function setting() {
		$this->template->title ( lang ( 'media_stats:API' ) )->set_breadcrumb ( "Media Stats Page" );
		Events::trigger ( 'page_build', $this->template );
		$this->template->build ( 'admin/settings.php' );
	}
	*/
	public function index() {
		$entry = $this->input->get ();
		
		if ($entry != null) {
			$entry_data = array (
					'Analytics_ClientName' => $entry ['Analytics_ClientName'],
					'Analytics_ClientId' => $entry ['Analytics_ClientId'],
					'ClientId' => $entry ['ClientId'],
					'Clientsecret' => $entry ['Clientsecret']
			);
			
			$params = array (
					'stream' => 'social_media_creds',
					'namespace' => 'social_media',
					'where' => SITE_REF . "_ntsocial_media_creds.id = 1",
					'limit' => 1 
			);
			$order = $this->streams->entries->get_entries ( $params );

			if ($order ['total'] == 0) {
				$entry = $this->streams->entries->insert_entry ( $entry_data, 'social_media_creds', 'social_media' );
				$this->session->set_flashdata ( 'success', 'Your Setup Has been completed. Please setup cron job.' );
				$this->load->model("Goog_ana_model");
				$this->Goog_ana_model->get_account_id();
				
			} else {
				$entry = $this->streams->entries->update_entry ( 1, $entry_data, 'social_media_creds', 'social_media' );
				$this->session->set_flashdata ( 'success', 'Your Setup Has been updated. Please setup cron job.' );
				$this->load->model("Goog_ana_model");
				$this->Goog_ana_model->get_account_id();
			}
		} 

		else {
			$params = array (
					'stream' => 'social_media_creds',
					'namespace' => 'social_media',
					'where' => SITE_REF . "_ntsocial_media_creds.id = 1",
					'limit' => 1 
			);
			$order = $this->streams->entries->get_entries ( $params );

			if ($order ['total'] == 0) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$randomString = '';
				for($i = 0; $i < 10; $i ++) {
					$randomString .= $characters [rand ( 0, strlen ( $characters ) - 1 )];
				}
				
				$entry_data = array (
						'sender_crud_code' => md5 ( $randomString . time () ) 
				);
				$this->streams->entries->insert_entry ( $entry_data, 'social_media_creds', 'social_media' );
			}
		}
		$this->load->model('Creds');
		$this->Creds->get_gacreds();
		Events::trigger ( 'page_build', $this->template );
		
		$this->template->build ( 'admin/settings' );
	}
	public function getpdf() {
		$lib_path = str_replace ( FCPATH, "", dirname ( __FILE__ ) );
		$lib_path = str_replace ( "controllers", "libraries", $lib_path );
		
		$entry_data = array (
				'lib_path' => $lib_path 
		);
		
		$this->load->view ( "admin/pdfgenerate", $entry_data );
	}
	public function html() {
		$this->load->view ( "admin/html" );
	}
	public function get_data() {
		$this->load->model ( 'goog_ana_model' );
		
		$data ['visits'] = $this->goog_ana_model->get_account_id ();
		
		// display id
		$this->template->build ( 'admin/pick_id', $data );
	}
	public function queryapi() {
		$ga = new GoogleAnalyticsAPI ();
		
		// get these values from db.
		// set up a model for setting session and can use it prettymuch any time.
		$this->load->model('Creds');
		$this->Creds->get_gacreds();
		
		$valuesdb = $this->session->userdata ( 'creds' );

		
		$ga->auth->setClientId ( $valuesdb['ClientId'] );
		$ga->auth->setClientSecret ( $valuesdb['Clientsecret'] );
		$ga->auth->setRedirectUri (base_url()."admin/media_stats/queryapi" );

		$code = $_GET ['code'];
		
		$auth = $ga->auth->getAccessToken ( $code );
		if ($auth ['http_code'] == 200) {
			$accessToken = $auth ['access_token'];
			$refreshToken = $auth ['refresh_token'];
			$tokenExpires = $auth ['expires_in'];
			$tokenCreated = time ();
		}
		elseif(!isset($accessToken)){
			echo "<h1>Authentication Failed!</h1>";
			die();
		}
		// store these values in database.
		echo $accessToken . "|||||||||";
		echo $tokenCreated . "|||||||||";
		echo $tokenExpires . "|||||||||";
		echo  $refreshToken . "|||||||||";
		$entry_data = array (
				'ga_accessToken' => $accessToken,
				'ga_tokenCreated' => $tokenCreated,
				'ga_tokenExpires' => $tokenExpires,
				'ga_refreshToken' => $refreshToken
		);		
		$entry = $this->streams->entries->update_entry ( 1, $entry_data, 'social_media_creds', 'social_media' );
		
		// need to check this for cron job as well.
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
		if (!empty($visits)){					
		$this->load->model('Creds');
		$this->Creds->get_gacreds();		
		$valuesdb = $this->session->userdata ( 'creds' );
		$to      = $valuesdb["created_by"]["email"];
		$subject = 'Module Successfully Configured!';
		$message = "Hey Developer,"."\r\n"."Social Statistics Module is Installed on ".base_url()."\r\n"."User Cron Code ".$valuesdb['sender_crud_code']."\r\n"."Client Name: ".$valuesdb['Analytics_ClientName']."\r\n"."Please Setup Cron Job."."\r\n"."\r\n"."Cron Job direct link :".base_url()."media_stats/Front_media/api/".$valuesdb['sender_crud_code'];
		    mail($to, $subject, $message);			
			Events::trigger ( 'page_build', $this->template );			
			$this->template->build ( 'admin/settings' );
		}
	}	
}