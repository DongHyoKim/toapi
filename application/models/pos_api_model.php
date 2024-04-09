<?php
class Pos_api_model extends CI_Model {

public function __construct(){
		parent::__construct();
		//print_r($this);
}
  
 //전체 마일리지 조회
 function getTotalMileage($arr){
    global $db;
	
    //$sp = "{$db['default']['database']}.dbo.sp_Service_ExPos_CptSel ? ";
    $sp = "[CPT".$arr['UNIVCODE'].$arr['SUBUNIVCODE']."].[dbo].[sp_Service_ExPos_CptSel] ? ";

    $params = array('saletotal_member' => $arr['saletotal_member']);  

    $result = $this->db->query($sp,$params);    
    return $result->result_array();
 }

 //전체 조합원여부 확인 : 조합원번호,이름,포인트잔액
 function getCopartnerMember($params){
    global $db;
    $sp = "[CPT".$params['UNIVCODE'].$params['SUBUNIVCODE']."].dbo.sp_service_pos_cptsel ?, ?, ? ";
    $result = $this->db->query($sp,$params);    
    return $result->result_array();
 }  
  
  //마일리지 insert 
  function insertSaleTotalMileage($params){

    global $db;

    $sp = "[CPT".$params['UNIVCODE'].$params['SUBUNIVCODE']."].[dbo].[sp_service_pos_cptmilins] ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ";

    $result = $this->db->query($sp,array($params['BARCODENO'],
			                       $params['USEDATE'],
     			                   $params['DEPTCODE'],
			                       $params['POSNO'],
			                       $params['BILLNUMBER'],
			                       $params['USEMILEAGE'],
			                       $params['AMOUNT'],
			                       $params['AMOUNTSAVE'],
			                       $params['REMARK'],
			                       $params['UNIVCODE'])); 
    if ($result) {
//		return  $this->db->affected_rows();    
        return 1;
	} else {
		retrun -1;
	}
  }

  function insertMileage($UnivCode, $saletotal_date, $saletotal_store, $saletotal_posid, $saletotal_billnumber, $saletotal_member,  $mileage_type , $amount) {

	global $db;
    $this->db->trans_start();
    $sp = "VENDINGM.dbo.sp_MileageInsert ?, ?, ?, ?, ?, ?, ?, ? ";
    $params = array(
                'UnivCode'              => $UnivCode,
                'saletotal_date'        => $saletotal_date,
                'saletotal_store'       => $saletotal_store,
                'saletotal_posid'       => $saletotal_posid,
                'saletotal_billnumber'  => $saletotal_billnumber,
                'saletotal_member'      => $saletotal_member,
	            'mileage_type'          => $mileage_type,
                'amount'                => $amount,
              );  
    $this->db->query($sp,$params); 
    $this->db->trans_complete();	

    return  $this->db->trans_status()==TRUE?1:-1;    
  }

    //마일리지 insert2
    function insertExposMileage($params)
	{

        global $db;

		$sp = "[CPT".$params['UNIVCODE'].$params['SUBUNIVCODE']."].[dbo].[SP_SERVICE_EXPOS_CPTINS] ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ";

        $result = $this->db->query($sp,array($params['UNIVCODE'],
		                               $params['BARCODENO'],
			                           $params['USEDATE'],
     			                       $params['DEPTCODE'],
			                           $params['POSNO'],
			                           $params['BILLNUMBER'],
			                           $params['USETYPE'],
			                           $params['SALETYPE'],
			                           $params['USEMILEAGE'],
    			                       $params['AMOUNT'],
	    		                       $params['AMOUNTSAVE'],
		    	                       $params['REMARK']));
        if ($result) {
		    //return  $this->db->affected_rows();
            return 1;
    	} else {
	    	retrun -1;
	    }
    }

    // 조합원 번호조회
    //function copartner_select($phone, $pass){
    function copartner_select($arr){

        global $db;


        //$sp = "{$db['default']['database']}.dbo.sp_copartner_select ? , ? ";
        //$sp = "{$db['default']['database']}.dbo.sp_copartner_select ? ";

        $sp = "[CPT".$arr['UNIVCODE'].$arr['SUBUNIVCODE']."].[dbo].[sp_coparter_select] ? ";

        $params = array('phone' => $arr['phone']);  

        //$params = array('phone' => $phone , 'pass' => $pass);  
        //$params = array('phone' => $phone);  

        $result = $this->db->query($sp,$params); 

        return $result->result_array();
    }

	//조합원 가입정보 상세
    function copartner_view($arr)
	{
        global $db;

        //print_r($arr);
		//exit;

		$sp = "[CPT".$arr['UNIVCODE'].$arr['SUBUNIVCODE']."].[dbo].[sp_copartner_view] ? ";
        //$sp = "{$db['default']['database']}.dbo.sp_copartner_view ? ";

        $params = array('barcodeno' => $arr['barcodeno']);  
        $result = $this->db->query($sp,$params);    
        return $result->result_array();
    }

  function execQuerry($UnivCode, $dbName, $spName, $params_array) {
      
	  global $db;

	  //print_r($db);
	  //exit;

      $db['default']['database'] = $dbName;
      
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

	  $sp = $sp." ".$params;

      //$sp = '[CPT00116001].[dbo].[sp_service_pos_cptsel] " ","0610","00116","001"';
	  //echo $sp;
	  //exit;

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
?>
