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

        $query = "SELECT [".DBNAME."].[DBO].[FUN_TORDERSETTING] ('".$code_type."','".OFFER."','".$receive_storecode."') AS univstore_code;";
        //echo($query);
        //exit;

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

        $query = "SELECT CODETYPE,OFFERCODE,CWAYCODE,CODENAME FROM [".DBNAME."].[DBO].[TOORDERSETTING] WHERE CUSTCODE = '".OFFER."' AND CODETYPE <> '01';";
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
        $this->db->insert('['.DBNAME.'].[DBO].[TOORDER]', $baseParams);  // 주문정보
        foreach($productParams as $value){
            $this->db->insert('['.DBNAME.'].[DBO].[TOORDERPRODUCT]', $value);  // 주문상품정보
        }
        unset($value);
        foreach($paymentParams as $value){
            $this->db->insert('['.DBNAME.'].[DBO].[TOORDERPAYMENT]', $value);  // 주문결제정보
        }
        unset($value);
        foreach($cardParams as $value){
            $this->db->insert('['.DBNAME.'].[DBO].[TOORDERPAYMENTCARD]', $value);  // 결제상세 카드정보
        }
        unset($value);
        foreach($cashParams as $value){
            $this->db->insert('['.DBNAME.'].[DBO].[TOORDERPAYMENTCASH]', $value);  // 결제상세 현금영수증정보
        }
        $this->db->insert('['.DBNAME.'].[DBO].[TOORDERLOG]', $logParams);  // 주문로그
        // transaction end
		$this->db->trans_complete();

	    //echo $this->db->last_query();
	    //print_r($arr);
        //exit;
      
        return $this->db->trans_status()? "0000" : -1;
    }   

    function delete2Order($params)
    {
	    global $db;
        $db['default']['database'] = DBNAME;

        $query = "SELECT  count(STORECODE) as CNT FROM [".DBNAME."].[DBO].[TOORDER] 
                  WHERE   UNIVCODE = '".$params['UNIVCODE']."' AND SUBUNIVCODE = '".$params['SUBUNIVCODE']."' 
                  AND     SALEDATE = '".$params['SALEDATE']."' AND STORECODE = '".$params['STORECODE']."';";
        $results = $this->db->query($query);

	    if ($results){
            $arr = $results->result_array();
        } else {
            $arr = null;   
        }
	    //echo $this->db->last_query();
        //echo("Count:".$arr['0'][CNT]);
	    //print_r($arr);
        //exit;
        if($arr['0']['CNT'] != 0){
            // transaction start
		    $this->db->trans_start();
            $this->db->delete('['.DBNAME.'].[DBO].[TOORDER]', $params);
            $this->db->delete('['.DBNAME.'].[DBO].[TOORDERPRODUCT]', $params);
            $this->db->delete('['.DBNAME.'].[DBO].[TOORDERPAYMENT]', $params);
            $this->db->delete('['.DBNAME.'].[DBO].[TOORDERPAYMENTCARD]', $params);
            $this->db->delete('['.DBNAME.'].[DBO].[TOORDERPAYMENTCASH]', $params);
            $this->db->delete('['.DBNAME.'].[DBO].[TOORDERLOG]', $params);
            // transaction end
		    $this->db->trans_complete();

	        //echo $this->db->last_query();
	        //print_r($arr);
            //exit;
            return $this->db->trans_status()? "0000" : -1;
        } else {
            return "0000";
        }
        
    }

}
/* End of file daumodel.php */
/* Location: ./application/models/daumodel.php */