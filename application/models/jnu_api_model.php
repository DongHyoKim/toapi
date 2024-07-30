<?php
class Jnu_api_model extends CI_Model {

    public function __construct(){
        parent::__construct();
	    //$this->load->database('options', true);
	    //$this->CI =& get_instance();
    }

    function getIdbyphone($univcode, $input_data){
      
	    global $db;
	  
	    $db['default']['database'] = "CPT00116001";

        $middle_int = 3;
	    if(strlen($input_data) == 11) $middle_int = 4;
	    $hpno1 = substr($input_data,0,3);
        $hpno2 = substr($input_data,3,$middle_int);
        $hpno3 = substr($input_data,3+$middle_int,4);

        $query = "SELECT id,name,barcodeno FROM [CPT00116001].[dbo].[Copartner] WHERE Status IN ('Y', 'H', 'Z') AND HPNo1 = ? AND HPNo2 = ? AND HPNo3 = ?";
		$row   = [];
        $results = $this->db->query($query, [$hpno1,$hpno2,$hpno3]);
        if ($results->num_rows() > 0) $row = $results->row_array();
	    //echo $this->db->last_query();
		//exit;
	    return $row;
    }

    function getIdbybarcodeno($univcode, $input_data){
      
	    global $db;
	  
	    $db['default']['database'] = "CPT00116001";

        $query = "SELECT id,name,barcodeno FROM [CPT00116001].[dbo].[Copartner] WHERE Status IN ('Y', 'H', 'Z') AND BarcodeNo = ?";
		$row   = [];
        $results = $this->db->query($query, [$input_data]);
        if ($results->num_rows() > 0) $row = $results->row_array();
	    //echo $this->db->last_query();
		//exit;
	    return $row;
    }
    
    function getBalancebyid($univcode, $id){

	    global $db;
	  
	    $db['default']['database'] = "CPT00116001";

		
		$query = "SELECT ISNULL(SUM(MILEAGE), 0) AS MILEAGE FROM MILEAGE WHERE BARCODENO
		          IN ( SELECT NEWBARCODENO FROM BARCODEHISTORY WHERE BARCODENO 
				  IN ( SELECT BARCODENO FROM COPARTNER WHERE ID = ? AND Status IN ('Y', 'H', 'Z') ) ) AND Status = 'Y'";

		//$spName = "sp_web_totalmileage";
		//$sp     = "[CPT00116001].[dbo]."."[".$spName."] ?";
		$row   = [];
	    $results = $this->db->query($query, [$id]);
        if ($results->num_rows() > 0) $row = $results->row_array();
		//echo $this->db->last_query();
        //exit;
      
	    return $row['MILEAGE'];
	}

    function getBalancebybarcodeno($univcode, $barcodeno){

	    global $db;
	  
	    $db['default']['database'] = "CPT00116001";
		
		$query = "SELECT ISNULL(SUM(MILEAGE), 0) AS MILEAGE FROM MILEAGE WHERE BARCODENO
		          IN ( SELECT NEWBARCODENO FROM BARCODEHISTORY WHERE BARCODENO 
				  IN ( SELECT BARCODENO FROM COPARTNER WHERE BarcodeNo = ? AND Status IN ('Y', 'H', 'Z') ) ) AND Status = 'Y'";

		$row   = [];
	    $results = $this->db->query($query, [$barcodeno]);
        if ($results->num_rows() > 0) $row = $results->row_array();
		//echo $this->db->last_query();
        //exit;
      
	    return $row['MILEAGE'];
	}

	function insertMileage($params) {
      
		global $db;
 
		$db['default']['database'] = "CPT".$params['univcode'].$params['subunivcode'];
		$sp ="CPT".$params['univcode'].$params['subunivcode'].".[dbo].[SP_SERVICE_POS_CPTMILINS]";
  
		$i = 0;
		$params_str = '';
		foreach($params as $k => $v){
			//if(!$v) $params_array[$k] = " ";
			if($i == count($params) - 1){
				$params_str .= '"'.$v.'"';
			} else {
				  $params_str .= '"'.$v.'",';
			}
			$i++;
		}
		//echo $params_str;
		//exit;

		$sp = $sp." ".$params_str;
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

}
/* End of file jnu_api_model.php */
/* Location: ./application/models/jnu_api_model.php */
?>