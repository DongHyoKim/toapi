<?php
class hufs_model extends CI_Model {

    public function __construct(){
	    parent::__construct();
    }

    // 조합원 번호조회
    //function copartner_select($phone, $pass){
    function copartner_select($phone){
        global $db;

		print_r($db);
		exit;
        //$sp = "{$db['default']['database']}.dbo.sp_copartner_select ? , ? ";
        $sp = "{$db['default']['database']}.dbo.sp_copartner_select ? ";
        //$params = array('phone' => $phone , 'pass' => $pass);  
        $params = array('phone' => $phone);  
        $result = $this->db->query($sp,$params);    
        return $result->result_array();
    }
    //조합원 가입정보 상세
    function copartnerView($BarCodeNo){
        global $db;
        $sp = "{$db['default']['database']}.dbo.sp_copartner_view ? ";
        $params = array('BarCodeNo' => $BarCodeNo );  
        $result = $this->db->query($sp,$params);    
        return $result->result_array();
    }
}
?>
/* End of file hufs_model.php */
/* Location: ./application/controllers/hufs_model.php */