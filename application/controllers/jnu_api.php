<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Jnu_api extends CT_Controller {

    private $json_array = array();

	public function __construct(){
		parent::__construct();
		$this->load->model('Jnu_api_model','API');
		//print_r($this);
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
        header("Content-type:text/html;charset=utf-8");
    }

    public function index(){
    }

    
	public function getBalanceMileage(){

    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/jnu_api/' . date('Ymd') . 'getBalanceMileage.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnu_api/' . date('Ymd') . 'getBalanceMileage.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $univcode   = $this->input->post('univcode',true);   // 대학코드 00116
        $input_type = $this->input->post('input_type',true); // 입력구분 phone/barcodeno
        $input_data = $this->input->post('input_data',true); // 입력값  01052446198 / 7001160000002564

        if(empty($univcode)){
			$message['rCode']                 = "0001";
            $message['error']['errorCode']    = "0001";
            $message['error']['errorMessage'] = "대학코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($input_type)){
			$message['rCode']                 = "0002";
            $message['error']['errorCode']    = "0002";
            $message['error']['errorMessage'] = "입력구분이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($input_data)){
			$message['rCode']                 = "0003";
            $message['error']['errorCode']    = "0003";
            $message['error']['errorMessage'] = "입력값이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
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
                echo json_encode($message);
                exit;
		    }
		} elseif($input_type == "barcodeno"){
			$tmp_arr = $this->API->getIdbybarcodeno($univcode, $input_data);
			if(empty($tmp_arr)){
				$message['rCode']                 = "1002";
                $message['error']['errorCode']    = "1002";
                $message['error']['errorMessage'] = "입력한 조합원번호에 해당하는 조합원이 없습니다!";
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
		    }
		} else {
			$message['rCode']                 = "0004";
            $message['error']['errorCode']    = "0004";
            $message['error']['errorMessage'] = "입력구분을 잘못 입력하였습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
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
            echo json_encode($message);
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
            'sLogPath'      => BASEPATH . '../../logs/jnu_api/' . date('Ymd') . 'insertMileage.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnu_api/' . date('Ymd') . 'insertMileage.log';
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

        $univcode    = $this->input->post('univcode',true);     // 대학코드: 00116
        $subunivcode = $this->input->post('subunivcode',true);  // 캠퍼스코드: 001
        $barcodeno   = $this->input->post('barcodeno',true);    // 조합원번호: 7001 1600 0000 256 4
        $usedate     = $this->input->post('usedate',true);      // 영업일: 구매영업일자 년월일(8자리)입력시 자동으로 14자리 변환됨
        $deptcode    = $this->input->post('deptcode',true);     // 매장코드:     (카페아띠 2001000 / 카페지젤 2000500)
        $posno       = $this->input->post('posno',true);        // 포스일련번호: (카페아띠 K1,K2   / 카페지젪 K1)
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
            echo json_encode($message);
            exit;
		}
        if(empty($subunivcode)){
			$message['rCode']                 = "0002";
            $message['error']['errorCode']    = "0002";
            $message['error']['errorMessage'] = "캠퍼스코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}   
        if(empty($barcodeno)){
			$message['rCode']                 = "0003";
            $message['error']['errorCode']    = "0003";
            $message['error']['errorMessage'] = "조합원번호가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
        if(empty($usedate)){
			$message['rCode']                 = "0004";
            $message['error']['errorCode']    = "0004";
            $message['error']['errorMessage'] = "영업일이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		} else if(strlen($usedate) != 8){
			$message['rCode']                 = "0005";
            $message['error']['errorCode']    = "0005";
            $message['error']['errorMessage'] = "영업일이 8자리가 아닙니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }            
        if(empty($deptcode)){
			$message['rCode']                 = "0006";
            $message['error']['errorCode']    = "0006";
            $message['error']['errorMessage'] = "매장코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		} else if($deptcode != "2001000" && $deptcode != "2000500"){
			$message['rCode']                 = "0007";
            $message['error']['errorCode']    = "0007";
            $message['error']['errorMessage'] = "지정된 매장코드가 아닙니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        if(empty($posno)){
			$message['rCode']                 = "0008";
            $message['error']['errorCode']    = "0008";
            $message['error']['errorMessage'] = "키오스크번호가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		} else if(substr($posno,0,1) != "K"){
			$message['rCode']                 = "0009";
            $message['error']['errorCode']    = "0009";
            $message['error']['errorMessage'] = "지정된 키오스크번호가 아닙니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        if(empty($billnumber)){
			$message['rCode']                 = "0010";
            $message['error']['errorCode']    = "0010";
            $message['error']['errorMessage'] = "영수번호가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
        if(empty($usemileage)) $usemileage = 0;  // 마일리지 사용액
        if(empty($amount))     $amount     = 0;  // 이용고적립액
        if(empty($amountsave)) $amountsave = 0;  // 포인트적립액
        if(empty($remark))     $remark = ' ';    // 적요
		
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
        writeLog("[{$sLogFileId}] tmp_result=".json_encode($tmp_result,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        if($tmp_result['USEMILEAGE'] < 0) $tmp_result['MILEAGESAVE'] = 0;                                           // -0 처리
        if(is_float($tmp_result['MILEAGESAVE'])) $tmp_result['MILEAGESAVE'] = round($tmp_result['MILEAGESAVE'],0);  // 반올림

        $message['successMessage'] = $tmp_result;
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
        return;
	}

    //한글인코딩 변경 euc-kr변경
    function convertMSEncoding($str)
    {
        $str =  mb_convert_encoding($str,  "CP949" , "UTF-8");
        $str =  mb_convert_encoding($str,  "UTF-8" , "CP949");  
        return  $str;
    }

	//주문정보 receive api
    public function ci_ver() {
        echo CI_VERSION;
	}
}
/* End of file jnu_api.php */
/* Location: ./application/controllers/jnu_api.php */