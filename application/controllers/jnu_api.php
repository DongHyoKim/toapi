<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Jnu_api extends CT_Controller {

    private $json_array = array();

	public function __construct(){
		parent::__construct();
		$this->load->model('Jnu_api_model','API');
        $this->load->helper('common_helper');
        $this->load->helper('jwt_helper');
		//print_r($this);
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
        header("Content-type:text/html;charset=utf-8");
    }

    public function index(){
        $str = "20000736";
        $enc_str=AES256_ENC($str);
        $dec_str=AES256_DEC($enc_str);
        echo("enc_str: ".$enc_str."\n");
        echo("dec_str: ".$dec_str);
        exit;
    }

    public function test()
    {
        $data_array = [
            'ch_gubun'    => '001', //채널구분 카카오"001"
        ];
        $encode_str  = AES256_ENC(json_encode($data_array,JSON_UNESCAPED_UNICODE));
        //echo("encode_str:".$encode_str."\n");
        
        //$encode = 'H4n3JLQT9vXiVLn1aT6n1bZHRpVLkT\/kciCSHkmfk9g=';  // 엔투 최현우
        $encode = 'akd2bE5Sd25qclZYZjQ4MllTaVA2enI1ajc1UTJQbXA1WjZ0UlhWekFHYz0='; //김동효
        $decode = AES256_DEC($encode_str);
        //echo("decode:".$decode."\n");
    
        //$test_json = '{"res_msg": "정상 처리되었습니다.","token_id": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NjY4NDY4MTksImlhdCI6MTY2Njg0NjUxOSwiaWQiOiJqaW5qdV9jaGF0Ym90X2FwaV9yZXEifQ.7YMRueUwxpmdo1OhI-oHpqrqgzSSaV-UmBCgEwEVQUI","kakao_key": "","DATA": "VpZd5I2/kU7klAva3OuApg==","interfaceid": "callChat","res_code": "0000"}';
        $test_json = '{"res_msg":"정상 처리되었습니다.","token_id":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NjczNjM3NzgsImlhdCI6MTY2NzM2MzQ3OCwiaWQiOiJqaW5qdV9jaGF0Ym90X2FwaV9yZXEifQ.KYquQ4NevTCn32VQifud7sg3YaEIwbnbY_8_S7KBITQ","DATA":"4AudkWoeR8L656iCyY8YakYnabypFi3feKHheXKENVq+ryCgC\/icb8R0nyGoaK3cvk8N5E5HX9FEfWOoNG9Q3LUHu9YzLsbHeekor19Zy4ayXSfD+iQgDHtzP8hjNL7flO5jZmAPNF6cFZG\/r0R7\/bxI4GDoxE7REer1gw6FEbA=","kakao_key":"CTv7EIkbm0M6","interfaceid":"callChat","res_code":"0000"}';
        $test_arr  = json_decode($test_json,true);
        $curl_data['res_body']['token_id'] = $test_arr['token_id'];
        $token_arr = $this->decode_token($curl_data['res_body']['token_id']);
        $token_yn  = $this->check_token($token_arr);
        echo("token_arr:");print_r($token_arr);
        echo("token_yn:");print_r($token_yn);
        $rec_data_arr = json_decode(AES256_DEC($test_arr['DATA']),true);
        echo("data:");print_r($rec_data_arr);
    }
  
    public function token()
    {
        $jwt = new JWT();
    
        $JwtSecretKey = TOKEN_KEY;
        $data = [
            //'exp' => time() + (TOKEN_EXP * (60*60*24)), // 만료기간(초*분*시간, 현재 5일
            'exp' => time() + (TOKEN_EXP * (60)), // 만료기간(초, 현재 5분)
            'iat' => time(),                            // 생성일
            'id' => TOKEN_ID,
        ];
    
        $token = $jwt->encode($data,$JwtSecretKey,'HS256');
        echo($token);
        return $token;
    }
  
    public function decode_token()
    {
        $jwt = new JWT();
        $JwtSecretKey = TOKEN_KEY;
        //$token1 = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NjczNjM3NzgsImlhdCI6MTY2NzM2MzQ3OCwiaWQiOiJqaW5qdV9jaGF0Ym90X2FwaV9yZXEifQ.KYquQ4NevTCn32VQifud7sg3YaEIwbnbY_8_S7KBITQ';
        $token1 = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE3MjI4MjA1MjQsImlhdCI6MTcyMjM4ODUyNCwiaWQiOiJraW9za19hcGlfcmVxIn0.44DmSfYpmegKCjznys27jTUqAxjlSkQIiSeefljrM_o';
        $decode_token = $jwt->decode($token1,$JwtSecretKey,'HS256');
    
        //echo '<pre>';
        print_r($decode_token);
    
        //$json_token = $jwt->jsonEncode($decode_token);
        $token_array = (array)$decode_token;
        return $token_array;
    }

    // 수신Token의 유효성검사(1.id 확인 2.유효시간)
    public function check_token($decode_token_array)
    {
        $result_check = '0000';
    
        if($decode_token_array['id'] != TOKEN_ID){
            $result_check = '2003';  // invalid id
        }
        if($decode_token_array['exp'] < $decode_token_array['iat']){
            $result_check = '2004';  // Valid time out
        }
        return $result_check;
    }
  
    // 발신토큰 유효성 확인 및 재발급
    public function pre_check_token($token_id)
    {
        // 토큰이 없거나 체크토큰이 유효성이 없으면 신규발행
        if(empty($token_id) || $this->check_token($token_id) != "0000"){
            $token_id = $this->token(); //json web token
        }
    
        return $token_id;
    }

	public function getBalanceMileage(){

    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_getBalanceMileage.log',
            'bLogable'      => true
        );

        $sLogFileId  = time().'_'.substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_getBalanceMileage.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $headers = $this->input->request_headers();
        $token   = isset($headers['Authorization']) ? $headers['Authorization'] : null;
        writeLog("[{$sLogFileId}] token=".json_encode($token,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        $token_arr = [];
        $token_arr = validate_jwt($token);
        $token_arr['exp'] = date('Y-m-d H:i:s',$token_arr['exp']);
        $token_arr['iat'] = date('Y-m-d H:i:s',$token_arr['iat']);
        writeLog("[{$sLogFileId}] token_arr=".json_encode($token_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        if($token_arr['tokenYn'] == "No-Token"){
            show_error('Authorization header not found', 401);
            exit;
        } else if($token_arr['tokenYn'] == "FALSE"){
            show_error('Invalid token', 401);
            exit;
        } else if($token_arr['tokenYn'] == "INVALID ID"){
            show_error('Invalid id', 401);
            exit;
        } else if($token_arr['tokenYn'] == "TIME OUT"){
            show_error('Expiration time error', 401);
            exit;
        }

        $univcode   = $this->input->post('univcode',true);   // 대학코드 00116
        $input_type = $this->input->post('input_type',true); // 입력구분 phone/barcodeno
        $input_data = $this->input->post('input_data',true); // 입력값  01052446198 / 7001160000002564

        if(empty($univcode)){
			$message['rCode']                 = "0001";
            $message['error']['errorCode']    = "0001";
            $message['error']['errorMessage'] = "대학코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}
		if(empty($input_type)){
			$message['rCode']                 = "0002";
            $message['error']['errorCode']    = "0002";
            $message['error']['errorMessage'] = "입력구분이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}
		if(empty($input_data)){
			$message['rCode']                 = "0003";
            $message['error']['errorCode']    = "0003";
            $message['error']['errorMessage'] = "입력값이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}

		$post_data = [
			'univcode'   => $univcode,
			'input_type' => $input_type,
			'input_data' => $input_data,
		];
		writeLog("[{$sLogFileId}] post_data=".json_encode($post_data,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        if($input_type == "phone"){
			$tmp_arr = $this->API->getIdbyphone($univcode, $input_data);
			if(empty($tmp_arr)){
				$message['rCode']                 = "1001";
                $message['error']['errorCode']    = "1001";
                $message['error']['errorMessage'] = "입력한 핸드폰번호에 해당하는 조합원이 없습니다!";
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message,JSON_UNESCAPED_UNICODE);
                exit;
		    }
		} elseif($input_type == "barcodeno"){
			$tmp_arr = $this->API->getIdbybarcodeno($univcode, $input_data);
			if(empty($tmp_arr)){
				$message['rCode']                 = "1002";
                $message['error']['errorCode']    = "1002";
                $message['error']['errorMessage'] = "입력한 조합원번호에 해당하는 조합원이 없습니다!";
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message,JSON_UNESCAPED_UNICODE);
                exit;
		    }
		} else {
			$message['rCode']                 = "0004";
            $message['error']['errorCode']    = "0004";
            $message['error']['errorMessage'] = "입력구분을 잘못 입력하였습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}
		//print_r($tmp_arr);
		//exit;

        $tmp_balance = $this->API->getBalancebyid($univcode, $tmp_arr['id']);
		if(empty($tmp_balance)){
			$message['rCode']                 = "2001";
            $message['error']['errorCode']    = "2001";
            $message['error']['errorMessage'] = "고객의 마일리지가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}
		//echo("tmp_balance:".$tmp_balance);
		//exit;

	    $results = [
		    'copartner_name' => $tmp_arr['name'],
		    'barcodeno'      => $tmp_arr['barcodeno'],
		    'mileage'        => $tmp_balance,
	    ];
        $message['successMessage'] = $results;

		writeLog("[{$sLogFileId}] message=".json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		//print_r($results);
		//exit;

        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
        return;
	}

	function insertMileage(){
    	
		$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_insertMileage.log',
            'bLogable'      => true
        );

        $sLogFileId  = time().'_'.substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_insertMileage.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
		/*
            [dbo].[SP_SERVICE_POS_CPTMILINS]    @BARCODENO      VARCHAR(16)     --조합원바코드 번호
                                               ,@USEDATE        VARCHAR(14)     --영업일 - 구매 일시 년월일
                                               ,@DEPTCODE       CHAR(7)         --매장코드 (카페아띠 2001000 / 카페지젤 2000500)
                                               ,@POSNO          CHAR(2)         --포스번호 (카페아띠 K1,K2   / 카페지젪 K1)
                                               ,@BILLNUMBER     FLOAT           --영수증 번호 (영수증번호)
                                               ,@USEMILEAGE     FLOAT           --포인트사용금액*  포인트사용할 경우
                                               ,@AMOUNT         FLOAT           --이용고금액*    이용고적립용
                                               ,@AMOUNTSAVE     FLOAT           --포인트적용금액*  포인트적립할 경우
                                               ,@REMARK         VARCHAR(128)    --설명
                                               ,@UNIVCODE       CHAR(8)         --학교코드  (전남대 00116)
                                               ,@SUBUNIVCODE    CHAR(3) = '001'	--캠퍼스코드 (광주캠퍼스 001)    
		*/

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $headers = $this->input->request_headers();
        $token   = isset($headers['Authorization']) ? $headers['Authorization'] : null;
        writeLog("[{$sLogFileId}] token=".json_encode($token,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        $token_arr = [];
        $token_arr = validate_jwt($token);
        $token_arr['exp'] = date('Y-m-d H:i:s',$token_arr['exp']);
        $token_arr['iat'] = date('Y-m-d H:i:s',$token_arr['iat']);
        writeLog("[{$sLogFileId}] token_arr=".json_encode($token_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        if($token_arr['tokenYn'] == "No-Token"){
            show_error('Authorization header not found', 401);
            exit;
        } else if($token_arr['tokenYn'] == "FALSE"){
            show_error('Invalid token', 401);
            exit;
        } else if($token_arr['tokenYn'] == "INVALID ID"){
            show_error('Invalid id', 401);
            exit;
        } else if($token_arr['tokenYn'] == "TIME OUT"){
            show_error('Expiration time error', 401);
            exit;
        }

        // 1. POST DATA수신 및 검증
        $univcode    = $this->input->post('univcode',true);     // 대학코드: 00116
        $subunivcode = $this->input->post('subunivcode',true);  // 캠퍼스코드: 001
        $barcodeno   = $this->input->post('barcodeno',true);    // 조합원번호: 7001 1600 0000 256 4
        $usedate     = $this->input->post('usedate',true);      // 영업일: 구매영업일자 년월일(8자리)입력시 자동으로 14자리 변환됨
        $deptcode    = $this->input->post('deptcode',true);     // 매장코드:     (카페아띠 2001000 / 카페지젤 2000500)
        $posno       = $this->input->post('posno',true);        // 포스일련번호: (카페아띠 K1,K2   / 카페지젤 K1)
        $billnumber  = $this->input->post('billnumber',true);   // 영수번호: 해당 영업일 영수번호
        $usemileage  = $this->input->post('usemileage',true);   // 포인트 사용금액(반품시 - 음수로)
        $amount      = $this->input->post('amount',true);       // 이용고 금액(반품시 - 음수로) 포인트/이용고 다름
        $amountsave  = $this->input->post('amountsave',true);   // 포인트 적립금액(반품시 - 음수로)
        $remark      = $this->input->post('remark',true);       // 설명, 적요
       
        if(empty($univcode)){
			$message['rCode']                 = "0001";
            $message['error']['errorCode']    = "0001";
            $message['error']['errorMessage'] = "대학코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}
        if(empty($subunivcode)){
			$message['rCode']                 = "0002";
            $message['error']['errorCode']    = "0002";
            $message['error']['errorMessage'] = "캠퍼스코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}   
        if(empty($barcodeno)){
			$message['rCode']                 = "0003";
            $message['error']['errorCode']    = "0003";
            $message['error']['errorMessage'] = "조합원번호가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}
        if(empty($usedate)){
			$message['rCode']                 = "0004";
            $message['error']['errorCode']    = "0004";
            $message['error']['errorMessage'] = "영업일이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		} else if(strlen($usedate) != 8){
			$message['rCode']                 = "0005";
            $message['error']['errorCode']    = "0005";
            $message['error']['errorMessage'] = "영업일이 8자리가 아닙니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }            
        if(empty($deptcode)){
			$message['rCode']                 = "0006";
            $message['error']['errorCode']    = "0006";
            $message['error']['errorMessage'] = "매장코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		} else if($deptcode != "2001000" && $deptcode != "2000500"){
			$message['rCode']                 = "0007";
            $message['error']['errorCode']    = "0007";
            $message['error']['errorMessage'] = "지정된 매장코드가 아닙니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
        if(empty($posno)){
			$message['rCode']                 = "0008";
            $message['error']['errorCode']    = "0008";
            $message['error']['errorMessage'] = "키오스크번호가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		} else if(substr($posno,0,1) != "K"){
			$message['rCode']                 = "0009";
            $message['error']['errorCode']    = "0009";
            $message['error']['errorMessage'] = "지정된 키오스크번호가 아닙니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
        if(empty($billnumber)){
			$message['rCode']                 = "0010";
            $message['error']['errorCode']    = "0010";
            $message['error']['errorMessage'] = "영수번호가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
		}
        if(empty($usemileage)) $usemileage = 0;     // 마일리지 사용액
        if(empty($amount))     $amount     = 0;     // 이용고적립액
        if(empty($amountsave)) $amountsave = 0;     // 포인트적립액
        if(empty($remark))     $remark     = ' ';   // 적요

        // 2. 마일리지 사용시 잔여액 검증
        $tmp_balance = $this->API->getBalancebybarcodeno($univcode, $barcodeno);
        writeLog("[{$sLogFileId}] tmp_balance=".json_encode($tmp_balance,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        if($usemileage > 0 && $usemileage > $tmp_balance){
            $message['rCode']                 = "1001";
            $message['error']['errorCode']    = "1001";
            $message['error']['errorMessage'] = "차감할 포인트가 부족합니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
		
		$post_data = [
			'barcodeno'   => $barcodeno,
			'usedate'     => $usedate,
			'deptcode'    => $deptcode,
			'posno'       => $posno,
			'billnumber'  => $billnumber,
			'usemileage'  => $usemileage,  // 마일리지사용액 없는 경우 0
			'amount'      => $amount,      // 이용고적립액   없는 경우 0
            'amountsave'  => $amountsave,  // 포인트적립액   없는 경우 0
			'remark'      => $remark,
			'univcode'    => $univcode,
			'subunivcode' => $subunivcode,
		];
		writeLog("[{$sLogFileId}] post_data=".json_encode($post_data,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        $tmp_result = $this->API->insertMileage($post_data);
        if($tmp_result['USEMILEAGE'] <= 0) $tmp_result['MILEAGESAVE'] = 0;                                          // -0 처리
        if(is_float($tmp_result['MILEAGESAVE'])) $tmp_result['MILEAGESAVE'] = round($tmp_result['MILEAGESAVE'],0);  // 반올림
        writeLog("[{$sLogFileId}] tmp_result=".json_encode($tmp_result,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);        

        $message['successMessage'] = $tmp_result;
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
        return;
	}

    //card조회(상품권/선불카드, 단일/멀티카드 공용) api
    public function check_card_all() {
        
    	$logs = [
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_check_card_all.log',
            'bLogable'      => true
        ];

        $sLogFileId  = time().'_'.substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_check_card_all.log';
        $bLogable    = true;

        $message = [
            'rCode' => RES_CODE_SUCCESS,
            'error' => [    'errorCode'     => null,
                            'errorMessage'  => null, ],
        ];
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $headers = $this->input->request_headers();
        $token   = isset($headers['Authorization']) ? $headers['Authorization'] : null;
        writeLog("[{$sLogFileId}] token=".json_encode($token,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        $token_arr = [];
        $token_arr = validate_jwt($token);
        $token_arr['exp'] = date('Y-m-d H:i:s',$token_arr['exp']);
        $token_arr['iat'] = date('Y-m-d H:i:s',$token_arr['iat']);
        writeLog("[{$sLogFileId}] token_arr=".json_encode($token_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        if($token_arr['tokenYn'] == "No-Token"){
            show_error('Authorization header not found', 401);
            exit;
        } else if($token_arr['tokenYn'] == "FALSE"){
            show_error('Invalid token', 401);
            exit;
        } else if($token_arr['tokenYn'] == "INVALID ID"){
            show_error('Invalid id', 401);
            exit;
        } else if($token_arr['tokenYn'] == "TIME OUT"){
            show_error('Expiration time error', 401);
            exit;
        }

        $univcode     = $this->input->post('univcode',true);
		$sub_univcode = $this->input->post('sub_univcode',true);
		$card_string  = $this->input->post('card_string',true);
		
		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
		if (!$card_string) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "card번호,사용 금액군이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
        //echo("card_string=".$card_string);

		$tmp_card = array();
		$tmp_card_arr = array();
        $tmp_card = explode(",",$card_string);

	    //print_r($tmp_card);
		//exit;

		foreach($tmp_card as $key => $value){
            $tmp_card_arr = explode("|",$value);
            $params_array = array(
		        'univcode' => $univcode,
			    'card_no'  => $tmp_card_arr['0'],
		    );
	        //print_r($params_array);
		    //exit;
            if(substr($tmp_card_arr['0'],0,1) == "4"){ // 상품권
                $dbName = "U_VOUCHER";
		        $spName = "SP_VOUCHER_APISEL";
			} elseif(substr($tmp_card_arr['0'],0,1) == "5"){
			    $dbName = "U_PREPAID";
			    $spName = "SP_PREPAID_APISEL";
			}

			// 상품권/선불카드별 사용여부 조회
    	    $tmp_result = $this->API->execSp($univcode, $dbName, $spName, $params_array);
            $tmp_result = convertMSEncoding($tmp_result);
            //print_r($tmp_result);
		    //exit;
            
   			writeLog("[{$sLogFileId}] result".$key."=". json_encode($tmp_result,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            // 사용불가의 경우 에러메시지 리턴
			if($tmp_result['VALUE'] == "FALSE"){
                $message['rCode'] = "0005";
                $message['error']['errorCode'] = "0005";
                $message['error']['errorMessage'] = $tmp_card_arr['0']." ".$tmp_result['MESSAGE'];
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
                echo json_encode($message,JSON_UNESCAPED_UNICODE);
                exit;
	        } else {
			// 사용가능하나 사용한도가 초과될 경우 에러메시지 리턴
                //echo($tmp_result['BALANCEAMT']."&");
                //echo($tmp_card_arr['1']."\n");
				
			    if($tmp_result['BALANCEAMT'] < $tmp_card_arr['1']){
                    $message['rCode'] = "0006";
                    $message['error']['errorCode'] = "0006";
                    $message['error']['errorMessage'] = $tmp_card_arr['0']." 금액 초과, ".$tmp_result['BALANCEAMT'];
                    writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                    writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
                    echo json_encode($message,JSON_UNESCAPED_UNICODE);
                    exit;
				}
			}
            
            if(empty($tmp_result['CARDNO'])) $tmp_result['CARDNO'] = $tmp_card_arr['0'];

			if(empty($message['message_detail'])){
                $message['message_detail'] = $tmp_result['CARDNO']."번 카드 ".$tmp_result['BALANCEAMT']."원 \n";
			} else {
                $message['message_detail'] .= $tmp_result['CARDNO']."번 카드 ".$tmp_result['BALANCEAMT']."원 \n";
			}
		}
        // Ends of Log Write
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
        $message['message'] = count($tmp_card)."개의 상품권이 모두 사용가능!!";
		
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
		//print_r($results);
        return;
    }

    //card사용 (상품권/선불카드, 단일/멀티카드 공용) api
    public function use_card_all() {
        
    	$logs = [
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_use_card_all.log',
            'bLogable'      => true
        ];

        $sLogFileId  = time().'_'.substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_use_card_all.log';
        $bLogable    = true;

        $message = [
            'rCode' => RES_CODE_SUCCESS,
            'error' => [ 'errorCode'     => null,
                         'errorMessage'  => null, ],
        ];
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $headers = $this->input->request_headers();
        $token   = isset($headers['Authorization']) ? $headers['Authorization'] : null;
        writeLog("[{$sLogFileId}] token=".json_encode($token,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        $token_arr = [];
        $token_arr = validate_jwt($token);
        $token_arr['exp'] = date('Y-m-d H:i:s',$token_arr['exp']);
        $token_arr['iat'] = date('Y-m-d H:i:s',$token_arr['iat']);
        writeLog("[{$sLogFileId}] token_arr=".json_encode($token_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        if($token_arr['tokenYn'] == "No-Token"){
            show_error('Authorization header not found', 401);
            exit;
        } else if($token_arr['tokenYn'] == "FALSE"){
            show_error('Invalid token', 401);
            exit;
        } else if($token_arr['tokenYn'] == "INVALID ID"){
            show_error('Invalid id', 401);
            exit;
        } else if($token_arr['tokenYn'] == "TIME OUT"){
            show_error('Expiration time error', 401);
            exit;
        }

        $univcode     = $this->input->post('univcode',true);     // 대학코드(5)
		$sub_univcode = $this->input->post('sub_univcode',true); // 캠퍼스코드(3)
		$use_date     = $this->input->post('use_date',true);     // 사용일자(8)
		$store_code   = $this->input->post('store_code',true);   // 매장코드 (카페아띠 2001000 / 카페지젤 2000500)
		$order_no     = $this->input->post('order_no',true);     // 주문번호(20자리,일자시간+랜덤번호)
		$order_status = $this->input->post('order_status',true); // 주문상태값(20,70)
		$card_string  = $this->input->post('card_string',true);  // 상품권번호,금액|상품권번호,금액 .....
		
		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
		if (!$use_date) {      
            $message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "use_date이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
		if (!$order_no) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "order_no가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
		if (!$order_status) {      
            $message['rCode'] = "0005";
            $message['error']['errorCode'] = "0005";
            $message['error']['errorMessage'] = "order_status가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
		if (!$card_string) {      
            $message['rCode'] = "0006";
            $message['error']['errorCode'] = "0006";
            $message['error']['errorMessage'] = "card_string이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (!$store_code) {      
            $message['rCode'] = "0007";
            $message['error']['errorCode'] = "0007";
            $message['error']['errorMessage'] = "매장코드가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message,JSON_UNESCAPED_UNICODE);
            exit;
        }
        //echo("card_string=".$card_string);

		$tmp_card = [];
		$tmp_card_arr = [];
        $tmp_card = explode("/",$card_string);

	    //print_r($tmp_card);
		//exit;

		foreach($tmp_card as $key => $value){
            $tmp_card_arr = explode(",",$value);
			// 카드 사용
            unset($params_array);
			unset($dbName);
			unset($spName);
            unset($tmp_result);

            if(substr($tmp_card_arr['0'],0,1) == "4"){ // 상품권
		        $dbName = "U_VOUCHER";
		        $spName = "SP_VOUCHER_APIESHOP";

			    $params_array = array(
                    'univcode'     => $univcode,                      // 대학코드(5)
        		    'sub_univcode' => $sub_univcode,                  // 캠퍼스코드(3)
	        	    'use_date'     => $use_date,                      // 사용일자(8)
		        	'store_code'   => $store_code,                    // 매장코드
	    	        'order_no'     => $order_no,                      // 주문번호(20자리,일자시간+랜덤번호)
    		        'order_status' => $order_status,                  // 주문상태값(20,70)
	    	        //'in_string'    => $tmp_card_arr['0']."|".$tmp_card_arr['1'],             // 사용금액(음수:반품)
					'in_string'    => $tmp_card_arr['0'].",".$tmp_card_arr['1'],          // 사용카드금액(음수:반품)
		        );
			} elseif(substr($tmp_card_arr['0'],0,1) == "5"){
		        $dbName = "U_PREPAID";
		        $spName = "SP_PREPAID_APIESHOP";

			    $params_array = array(
                    'univcode'     => $univcode,                      // 대학코드(5)
        		    'sub_univcode' => $sub_univcode,                  // 캠퍼스코드(3)
	        	    'use_date'     => $use_date,                      // 사용일자(8)
		        	'store_code'   => $store_code,                    // 매장코드
    		        'card_no'      => $tmp_card_arr['0'],             // 카드번호(16자리)
	    	        'order_no'     => $order_no,                      // 주문번호(20자리,일자시간+랜덤번호)
    		        'order_status' => $order_status,                  // 주문상태값(20,70)
	    	        'use_amount'   => $tmp_card_arr['1'],             // 사용금액(음수:반품)
		        );
			}
			//print_r($params_array);
			//exit;

    	    $tmp_result = $this->API->execSp($univcode, $dbName, $spName, $params_array);
            $tmp_result = convertMSEncoding($tmp_result);

			writeLog("[{$sLogFileId}] return_result=".json_encode($tmp_result,JSON_UNESCAPED_UNICODE),$sLogPath, $bLogable);

            // 사용불가의 경우 에러메시지 리턴
			if($tmp_result['VALUE'] == "FALSE"){
                $message['rCode'] = "0008";
                $message['error']['errorCode'] = "0008";
                $message['error']['errorMessage'] = $tmp_card_arr['0']." ".$tmp_result['MESSAGE'];
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message,JSON_UNESCAPED_UNICODE);
                exit;
	        }
		}
        // Ends of Log Write
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
        $message['message'] = count($tmp_card)."개의 상품권/선불카드 사용완료!!!";
		
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
		//print_r($results);
        return;		
    }

    //한글인코딩 변경 euc-kr변경
    function convertMSEncoding($str)
    {
        $str =  mb_convert_encoding($str,  "CP949" , "UTF-8");
        $str =  mb_convert_encoding($str,  "UTF-8" , "CP949");  
        return  $str;
    }

    // NodeJS로 전송된 매출정보 처리
    function process_file() {

        // $receiveHeader['univcode']
        $receiveHeader = apache_request_headers();
        $univcode  = $receiveHeader['univcode'];
    	
        $logs = [
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_process_file.log',
            'bLogable'      => true
        ];

        $sLogFileId  = time().'_'.substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_process_file.log';
        $bLogable    = true;

        $file_name = $this->input->get('fileName',true);  // 읽어야할 파일명 예: JNUNI00001-TRANS.240813
        $file_path = FCPATH.'downloads/'.$file_name;  // 파일 경로 설정 예: application/downloads/JNUNI00001-TRANS.240813
        writeLog("[{$sLogFileId}] file=".json_encode($file_path,JSON_UNESCAPED_UNICODE),$sLogPath, $bLogable);

        // 파일 라인을 저장할 배열 초기화
        $lines = [];
        
        if (file_exists($file_path)) {    // 파일 열기
            $file = fopen($file_path, 'r');
            if ($file) {
                while (($line = fgets($file)) !== false) {    // 파일의 각 줄을 반복
                    $lines[] = trim($line);                   // 줄 끝의 공백 제거
                }
                fclose($file);    // 파일 닫기
                $this->process_lines($lines);    // 한 번에 모든 라인을 처리
                $message = $file_name." 파일 전송완료!";
                writeLog("[{$sLogFileId}] error=".json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

            } else {
                //echo "파일을 열 수 없습니다.";
                $message = $file_name." 파일을 열 수 없습니다.";
                writeLog("[{$sLogFileId}] error=".json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                exit;
            }
        } else {
            //echo "파일이 존재하지 않습니다.";
            $message = $file_name." 파일이 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] error=".json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            exit;
        }
    }

    function process_line($univcode,$lines,) {
		
        $logs = [
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_cron_receive.log',
            'bLogable'      => true
        ];

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnu_api/' . date('Ymd') . '_cron_receive.log';
        $bLogable    = true;

        $results = [
            'success'    => RES_CODE_SUCCESS,
            'msg'        => '',
        ];

        try
        {
		    writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

		    $yesterday = date('Ymd', $_SERVER['REQUEST_TIME']-86400);
		    //$yesterday = '20240401';

            $store_code       = $this->API->getStorecode($code_type,$receive_store);

            $params = [
                'UNIVCODE'    => $univcode,    // 이대
                'SUBUNIVCODE' => '001',      // 서울캠퍼스
                'SALEDATE'    => $yesterday,
                'STORECODE'   => '3000200'   // 김옥길기념관카페
            ];

		} 
		catch(File_exception $e) 
		{
			$results['success'] = $e->error;
			$results['msg']     = $e->message;
		}
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		return $results;

        //$this->db->insert('processed_lines', $data);  // 'processed_lines'는 데이터베이스 테이블 이름
    }

	//주문정보 receive api
    public function ci_ver() {
        echo CI_VERSION;
	}
}
/* End of file jnu_api.php */
/* Location: ./application/controllers/jnu_api.php */