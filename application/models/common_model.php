<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model {
	
	/** @var CI object */
	private $CI = null;

	/** 
	 * __construct
	 *
	 * @param Object $CI
	 */
	public function __construct()
	{
		parent::__construct();
		$this->CI =& get_instance();
	}

    /** 
	 * __destruct
	 *
	 * @description masterDb , slaveDb close
	 */
	public function __destruct()
    {
		if (class_exists('CI_DB') AND isset($this->CI->default) AND ! empty($this->CI->default->conn_id))
		{
			$this->CI->default->close();
		}
    }

    function execSp($UnivCode, $dbName, $spName, $params_array) {
      

		//$this->load->database('default', TRUE);
		global $db;
      
		print_r($db);
	    exit;
		//echo("UnivCode=".$UnivCode);
		//echo("dbName=".$dbName);
		//echo("spName=".$spName);
		//print_r($params_array);
	    //exit;

        $db['default']['database'] = $dbName;
		print_r($db);
	    exit;

	  

	    $sp = "[".$dbName."].[dbo]."."[".$spName."]";
        $params = "";
	    for($i = 0;$i < count($params_array);$i++){
		    if(!$params_array[$i]) $params_array[$i] = " ";
		    if($i == count($params_array) - 1){
		        $params .= '"'.$params_array[$i].'"';
    	    } else {
	  		    $params .= '"'.$params_array[$i].'",';
		    }
	    }
        echo('now');
	    $sp = $sp." ".$params;

        //$sp = '[CPT00116001].[dbo].[sp_service_pos_cptsel] " ","0610","00116","001"';
	    echo $sp;
	    exit;

	    $results = $this->db->query($sp);

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