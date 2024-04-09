<?php
class Api_model extends CI_Model {

    public function __construct(){
        parent::__construct();        
    }

    //전체 마일리지 조회
    function getTotalMileage($saletotal_member) {
        global $db;
        $sp = "{$db['default']['database']}.dbo.sp_web_totalmileage ? ";
        $params = array('saletotal_member' => $saletotal_member);  
        $result = $this->db->query($sp,$params);    
        return $result->result_array();
    }

    //마일리지 insert 
    function insertSaleTotalMileage($params){

		global $db;

        $sp = "[CPT".$params['UNIVCODE'].$params['SUBUNIVCODE'].".[dbo].[sp_service_pos_cptmilins] ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ";

		$this->db->query($sp,array($params['BARCODENO'],
			                       $params['USEDATE'],
     			                   $params['DEPTCODE'],
			                       $params['POSNO'],
			                       $params['BILLNUMBER'],
			                       $params['USEMILEAGE'],
			                       $params['AMOUNT'],
			                       $params['AMOUNTSAVE'],
			                       $params['REMARK'],
			                       $params['UNIVCODE'])); 
        
		return  $this->db->affected_rows();    
    }

/* End of file api_model.php */
/* Location: ./application/models/api_model.php */