<?php
class Jnu_book_model extends CI_Model {

    public function __construct(){
	    parent::__construct();
		//$this->load->database('options', true);
		//$this->CI =& get_instance();
    }

  function execSp($UnivCode, $dbName, $spName, $params_array) {
      
	  global $db;

	  //print_r($db);
	  //exit;

      $db['default']['database'] = $dbName;
      $sp = "[".$dbName."].[dbo]."."[".$spName."]";
      $params = "";

	  //echo $sp;
	  //echo $dbName;
      //echo count($params_array);
	  //print_r($params_array);
	  //exit;

      $i = 0;
	  foreach($params_array as $k => $v){
		  //if(!$v) $params_array[$k] = " ";
		  if($i == count($params_array) - 1){
		      $params .= '"'.$v.'"';
		  } else {
	  		  $params .= '"'.$v.'",';
		  }
		  $i++;
	  }
	  
      //echo $params;
	  //exit;

	  /*
      foreach($params_array as $key => $value){
          $params .= '"'.$value.'"';
		  if($key <> "card_no") $params .= ',';
	  }
      */	  
	  $sp = $sp." ".$params;
	  $results = $this->db->query($sp);

	  if ($results) {
          $arr = $results->result_array();
      } else {
          $arr = null;   
      }
	  //echo $this->db->last_query();
	  //print_r($arr['0']);
      //exit;
      
	  return $arr['0'];
  }

  function countbook($UNIVCODE, $dbName, $spName, $params) {

	  global $db;
      
      $db['default']['database'] = $dbName;
      $sp = "[".$dbName."].[dbo].[".$spName."] ?,?,?,?,?";
	  $results = $this->db->query($sp,array($params['UNIVCODE'],
			                       $params['STORECODE'],
     			                   $params['ISBN'],
			                       $params['BOOKNAME'],
			                       $params['AUTHORNAME'])); 
	  if ($results) {
          $arr = $results->result_array();
      } else {
          $arr = null;   
      }
	  //echo $this->db->last_query();
	  //print_r($arr['0']['CNT']);
	  //exit;
      
	  return $arr['0']['CNT'];
  }

  function selectbook($UNIVCODE, $dbName, $spName, $params) {

	  global $db;

      $db['default']['database'] = $dbName;
      $sp = "[".$dbName."].[dbo].[".$spName."] ?,?,?,?,?,?,?";
	  $results = $this->db->query($sp,array($params['UNIVCODE'],
			                       $params['STORECODE'],
     			                   $params['ISBN'],
			                       $params['BOOKNAME'],
			                       $params['AUTHORNAME'],
                            	   $params['PAGE_ROW'],
		                           $params['PAGE_NO']));
	  if ($results) {
          $arr = $results->result_array();
      } else {
          $arr = null;
      }
	  //echo $this->db->last_query();
	  //print_r($arr);
	  //exit;

	  return $arr;
  }

}



/* End of file jnu_book_model.php */
/* Location: ./application/models/jnu_book_model.php */
?>