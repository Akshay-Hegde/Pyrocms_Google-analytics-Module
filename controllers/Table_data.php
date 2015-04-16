<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Table_data extends Public_Controller{
    
    
    function index()
    {
        echo 'HI';
    }
    function passData()
    {
        $this->load->model ( 'Table_data_model' );
         
        $data = $this->Table_data_model->Retrieve ();
        
      // echo "<pre>";
      // print_r($data) ;
       // die(json_encode($data));
       $this->template->build_json($data);
    }
    
    function recieveData()
    {
        $this->load->model ( 'Table_data_model' );
        $startDate = $this->input->post('startDate');
       
        $endDate = $this->input->post('endDate');

         
        //echo("Startdate is " . $startDate . " and endDate is " . $endDate);
       
        $data = $this->Table_data_model->RetrieveByDate ($startDate,$endDate);
        //echo("Startdate is " . $startDate . " and endDate is " . $endDate);
        $this->template->build_json($data);
    
    }
}

?>
