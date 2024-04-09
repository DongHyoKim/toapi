<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class hufs_app extends CT_Controller {
  
    private $json_array = array();

    public function __construct(){
        parent::__construct();
        $this->load->model('hufs_model','API');
    }	
  
    public function index(){
    }

    // 조합원 번호조회
	public function copartner_select(){	         

  		echo("********************");
		exit;


		$UnivCode = $this->input->post('UnivCode',true);
        $phone    = $this->input->post('phone',true);
        //$pass     = $this->input->post('pass',true);

        // 입력값 체크
        if (!$UnivCode) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "대학코드가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }

        if (!$phone) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "사용자 전화번호가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }

        //if (!$pass) {      
        //    $json_array['status'] = -1;    
        //    $json_array['message'] = "사용자 PassWord가 존재하지 않습니다.";
        //    echo json_encode($json_array);      
        //    exit;
        //}
    
        $tmp_result = $this->API->copartner_select($phone, $pass);
        $tmp_result = $this->API->copartner_select($phone);

        $json_array['barcodeno']  = $tmp_result['0']['barcodeno'];
        $json_array['class']      = $tmp_result['0']['class'];
        $json_array['party']      = $tmp_result['0']['party'];
		$json_array['name']       = $tmp_result['0']['name'];
        $json_array['gender']     = $tmp_result['0']['gender'];
        $json_array['stnum']      = $tmp_result['0']['stnum'];
		
        echo json_encode($json_array);      
        exit;    
	}

    // 조합원 포인트조회
    public function copartner_view(){	         
        
		$UnivCode = $this->input->post('UnivCode',true);
        $barcodeno = $this->input->post('barcodeno',true);
    
        if (!$UnivCode) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "대학코드가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
        if (!$barcodeno) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "사용자 조합원BarCode가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        $tmp_result = $this->API->copartner_view($barcodeno);
        
		$json_array['status']     = 1;
        $json_array['Investment'] = $tmp_result['0']['Investment'];   
        $json_array['Surplus']    = $tmp_result['0']['Surplus'];   
        $json_array['Point']      = $tmp_result['0']['Point'];   
        echo json_encode($json_array);      
        exit;    
	}

}
/* End of file hufs_app.php */
/* Location: ./application/controllers/hufs_app.php */