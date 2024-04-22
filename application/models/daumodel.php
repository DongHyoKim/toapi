<?php
class Daumodel extends CI_Model {

    public function __construct(){
	    parent::__construct();
		//$this->load->database('options', true);
		//$this->CI =& get_instance();
    }

    function getStorecode($code_type,$receive_storecode)
    {
	    global $db;
        $db['default']['database'] = DBNAME;

        $query = "SELECT DBO.FUN_TORDERSETTING('".$code_type."','".OFFER."','".$receive_storecode."') AS storeCode;";
        $results = $this->db->query($query);

	    if ($results)
        {
            $arr = $results->result_array();
        }
        else
        {
            $arr = null;   
        }
	    //echo $this->db->last_query();
	    //print_r($arr['0']);
        //exit;
      
	    return $arr['0'];
    }

    function getStandardcode()
    {
	    global $db;
        $db['default']['database'] = DBNAME;

        $query = "SELECT CODETYPE,OFFERCODE,CWAYCODE,CODENAME FROM TOORDERSETTING WHERE CUSTCODE = '".OFFER."' AND CODETYPE <> '01';";
        $results = $this->db->query($query);

	    if ($results)
        {
            $arr = $results->result_array();
        }
        else
        {
            $arr = null;   
        }
	    //echo $this->db->last_query();
	    //print_r($arr);
        //exit;
      
	    return $arr;
    }        

    function save2Order($baseParams,$productParams,$paymentParams,$cardParams,$cashParams,$logParams)
    {
	    global $db;
        $db['default']['database'] = DBNAME;

        // transaction start
		$this->db->trans_start();
        $this->db->insert('['.DBNAME.'.[DBO].[TOORDER]', $baseParams);  // 주문정보
        foreach($productParams as $value){
            $this->db->insert('['.DBNAME.'.[DBO].[TOORDERPRODUCT]', $value);  // 주문상품정보
        }
        unset($value);
        foreach($paymentParams as $value){
            $this->db->insert('['.DBNAME.'.[DBO].[TOORDERPAYMENT]', $value);  // 주문결제정보
        }
        unset($value);
        foreach($cardParams as $value){
            $this->db->insert('['.DBNAME.'.[DBO].[TOORDERPAYMENTCARD]', $value);  // 결제상세 카드정보
        }
        unset($value);
        foreach($cashParams as $value){
            $this->db->insert('['.DBNAME.'.[DBO].[TOORDERPAYMENTCASH]', $value);  // 결제상세 현금영수증정보
        }
        $this->db->insert('['.DBNAME.'.[DBO].[TOORDERLOG]', $logParams);  // 주문로그
        // transaction end
		$this->db->trans_complete();

	    //echo $this->db->last_query();
	    //print_r($arr);
        //exit;
      
        return $this->db->trans_status()? "0000" : -1;
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



/* End of file daumodel.php */
/* Location: ./application/models/daumodel.php */