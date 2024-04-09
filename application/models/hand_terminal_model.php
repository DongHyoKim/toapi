<?php
class Hand_terminal_model extends CI_Model {

    public function __construct(){
	    parent::__construct();
		//$this->load->database('options', true);
		//$this->CI =& get_instance();
    }

    function user_check($params){
      
	    global $db;

        $db['default']['database'] = $params['dbname'];
	  
        $query = "SELECT CASE (SELECT 1 FROM U_BASIC.dbo.PERSON WHERE UNIVCODE = ? AND SUBUNIVCODE = ? AND PSID = ? AND PSPASSWORD = ?) 
		          WHEN 1 THEN 1 ELSE 0 END AS Result";
		$row   = 0;

        $results = $this->db->query($query, array($params['UNIVCODE'],
		                                          $params['CAMPUSCODE'],
                                     		      $params['USERID'],
                         		                  $params['PASS']));
        if ($results->num_rows() > 0){
            $row = $results->row_array();
        }
	    //echo $this->db->last_query();
		//exit;
	    return $row['Result'];
    }

    function user_check_idpw($params){
      
	    global $db;

        $db['default']['database'] = $params['dbname'];
	  
        $query = "SELECT CASE (SELECT 1 FROM U_BASIC.dbo.USERTABLE WHERE UNIVCODE = ? AND USERID = ? AND USERPASS = ?) 
		          WHEN 1 THEN 1 ELSE 0 END AS Result";
		$row   = 0;

        $results = $this->db->query($query, array($params['UNIVCODE'],
                                     		      $params['USERID'],
                         		                  $params['PASS']));
        if ($results->num_rows() > 0){
            $row = $results->row_array();
        }
	    //echo $this->db->last_query();
		//exit;
	    return $row['Result'];
    }

    function get_store_info($params){
      
	    global $db;

        $db['default']['database'] = $params['dbname'];
	  
        $query = "SELECT u.SUBUNIVCODE,u.USERNAME,u.STORECODE,u.BOOKTYPE,
		          (select DEPTNAME FROM U_BASIC.dbo.DEPARTMENT d WHERE d.UNIVCODE = u.UNIVCODE AND d.SUBUNIVCODE = u.SUBUNIVCODE and d.DEPTCODE = u.STORECODE) AS STORENAME FROM U_BASIC.dbo.USERTABLE u WHERE u.UNIVCODE = ? AND u.USERID = ? AND u.USERPASS = ?";

        $results = $this->db->query($query, array($params['UNIVCODE'],
                                     		      $params['USERID'],
                         		                  $params['PASS']));
        if ($results->num_rows() > 0){
            $row = $results->row_array();
        }
	    //echo $this->db->last_query();
		//exit;
	    return $row;
    }

	function get_store_name($params){
      
	    global $db;

        $db['default']['database'] = $params['dbname'];
	  
        $query = "SELECT DEPTNAME FROM U_BASIC.dbo.DEPARTMENT WHERE UNIVCODE = ? AND SUBUNIVCODE = ? AND DEPTCODE = ?";

        $results = $this->db->query($query, array($params['UNIVCODE'],
                                     		      $params['SUBUNIVCODE'],
                         		                  $params['STORECODE']));
        if ($results->num_rows() > 0){
            $row = $results->row_array();
        }
	    //echo $this->db->last_query();
		//exit;
	    return $row;
    }

    function insert_silsa($params) {

	    global $db;
      
        $db['default']['database'] = $params['dbname'];
        $query = "INSERT INTO U_BOOK.dbo.SILSA_TEMP SELECT ?,?,?,?,?,?,?,?,?";


        // transaction start
		$this->db->trans_start();
	    $results = $this->db->query($query,array($params['UNIVCODE'],
                                                 $params['SUBUNIVCODE'],
			                                     $params['STORECODE'],
			                                     $params['SILSADATE'],
     			                                 $params['BOOKCODE'],
			                                     $params['SILSAQTY'],
			                                     $params['SILSATYPE'],
			                                     $params['INSERTDATE'],
			                                     $params['HANDID']));
        // transaction end
		$this->db->trans_complete();

	    //echo $this->db->last_query();
		return $this->db->trans_status()? 1 : 0;
	}

    function insert_silsa_multi($params) {

	    global $db;
      
        $db['default']['database'] = $params['dbname'];
        $q = "INSERT INTO U_BOOK.dbo.SILSA_TEMP VALUES ";
		//SELECT ?,?,?,?,?,?,?,?,?";
		//print_r($params['STOCK']);
		//exit;

        foreach($params['STOCK'] as $key => $value){
            //$value['HANDID'] = (string)$value['HANDID'];
			if($key == 0){
				$q .= "(".$params['UNIVCODE'].",".$params['SUBUNIVCODE'].",".$params['STORECODE'].",".$params['SILSADATE'].",".$value['BOOKCODE'].",".$value['SILSAQTY'].",".$params['SILSATYPE'].",".$params['INSERTDATE'].",".$value['HANDID'].")";
			} else {
                $q .= ",(".$params['UNIVCODE'].",".$params['SUBUNIVCODE'].",".$params['STORECODE'].",".$params['SILSADATE'].",".$value['BOOKCODE'].",".$value['SILSAQTY'].",".$params['SILSATYPE'].",".$params['INSERTDATE'].",".$value['HANDID'].")";
			}
		}
		//echo $q;
		//exit;

        // transaction start/end
		$this->db->trans_start();
	    $results = $this->db->query($q);

		if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback(); // 트랜잭션 롤백
            //echo "트랜잭션 실패";
			return -1;
        } else {
            $this->db->trans_commit(); // 트랜잭션 커밋
            //echo "트랜잭션 성공";
        }
		$this->db->trans_complete();
	    //echo $this->db->last_query();
		
		return $this->db->trans_status()? 1 : 0;
	}

}
/* End of file hand_terminal_model.php */
/* Location: ./application/models/hand_terminal_model.php */
?>