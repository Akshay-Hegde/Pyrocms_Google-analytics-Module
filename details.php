<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * social Media Module
 *
 * @author Dinesh Devkota
 * @package
 *
 * @link http://www.dineshdevkota.com
 *      
 */
// admin
class Module_Media_stats extends Module
{

    public $version = '0.2';

    public function __construct()
    {
        parent::__construct();
        // load the streams driver
        $this->load->driver('Streams');
        set_time_limit(60);
    }

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Statistics Module'
            ),
            'description' => array(
                'en' => 'Module Made by Senior Citizens.'
            ),
            'frontend' => False,
            'backend' => TRUE,
            'menu' => 'content',
            'skip_xss' => true,
            'shortcuts' => array(
                array(
                    'name' => 'media_stats:API',
                    'uri' => 'admin/media_stats/',
                    'class' => 'add'
                )
            )
        ); // You can also place modules in their top level menu. For example try: 'menu' => 'Sample',

        
    }

    public function install()
    {
        // remove all tables if they exist already
        $this->streams->utilities->remove_namespace('social_media');
        // add our new stream( table) for API creds
        $this->streams->streams->add_stream('social_media_creds', 'social_media_creds', 'social_media', "nt", "Stream used for the creds if Social Media of USER to consume API.");
        $this->streams->streams->add_stream('google_analytics', 'google_analytics', 'social_media', "nt_", "Table for Google Analytics");
        
        $credsfield = array(
            array(
                'name' => 'Google Ana Accesstoken',
                'slug' => 'ga_accessToken',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            ),
            array(
                'name' => 'Google Ana tokenCreated',
                'slug' => 'ga_tokenCreated',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            ),
            array(
                'name' => 'Google Ana tokenExpires',
                'slug' => 'ga_tokenExpires',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            ),
            array(
                'name' => 'Google Ana refreshToken',
                'slug' => 'ga_refreshToken',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            ),
            array(
                'name' => 'sende crud code',
                'slug' => 'sender_crud_code',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            ),
            array(
                'name' => 'ClientId',
                'slug' => 'ClientId',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            ),
            array(
                'name' => 'Clientsecret',
                'slug' => 'Clientsecret',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            ),
            array(
                'name' => 'Analytics ClientId',
                'slug' => 'Analytics_ClientId',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            ),
            array(
                'name' => 'Analytics ClientName',
                'slug' => 'Analytics_ClientName',
                'namespace' => 'social_media',
                'type' => 'text',
                'assign' => 'social_media_creds',
                'required' => false
            )
        )
        ;
        $this->streams->fields->add_fields($credsfield);
        /* Begin Google Analytics Fields */
        $fieldsb = array(
            array(
                'name' => 'Raw Data',
                'slug' => 'raw_data',
                'namespace' => 'social_media',
                'type' => 'textarea',
                'assign' => 'google_analytics',
                'required' => false
            ),
            array(
                'name' => 'Total Visit Days',
                'slug' => 'total_visit',
                'namespace' => 'social_media',
                'type' => 'integer',
                'assign' => 'google_analytics',
                'required' => false
            ),
            array(
                'name' => 'Visits By Day Facebook',
                'slug' => 'vbd_fb',
                'namespace' => 'social_media',
                'type' => 'integer',
                'assign' => 'google_analytics',
                'required' => false
            ),
            array(
                'name' => 'Visits By Day Google',
                'slug' => 'vbd_google',
                'namespace' => 'social_media',
                'type' => 'integer',
                'assign' => 'google_analytics',
                'required' => false
            ),
            array(
                'name' => 'Visits By Day Reddit',
                'slug' => 'vbd_reddit',
                'namespace' => 'social_media',
                'type' => 'integer',
                'assign' => 'google_analytics',
                'required' => false
            ),
            array(
                'name' => 'Visits By Day Twitter',
                'slug' => 'vbd_Twitter',
                'namespace' => 'social_media',
                'type' => 'integer',
                'assign' => 'google_analytics',
                'required' => false
            ),
            array(
                'name' => 'Visits By Day Bing',
                'slug' => 'vbd_bing',
                'namespace' => 'social_media',
                'type' => 'integer',
                'assign' => 'google_analytics',
                'required' => false
            ),
            array(
                'name' => 'Visits By Day Pininterest',
                'slug' => 'vbd_pininterest',
                'namespace' => 'social_media',
                'type' => 'integer',
                'assign' => 'google_analytics',
                'required' => false
            ),
            array(
                'name' => 'Visits By Day Linkedin',
                'slug' => 'vbd_linkedin',
                'namespace' => 'social_media',
                'type' => 'integer',
                'assign' => 'google_analytics',
                'required' => false
            )
        );
        $this->streams->fields->add_fields($fieldsb);
        
        // We made it!
        return TRUE;
    }

    public function uninstall()
    { // Remove all tables with this namespace.
        $this->streams->utilities->remove_namespace('social_media');
        // Put a check in to see if something failed, otherwise it worked
        return TRUE;
    }

    public function upgrade($old_version)
    {
        // Your Upgrade Logic
        return TRUE;
    }

    public function help()
    {
        // Return a string containing help info
        return "Here you can enter HTML with paragrpah tags or whatever you like";
        // You could include a file and return it here.
        return $this->load->view('help', NULL, TRUE); // loads modules/sample/views/help.php
    }
}

?>