<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Table_data_model
 *
 * @author Bob
 */
class Table_data_model extends MY_Model {
    
    
    //put your code here
    //Stream Stuff to pull data 
    Function Retrieve() {
       
      // $query = $this->db->query("SELECT total_visit from default_nt_google_analytics");
      // return $query->result();
      $this->load->driver('Streams');
      //$this->streams->entries->function();
      
      $month = date('m');
      
      $params = array(
        'stream'        => 'google_analytics',
        'month'         => $month,
        'sort'          => 'asc',
        'namespace'     => 'social_media',
          
      );
 
        $entries = $this->streams->entries->get_entries($params);
        return $entries;
       //$ret = $query->row();
       //return $ret->total_visit;

        //return $records;
    }
    Function RetrieveByDate($startDate,$endDate) {
     $this->load->driver('Streams');
      //$this->streams->entries->function();
    // die($startDate);
     
     $params = array(
       'stream'        => 'google_analytics',
       'namespace'     => 'social_media',
       'where'         => "default_nt_google_analytics.created >= \"$startDate\" AND default_nt_google_analytics.created  <= \"$endDate\"",
       
             );
             //print_r($params);
       $entries = $this->streams->entries->get_entries($params);
       return $entries;
    }
    
    //Return(stuff from database)
}
