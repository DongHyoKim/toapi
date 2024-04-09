<?php
class Family_app_api_model extends CI_Model {

  public function __construct(){
		parent::__construct();
	}
  
  //조합원 가입정보
  function copartnerAutoJoin($eMail, $Pass){
    global $db;
    $sp = "{$db['default']['database']}.dbo.sp_CopartnerAutoJoin ? , ? ";
    $params = array('eMail' => $eMail , 'Pass' => $Pass);  
    $result = $this->db->query($sp,$params);    
    return $result->result_array();
  }

  //조합원 가입정보
  function copartnerConfirm($eMail, $Pass , $Phone ){
    global $db;
    $sp = "{$db['default']['database']}.dbo.sp_CopartnerConfirm ? , ? , ?";
    $params = array('eMail' => $eMail , 'Pass' => $Pass , 'Phone' => $Phone );  
    $result = $this->db->query($sp,$params);    
    return $result->result_array();
  }
  
  //조합원 가입정보 상세
  function copartnerView($BarCodeNo){
    global $db;
    $sp = "{$db['default']['database']}.dbo.sp_CopartnerView ? ";
    $params = array('BarCodeNo' => $BarCodeNo );  
    $result = $this->db->query($sp,$params);    
    return $result->result_array();
  }

  //조합원 가입정보 상세1
  function copartnerViewDetail1($BarCodeNo){
    global $db;
    $sp = "{$db['default']['database']}.dbo.sp_CopartnerDetailView1 ? ";
    $params = array('BarCodeNo' => $BarCodeNo );  
    $result = $this->db->query($sp,$params);        
    $array = $result->result_array(); 
    
     /*
		foreach($array as $i=>$row){		
			$array[$i]['FundDate'] = $row['FundDate'];
			$array[$i]['FundName'] = $row['FundName'];
			$array[$i]['Accountnum'] = $row['Accountnum'];
      $array[$i]['Amount'] = $row['Amount'];
		}*/
    return $array;
  }

  //조합원 가입정보 상세2
  function copartnerViewDetail2($UnivCode , $BarCodeNo){
    global $db;
    $sp = "{$db['default']['database']}.dbo.sp_CopartnerDetailView2 ? , ?  ";
    $params = array('UnivCode' => $UnivCode , 'BarCodeNo' => $BarCodeNo );  
    $result = $this->db->query($sp,$params);    
    return $result->result_array();
  }
  
}
?>
