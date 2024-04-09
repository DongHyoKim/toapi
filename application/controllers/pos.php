<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos extends CT_Controller {
  
  private $json_array = array();

  public function __construct(){
		parent::__construct();
		$this->load->model('Pos_api_model','API');
		//print_r($this);
  }	
  
  public function index(){

  }
  
  public function ticket_machine_mileage(){	
	
	//print_r($this);
	//exit;
    $logs = array(
        'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
        'sLogPath'      => BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log',
        'bLogable'      => true
    );

    $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
    $sLogPath    = BASEPATH . '../../logs/copartner/success_log/' . date('Ymd') . '_data.log';
	$eLogPath    = BASEPATH . '../../logs/copartner/error_log/' . date('Ymd') . '_data.log';
    $bLogable    = true;

    $message = array(
        'rCode' => RES_CODE_SUCCESS,
        'error' => array (  'errorCode'     => null,
                            'errorMessage'  => null, ),
    );
    writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

	$UnivCode = $this->input->post('UnivCode',true);
	$saletotal_member = $this->input->post('saletotal_member',true);

	$arr = array(
		'saletotal_member'   => $saletotal_member,
		'HPHONENO'    => '',
		'UNIVCODE'    => $UnivCode,
		'SUBUNIVCODE' => '001',
		);		

	if (!$UnivCode) {      
	  $json_array['status'] = -1;    
	  $json_array['message'] = "대학코드가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  echo json_encode($json_array);      
	  exit;
	}

	if (!$saletotal_member) {      
	  $json_array['status'] = -1;    
	  $json_array['message'] = "조합원번호가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  echo json_encode($json_array);      
	  exit;
	}

	$tmp_result = $this->API->getTotalMileage($arr);
	
	if($tmp_result['0']['MemName']) {
	    $json_array['status']       = 1;
        $json_array['MemName']      = $tmp_result['0']['MemName'];
	    $json_array['totalmileage'] = $tmp_result['0']['MILEAGE'];
	} else {
        $json_array['status']  = -1;
	    $json_array['message'] = "입력한 조합원번호가 존재하지 않습니다.";
	    writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	    echo json_encode($json_array);      
	    exit;
	}

	echo json_encode($json_array);
	writeLog("[{$sLogFileId}] univcode=".json_encode($UnivCode)." member=".json_encode($json_array,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
	writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
	exit;    
	}

  public function ticket_machine_mileage_insert() {

	$logs = array(
        'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
        'sLogPath'      => BASEPATH . '../../logs/copartner/' . date('Ymd') . '_data.log',
        'bLogable'      => true
    );
    $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
    $sLogPath    = BASEPATH . '../../logs/copartner/success_log/' . date('Ymd') . '_data.log';
	$eLogPath    = BASEPATH . '../../logs/copartner/error_log/' . date('Ymd') . '_data.log';
    $bLogable    = true;
    $message = array(
        'rCode' => RES_CODE_SUCCESS,
        'error' => array (  'errorCode'     => null,
                            'errorMessage'  => null, ),
    );

    writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

	$UnivCode = $this->input->post('UnivCode',true);
    $BARCODENO = $this->input->post('BARCODENO',true);  //-- 조합원코드
	$params = array(
		'BARCODENO'   => $BARCODENO,
		'HPHONENO'    => '',
		'UNIVCODE'    => $UnivCode,
		'SUBUNIVCODE' => '001',
		);
	$tmp_result = $this->API->getCopartnerMember($params);
    if(empty($tmp_result)) {
		$json_array['status'] = -1;    
	    $json_array['message'] = "조합원번호가 존재하지 않습니다.";
	    writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
	    echo json_encode($json_array);      
	    exit;
    }
	unset($params);
    $params = array(
	    'BARCODENO'   => $BARCODENO,
        'USEDATE'     => $this->input->post('USEDATE',true),    //-- 영업일
        'DEPTCODE'    => $this->input->post('DEPTCODE',true),   //-- 매장코드
        'POSNO'       => $this->input->post('POSNO',true),      //-- 포스번호
        'BILLNUMBER'  => $this->input->post('BILLNUMBER',true), //-- 영수증번호
        'USEMILEAGE'  => $this->input->post('USEMILEAGE',true), //-- 포인트사용금액
        'AMOUNT'      => $this->input->post('AMOUNT',true),     //-- 이용고금액
        'AMOUNTSAVE'  => $this->input->post('AMOUNTSAVE',true), //-- 포인트적용금액
        'REMARK'      => $this->input->post('REMARK',true),     //-- 설명
        'UNIVCODE'    => $UnivCode,                             //-- 학교코드
		'SUBUNIVCODE' => '001',                                 //-- 캠퍼스코드
	);

	if (!$params['BARCODENO']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "조합원코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  echo json_encode($json_array);
      exit;
    }
    if (!$params['USEDATE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용일자가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);
      exit;
    }
    if (!$params['DEPTCODE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "매장코드가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['POSNO']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "자판기 코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }

    if (!$params['BILLNUMBER']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "영수번호가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    /*
	if (!$params['USEMILEAGE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "포인트사용액이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['AMOUNT']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "이용금액이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['AMOUNTSAVE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "포인트적용액이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['REMARK']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "비고 내용이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
	*/
    if (!$params['UNIVCODE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "학교코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
     }
	unset($tmp_result);
	$tmp_result = $this->API->insertSaleTotalMileage($params);
    
    if($tmp_result){
      $json_array['status']  = 1; 
    }else{
      $json_array['status']  = -1; 
      $json_array['message'] = "DB에러가 발생하였습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  exit;
    }    
 
    echo json_encode($json_array);
	writeLog("[{$sLogFileId}] results=".json_encode(implode("|",$params),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
	writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
    exit;    
	}

  public function service_expos_mileage_ins() {

	$logs = array(
        'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
        'sLogPath'      => BASEPATH . '../../logs/copartner/' . date('Ymd') . '_data.log',
        'bLogable'      => true
    );
    $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
    $sLogPath    = BASEPATH . '../../logs/copartner/success_log/' . date('Ymd') . '_data.log';
	$eLogPath    = BASEPATH . '../../logs/copartner/error_log/' . date('Ymd') . '_data.log';
    $bLogable    = true;
    $message = array(
        'rCode' => RES_CODE_SUCCESS,
        'error' => array (  'errorCode'     => null,
                            'errorMessage'  => null, ),
    );

    writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

	$UnivCode = $this->input->post('UnivCode',true);
    $BARCODENO = $this->input->post('BARCODENO',true);  //-- 조합원코드
    /*
	$params = array(
		'BARCODENO'   => $BARCODENO,
		'HPHONENO'    => '',
		'UNIVCODE'    => $UnivCode,
		'SUBUNIVCODE' => '001',
		);
	$tmp_result = $this->API->getCopartnerMember($params);
    if(empty($tmp_result)) {
		$json_array['status'] = -1;    
	    $json_array['message'] = "조합원번호가 존재하지 않습니다.";
	    writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
	    echo json_encode($json_array);      
	    exit;
    }
	unset($params);
	*/
    $params = array(
        'UNIVCODE'    => $UnivCode,                             //-- 학교코드
		'SUBUNIVCODE' => '001',                                 //-- 캠퍼스코드
	    'BARCODENO'   => $BARCODENO,
        'USEDATE'     => $this->input->post('USEDATE',true),    //-- 영업일
        'DEPTCODE'    => $this->input->post('DEPTCODE',true),   //-- 매장코드
        'POSNO'       => $this->input->post('POSNO',true),      //-- 포스번호
        'BILLNUMBER'  => $this->input->post('BILLNUMBER',true), //-- 영수증번호
		'USETYPE'     => $this->input->post('USETYPE',true),    //-- 적립사용구분(001:적립,002:사용)
		'SALETYPE'    => $this->input->post('SALETYPE',true),   //-- 판매구분(1:정상판매,5:반품판매)
        'USEMILEAGE'  => $this->input->post('USEMILEAGE',true), //-- 포인트사용금액
        'AMOUNT'      => $this->input->post('AMOUNT',true),     //-- 이용고금액
        'AMOUNTSAVE'  => $this->input->post('AMOUNTSAVE',true), //-- 포인트적용금액
        'REMARK'      => $this->input->post('REMARK',true),     //-- 설명
	);

	if (!$params['BARCODENO']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "조합원코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  echo json_encode($json_array);
      exit;
    }
    if (!$params['USEDATE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "사용일자가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);
      exit;
    }
    if (!$params['DEPTCODE']) {      
      $json_array['status'] = 0;    
      $json_array['message'] = "매장코드가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['POSNO']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "자판기 코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['BILLNUMBER']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "영수번호가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
	if (!$params['USETYPE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "적립 사용구분이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['SALETYPE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "판매구분이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
    }
    if (!$params['UNIVCODE']) {      
      $json_array['status'] = -1;    
      $json_array['message'] = "학교코드가 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
      echo json_encode($json_array);      
      exit;
     }
	unset($tmp_result);
	$tmp_result = $this->API->insertExposMileage($params);
    
    if($tmp_result) {
      $json_array['status']  = 1; 
    } else {
      $json_array['status']  = -1; 
      $json_array['message'] = "DB에러가 발생하였습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  exit;
    }    
 
    echo json_encode($json_array);
	writeLog("[{$sLogFileId}] results=".json_encode(implode("|",$params),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
	writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
    exit;    
	}

    // 조합원 번호조회
	public function copartner_select()
	{	         

  		//echo("********************");
		//exit;


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
    
    	$arr = array(
	    	'UNIVCODE'    => $UnivCode,
		    'SUBUNIVCODE' => '001',
			'phone'       => $phone,
		);	
		
		//$tmp_result = $this->API->copartner_select($phone, $pass);
        $tmp_result = $this->API->copartner_select($arr);

		//print_r($tmp_result);
		//exit;

		$json_array['status']     = 1;
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
    public function copartner_view()
	{	         
        
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

    	$arr = array(
	    	'UNIVCODE'    => $UnivCode,
		    'SUBUNIVCODE' => '001',
			'barcodeno'   => $barcodeno,
		);	
		
        $tmp_result = $this->API->copartner_view($arr);
        
		$json_array['status']     = 1;
        $json_array['Investment'] = $tmp_result['0']['Investment'];   
        $json_array['Surplus']    = $tmp_result['0']['Surplus'];   
        $json_array['Point']      = $tmp_result['0']['Point'];   
        echo json_encode($json_array);      
        exit;    
	}

    // pos 실시간 처리를 위한 공용API
	public function posApi(){	

    $logs = array(
        'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
        'sLogPath'      => BASEPATH . '../../logs/posApi/' . date('Ymd') . '_data.log',
        'bLogable'      => true
    );

    $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
    $sLogPath    = BASEPATH . '../../logs/posapi/success_log/' . date('Ymd') . '_data.log';
	$eLogPath    = BASEPATH . '../../logs/posapi/error_log/' . date('Ymd') . '_data.log';
    $bLogable    = true;

    $message = array(
        'rCode' => RES_CODE_SUCCESS,
        'error' => array (  'errorCode'     => null,
                            'errorMessage'  => null, ),
    );
    writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

	/*
	$UnivCode  =  $this->input->post('UnivCode',true);
	$dbName    =  $this->input->post('dbName',true);
	$spName    =  $this->input->post('spName',true);
	$params    =  $this->input->post('params',true);
    */

	$str_receivedata = $this->input->post('sPost',true);

    $params_array = array();
    $params_array = explode("|",$str_receivedata);

    $UnivCode = $params_array['0'];
	$dbName   = $params_array['1'];
	$spName   = $params_array['2'];

	array_splice($params_array, 0, 3);

	//print_r($params_array);
	//exit;

	if (!$UnivCode) {      
	  $json_array['status'] = -1;    
	  $json_array['message'] = "UnivCode가 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  echo json_encode($json_array);      
	  exit;
	}
	if (!$dbName) {  
	  $json_array['status'] = -1;    
	  $json_array['message'] = "dbName이 존재하지 않습니다.";
      writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  echo json_encode($json_array);      
	  exit;
	}
	if (!$spName) {      
	  $json_array['status'] = -1;    
	  $json_array['message'] = "spName이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  echo json_encode($json_array);      
	  exit;
	}
	if (!$params_array) {      
	  $json_array['status'] = -1;    
	  $json_array['message'] = "params이 존재하지 않습니다.";
	  writeLog("[{$sLogFileId}] eCode=".json_encode($json_array['status'])." eMessage=".json_encode($json_array['message'],JSON_UNESCAPED_UNICODE), $eLogPath, $bLogable);
	  echo json_encode($json_array);      
	  exit;
	}

    writeLog("[{$sLogFileId}] univcode=".json_encode($UnivCode)." dbName=".json_encode($dbName)." spName=".json_encode($spName)." params_array=".json_encode($params_array), $sLogPath, $bLogable);

	$tmp_result = $this->API->execQuerry($UnivCode, $dbName, $spName, $params_array);

    $tmp_result = convertMSEncoding($tmp_result);

    //print_r($tmp_result);
	//exit;

	$json_array['status']  = 1;
	$json_array['results'] = $tmp_result;

	writeLog("[{$sLogFileId}] results=".json_encode($tmp_result,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
	writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
	
	echo json_encode($json_array);
	
	exit;   
	}

}

/* End of file pos.php */
/* Location: ./application/controllers/pos.php */