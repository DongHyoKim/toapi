<?php
class Alimtalk_model extends CI_Model {

    public function __construct(){
            parent::__construct();
            //print_r($this);
            $this->mysql = $this->load->database('mysql', TRUE);
    }

    /**
     * insertAlimtalkLog function
     *
     * @param [type] $params
     *
     * @return 1 , 0 
     */
    function insertAlimtalkLog($params) 
    {                         
		
        $sql = "insert into tb_kakao_alimtalk_log set 
                                                send_from = 'api',
												mtype = ?,												
												sender_key = ?, 
                                                univcode = ? ,  
												sub_univcode = ? ,     
                                                phone = ?, 
												template_code = ?, 
                                                user_key = ? ,
                                                msg = ?, 
												auth_no = ?, 
                                                result_status = ?, 
                                                result_code = ?, 
												result_alt_code = ?, 
                                                result = ?, 
                                                time_create = now()                            
                                                ";
        $this->mysql->trans_start();
        $this->mysql->query($sql,array(        
									$params['mtype'],									
									$params['sender_key'],
                                    $params['univcode'],
									$params['sub_univcode'],
                                    $params['phone'],
									$params['template_code'],
                                    $params['user_key'],
                                    $params['msg'],
									$params['auth_no'],
                                    $params['result_status'],
                                    $params['result_code'],
									$params['result_alt_code'],			
                                    $params['altMsg']
        ));
        $this->mysql->trans_complete();
        return $this->mysql->trans_status()==TRUE?1:0;        
    }

	 /**
     * getAlimtalkLog function
     *
     * @param [type] $params
     *
     * @return single row
     */
    function getAlimtalkLog($params)
    {
        $sql = "select auth_no, time_create  from tb_kakao_alimtalk_log where mtype='auth' and univcode = ? and user_key = ? order by id desc limit 0, 1 ";
        $query = $this->mysql->query($sql,array($params['univcode'], $params['user_key']));

        //echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $arr = $query->row_array();
        } else {
            $arr = null;
        }
        return $arr;
    }

}
  