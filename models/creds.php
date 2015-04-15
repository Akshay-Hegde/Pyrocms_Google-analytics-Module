<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Creds extends MY_Model
{
	public function get_gacreds()
	{
		//refactor this template to get all the creds for social media. Call the method once 
		//you should be able to have all the values required from database.
		$this->session->unset_userdata('creds');
		$this->load->driver('Streams');
		$params	 = array(
			'stream' 	=> 'social_media_creds',
			'namespace'	=> 'social_media',
			'where'		=> SITE_REF."_ntsocial_media_creds.id = 1",
			'limit'		=> 1
		);
		$creds = $this->streams->entries->get_entries($params);
		if(isset($creds['entries'][0]))
		{
			$this->session->unset_userdata('creds');
		$this->session->set_userdata('creds',$creds['entries'][0]);
		}
		else {
			$this->session->unset_userdata('creds');
			$this->session->set_userdata('creds',"Not set");
		}
			
	}
}
