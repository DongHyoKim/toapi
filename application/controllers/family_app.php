<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Family_app extends CT_Controller {
  
  private $json_array = array();

  public function __construct(){
		parent::__construct();
		$this->load->model('Family_app_api_model','API');
  }	
  
  public function index(){
   
  }

    //자동가입
	public function copartner_auto_join(){	         
    $UnivCode = $this->input->post('UnivCode',true);
    $eMail = $this->input->post('eMail',true);
    $Pass = $this->input->post('Pass',true);

       
    if (!$UnivCode) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "대학코드가 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }

    if (!$eMail) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용자 e-Mail이 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }

    if (!$Pass) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용자 PassWord가 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }
    
    $tmp_result = $this->API->copartnerAutoJoin($eMail, $Pass);
    $json_array['status']     = 1;
    $json_array['Name']       = $tmp_result['0']['Name'];
    $json_array['Phone']      = $tmp_result['0']['Phone'];
    $json_array['ClassName']  = $tmp_result['0']['ClassName'];
    $json_array['Stnum']      = $tmp_result['0']['Stnum'];
    $json_array['BarCodeNo']  = $tmp_result['0']['BarCodeNo'];
    echo json_encode($json_array);      
    exit;    
	}
  
  // 조합원 확인
  public function copartner_confirm(){	         
    $UnivCode = $this->input->post('UnivCode',true);
    $eMail = $this->input->post('eMail',true);
    $Pass = $this->input->post('Pass',true);
    $Phone = $this->input->post('Phone',true); //부경대(00121)는 전화번호를 조합원에 저장하략지 않으므로 생략 가능
         
    if (!$UnivCode) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "대학코드가 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }

    if (!$eMail) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용자 e-Mail이 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }

    if (!$Pass) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용자 PassWord가 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }
    
    if ($UnivCode != "00121" ) {
      if (!$Phone) {      
        $json_array['status'] = -1;    
        $json_array['message'] = "사용자 핸드폰번호가 존재하지 않습니다.";
        echo json_encode($json_array);      
        exit;
      }
    }
    
    $tmp_result = $this->API->copartnerConfirm($eMail, $Pass, $Phone);
    $json_array['status']    = 1;
    $json_array['BarCodeNo'] = $tmp_result['0']['BarCodeNo'];   
    echo json_encode($json_array);      
    exit;    
	}
  
  // 조합원 정보 전달
  public function copartner_view(){	         
    $UnivCode = $this->input->post('UnivCode',true);
    $BarCodeNo = $this->input->post('BarCodeNo',true);
    
    if (!$UnivCode) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "대학코드가 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }

    if (!$BarCodeNo) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용자 조합원BarCode가 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }
    
    $tmp_result = $this->API->copartnerView($BarCodeNo);
    $json_array['status']     = 1;
    $json_array['Investment'] = $tmp_result['0']['Investment'];   
    $json_array['Surplus']    = $tmp_result['0']['Surplus'];   
    $json_array['Point']      = $tmp_result['0']['Point'];   
    echo json_encode($json_array);      
    exit;    
	}
  // 조합원 상세
  public function copartner_view_detail(){	         
    $UnivCode  = $this->input->post('UnivCode',true);
    $BarCodeNo = $this->input->post('BarCodeNo',true);
    $InvOrPnt  = $this->input->post('InvOrPnt',true);
    $Month       = $this->input->post('Month',true);    
    
    if (!$UnivCode) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "대학코드가 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }

    if (!$BarCodeNo) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용자 조합원BarCode가 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }
    
   if (!$InvOrPnt) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "출자, 포인트 구분이 존재하지 않습니다.";
      echo json_encode($json_array);      
      exit;
    }
    
    
    if($InvOrPnt == "INV") {
      $tmp_result = $this->API->copartnerViewDetail1($BarCodeNo);
      $json_array['status']     = 1;
      $json_array['data']       = $tmp_result; 

    }elseif($InvOrPnt == "PNT"){
      $tmp_result = $this->API->copartnerViewDetail2($UnivCode, $BarCodeNo, $Month);
      $json_array['status']     = 1;
      $json_array['data']       = $tmp_result; 

    } else {
      $json_array['status']  = -1;    
      $json_array['message'] = "출자, 포인트 구분이 INV or PNT가 아닙니다.";
      echo json_encode($json_array);      
      exit;
    }     
   
    echo json_encode($json_array);      
    exit;    
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */