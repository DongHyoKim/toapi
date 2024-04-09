<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Commonapi extends CT_Controller {

    private $json_array = array();

	public function __construct(){
		parent::__construct();
		$this->load->model('Pos_api_model','API');
		//print_r($this);
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
        header("Content-type:text/html;charset=utf-8");
      
    }
	
    
    public function index(){
		echo "now route!!";
    }

    //card사용 (상품권/선불카드, 단일/멀티카드 공용) api
    public function use_card_all() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_use_card_all.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_use_card_all.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
        
        $univcode     = $this->input->post('univcode',true);     // 대학코드(5)
		$sub_univcode = $this->input->post('sub_univcode',true); // 캠퍼스코드(3)
		$use_date     = $this->input->post('use_date',true);     // 사용일자(8)
		$order_no     = $this->input->post('order_no',true);     // 주문번호(20자리,일자시간+랜덤번호)
		$order_status = $this->input->post('order_status',true); // 주문상태값(20,70)
		$card_string  = $this->input->post('card_string',true);  // 상품권번호,금액|상품권번호,금액 .....
		
		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 Header에 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$use_date) {      
            $message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "use_date이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$order_no) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "order_no가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$order_status) {      
            $message['rCode'] = "0005";
            $message['error']['errorCode'] = "0005";
            $message['error']['errorMessage'] = "order_status가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$card_string) {      
            $message['rCode'] = "0006";
            $message['error']['errorCode'] = "0006";
            $message['error']['errorMessage'] = "card_string이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        //echo("card_string=".$card_string);

		$tmp_card = array();
		$tmp_card_arr = array();
        $tmp_card = explode("/",$card_string);

	    //print_r($tmp_card);
		//exit;

		foreach($tmp_card as $key => $value){
			// sub1) 카드 조회
            $tmp_card_arr = explode(",",$value);
			/*
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
                $message['error']['errorCode'] = "005";
                $message['error']['errorMessage'] = $tmp_card_arr['0']." ".$tmp_result['MESSAGE'];
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
	        } else {
			// 사용가능하나 사용한도가 초과될 경우 에러메시지 리턴
                //echo($tmp_result['BALANCEAMT']."&");
                //echo($tmp_card_arr['1']."\n");
				
			    if($tmp_result['BALANCEAMT'] < $tmp_card_arr['1']){
                    $message['rCode'] = "0006";
                    $message['error']['errorCode'] = "006";
                    $message['error']['errorMessage'] = $tmp_card_arr['0']." 금액 초과".$tmp_result['BALANCEAMT'];
                    writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                    echo json_encode($message);
                    exit;
				}
			}
			*/
			// sub2 카드 사용
			
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
		        	'store_code'   => EWHACOOP_ESHOP_DEPARTMENT_CODE, // 매장코드
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
		        	'store_code'   => EWHACOOP_ESHOP_DEPARTMENT_CODE, // 매장코드
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
                $message['rCode'] = "0007";
                $message['error']['errorCode'] = "007";
                $message['error']['errorMessage'] = $tmp_card_arr['0']." ".$tmp_result['MESSAGE'];
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
	        }
            /*
			// 사용잔액 표시
			if(empty($message['message_detail'])){
                $message['message_detail'] = $tmp_result['CARDNO']."번 카드 ".$tmp_result['BALANCEAMT']."원 \n";
			} else {
                $message['message_detail'] .= $tmp_result['CARDNO']."번 카드 ".$tmp_result['BALANCEAMT']."원 \n";
			}
			*/
		}
        // Ends of Log Write
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
        $message['message'] = count($tmp_card)."개의 상품권/선불카드 사용완료!!!";
		
		echo json_encode($message);
		//print_r($results);
        return;
		
    }

    //card조회(상품권/선불카드, 단일/멀티카드 공용) api
    public function check_card_all() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_check_card_all.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_check_card_all.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
        
        $univcode     = $this->input->post('univcode',true);
		$sub_univcode = $this->input->post('sub_univcode',true);
		$card_string  = $this->input->post('card_string',true);
		
		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 Header에 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$card_string) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "card번호,사용 금액군이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
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
                $message['error']['errorCode'] = "005";
                $message['error']['errorMessage'] = $tmp_card_arr['0']." ".$tmp_result['MESSAGE'];
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
	        } else {
			// 사용가능하나 사용한도가 초과될 경우 에러메시지 리턴
                //echo($tmp_result['BALANCEAMT']."&");
                //echo($tmp_card_arr['1']."\n");
				
			    if($tmp_result['BALANCEAMT'] < $tmp_card_arr['1']){
                    $message['rCode'] = "0006";
                    $message['error']['errorCode'] = "006";
                    $message['error']['errorMessage'] = $tmp_card_arr['0']." 금액 초과".$tmp_result['BALANCEAMT'];
                    writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                    echo json_encode($message);
                    exit;
				}
			}

			if(empty($message['message_detail'])){
                $message['message_detail'] = $tmp_result['CARDNO']."번 카드 ".$tmp_result['BALANCEAMT']."원 \n";
			} else {
                $message['message_detail'] .= $tmp_result['CARDNO']."번 카드 ".$tmp_result['BALANCEAMT']."원 \n";
			}
		}
        // Ends of Log Write
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
        $message['message'] = count($tmp_card)."개의 상품권이 모두 사용가능!!";
		
		echo json_encode($message);
		//print_r($results);
        return;
		
    }

    //card_gift사용 api
    public function use_card_gift() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_use_card.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_use_card.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
        
        $univcode     = $this->input->post('univcode',true);     // 대학코드(5)
		$sub_univcode = $this->input->post('sub_univcode',true); // 캠퍼스코드(3)
		$use_date     = $this->input->post('use_date',true);     // 사용일자(8)
		$order_no     = $this->input->post('order_no',true);     // 주문번호(20자리,일자시간+랜덤번호)
		$order_status = $this->input->post('order_status',true); // 주문상태값(20,70)
		$in_string    = $this->input->post('in_string',true);    // 상품권번호,금액|상품권번호,금액 .....
        // $in_string "|"를 "/"로 치환
		if(strpos($in_string,"|") === FALSE) $in_string = str_replace("|", "/", $in_string);

		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 Header에 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$use_date) {      
            $message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "사용일자가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$order_no) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0005";
            $message['error']['errorMessage'] = "주문번호가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        if (!$order_status) {      
            $message['rCode'] = "0005";
            $message['error']['errorCode'] = "0006";
            $message['error']['errorMessage'] = "주문상태값이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        if (!$in_string) {      
            $message['rCode'] = "0006";
            $message['error']['errorCode'] = "0006";
            $message['error']['errorMessage'] = "상품권 사용정보(번호,금액)이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        //echo("card_string=".$card_string);

		$dbName = "U_VOUCHER";
		$spName = "SP_VOUCHER_APIESHOP";
        
        $params_array = array(
            'univcode'     => $univcode,     // 대학코드(5)
		    'sub_univcode' => $sub_univcode, // 캠퍼스코드(3)
		    'use_date'     => $use_date,     // 사용일자(8)
			'store_code'   => EWHACOOP_ESHOP_DEPARTMENT_CODE, // 매장코드
		    'order_no'     => $order_no,     // 주문번호(20자리,일자시간+랜덤번호)
		    'order_status' => $order_status, // 주문상태값(20,70)
		    'in_string'    => $in_string,    // 사용금액(음수:반품)
		);
        //print_r($params_array);
		//exit;
            
	    // 상품권 사용
    	$tmp_result = $this->API->execSp($univcode, $dbName, $spName, $params_array);
        $tmp_result = convertMSEncoding($tmp_result);
        //print_r($tmp_result);
		//exit;

        if($tmp_result['VALUE'] == "FALSE"){
            $message['rCode']                 = "0007";
            $message['error']['errorCode']    = "0007";
            $message['error']['errorMessage'] = "해당 상품권(들)은 사용할수 없습니다. 생활협동조합 사무국에 문의하세요.";
		} else {
            $message['rCode']       = RES_CODE_SUCCESS;
            $message['card_no']     = $tmp_result['CARDNO'];
		    $message['message']     = $tmp_result['MESSAGE'];
		    $message['balance_amt'] = $tmp_result['BALANCEAMT'];
		}
		
		writeLog("[{$sLogFileId}] message=". json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        // Ends of Log Write
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		echo json_encode($message);
		//print_r($message);
        return;
    }

    //card_prepaid사용 api
    public function use_card_prepaid() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_use_card.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_use_card.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
        
        $univcode     = $this->input->post('univcode',true);     // 대학코드(5)
		$sub_univcode = $this->input->post('sub_univcode',true); // 캠퍼스코드(3)
		$use_date     = $this->input->post('use_date',true);     // 사용일자(8)
		$card_no      = $this->input->post('card_no',true);      // 카드번호(16자리)
		$order_no     = $this->input->post('order_no',true);     // 주문번호(20자리,일자시간+랜덤번호)
		$order_status = $this->input->post('order_status',true); // 주문상태값(20,70)
		$use_amount   = $this->input->post('use_amount',true);   // 사용금액(음수:반품)

		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 Header에 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$use_date) {      
            $message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "사용일자가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$card_no) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "선불카드 번호가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$order_no) {      
            $message['rCode'] = "0005";
            $message['error']['errorCode'] = "0005";
            $message['error']['errorMessage'] = "주문번호가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        if (!$order_status) {      
            $message['rCode'] = "0006";
            $message['error']['errorCode'] = "0006";
            $message['error']['errorMessage'] = "주문상태값이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        if (!$use_amount) {      
            $message['rCode'] = "0006";
            $message['error']['errorCode'] = "0006";
            $message['error']['errorMessage'] = "사용금액이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        //echo("card_string=".$card_string);

		$dbName = "U_PREPAID";
		$spName = "SP_PREPAID_APIESHOP";
        
        $params_array = array(
            'univcode'     => $univcode,     // 대학코드(5)
		    'sub_univcode' => $sub_univcode, // 캠퍼스코드(3)
		    'use_date'     => $use_date,     // 사용일자(8)
			'store_code'   => EWHACOOP_ESHOP_DEPARTMENT_CODE, // 매장코드
		    'card_no'      => $card_no,      // 카드번호(16자리)
		    'order_no'     => $order_no,     // 주문번호(20자리,일자시간+랜덤번호)
		    'order_status' => $order_status, // 주문상태값(20,70)
		    'use_amount'   => $use_amount,   // 사용금액(음수:반품)
		);
        //print_r($params_array);
		//exit;
            
	    // 선불카드 사용
    	$tmp_result = $this->API->execSp($univcode, $dbName, $spName, $params_array);
        $tmp_result = convertMSEncoding($tmp_result);
        //print_r($tmp_result);
		//exit;

        if($tmp_result['VALUE'] == "FALSE"){
            $message['rCode']                 = "0007";
            $message['error']['errorCode']    = "0007";
            $message['error']['errorMessage'] = "해당 선불카드는 사용할수 없습니다. 생활협동조합 사무국에 문의하세요.";
		} else {
            $message['rCode']       = RES_CODE_SUCCESS;
            $message['card_no']     = $tmp_result['CARDNO'];
		    $message['message']     = $tmp_result['MESSAGE'];
		    $message['balance_amt'] = $tmp_result['BALANCEAMT'];
		}
		
		writeLog("[{$sLogFileId}] message=". json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        // Ends of Log Write
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		echo json_encode($message);
		//print_r($message);
        return;
    }

	//card정보조회 api
    public function info_card($arr) {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_info_card.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_info_card.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        //$arr = $this->input->post('arr',true);
        
		$univcode     = $arr['univcode'];
		$sub_univcode = $arr['sub_univcode'];
		$card_type    = $arr['card_type'];
		$card_no      = $arr['card_no'];
		
		
		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$card_type) {      
            $message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "card_type이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$card_no) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "card_no가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        
        if($univcode == "00113"){
		    if($card_type == "G" || $card_type == 'g'){
			    $dbName = "U_VOUCHER";
			    $spName = "SP_VOUCHER_APIINFO";
		    } else if($card_type == "P" || $card_type == "p"){
			    $dbName = "U_PREPAID";
			    $spName = "SP_PREPAID_APIINFO";
		    }
		} else if($univcode == "00123"){
            if($card_type == "G" || $card_type == 'g'){
			    $dbName = "U00123_VOUCHER";
			    $spName = "SP_VOUCHER_APIINFO";
		    } else if($card_type == "P" || $card_type == "p"){
			    $dbName = "U00123_PREPAID";
			    $spName = "SP_PREPAID_APIINFO";
		    }
		} else if($univcode == "00116"){
            if($card_type == "G" || $card_type == 'g'){
			    $dbName = "U_VOUCHER";
			    $spName = "SP_VOUCHER_APIINFO";
		    } else if($card_type == "P" || $card_type == "p"){
			    $dbName = "U_PREPAID";
			    $spName = "SP_PREPAID_APIINFO";
		    }
        }
        $params_array = array(
			'univcode' => $univcode,
			'card_no'  => $card_no,
		);

    	$tmp_result = $this->API->execSp($univcode, $dbName, $spName, $params_array);
        $tmp_result = convertMSEncoding($tmp_result);
		
		
        
		writeLog("[{$sLogFileId}] result=" . json_encode($tmp_result,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        
		if($card_type == "G" || $card_type == "g"){  // 상품권일 경우
            $results = array(
	    		'value'        => $tmp_result['VALUE'],       // 사용유무
				'card_no'      => $tmp_result['VOUCHERNO'],   // 상품권번호
				'card_name'    => $tmp_result['VOUCHERNAME'], // 상품권명
				'issue_amount' => $tmp_result['ISSUEAMOUNT'], // 발급금액
				'issue_date'   => $tmp_result['ISSUEDATE'],   // 발급일자
				'start_date'   => $tmp_result['STARTDATE'],   // 유효기간S
                'end_date'     => $tmp_result['ENDDATE'],     // 유효기간E
				'remark1'      => $tmp_result['REMARK1'],     // 발행내용
				'remark2'      => $tmp_result['REMARK2'],     // 발행자
			    'balance_amt'  => $tmp_result['BALANCEAMT'],  // 잔액
		    );
	    } else {                                     // 선불카드의 경우
            $results = array(
	    		'value'        => $tmp_result['VALUE'],       // 사용유무
                'card_no'      => $tmp_result['CARDNO'],      // 선불카드번호
				'card_name'    => $tmp_result['CARDNAME'],    // 선불카드명
                'issue_date'   => $tmp_result['ISSUEDATE'],   // 발급일자
			    'balance_amt'  => $tmp_result['BALANCEAMT'],  // 잔액
		    );
        }

        // Ends of Log Write
		writeLog("[{$sLogFileId}] result=" . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		//print_r($results);
		//exit;
        return json_encode($results);
    }

    //card_multi조회 api
    public function check_card_multi() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_check_card_multi.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_check_card_multi.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
        
        $univcode     = $this->input->post('univcode',true);
		$sub_univcode = $this->input->post('sub_univcode',true);
		$card_string  = $this->input->post('card_string',true);
		
		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 Header에 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$card_string) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "card번호,금액군이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        //echo("card_string=".$card_string);

		$dbName = "U_VOUCHER";
		$spName = "SP_VOUCHER_APISEL";
        
        $card_arr = array();
		$tmp_card = array();
		$tmp_card_arr = array();
		// 멀티조회에 2건이상의 데이터만 사용(박해선과 약속 2022.02.05)
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
            
			// 상품권별 사용여부 조회
    	    $tmp_result = $this->API->execSp($univcode, $dbName, $spName, $params_array);
            $tmp_result = convertMSEncoding($tmp_result);
            //print_r($tmp_result);
		    //exit;

            
   			writeLog("[{$sLogFileId}] result".$key."=". json_encode($tmp_result,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            // 사용불가의 경우 에러메시지 리턴
			if($tmp_result['VALUE'] == "FALSE"){
                $message['rCode'] = "0005";
                $message['error']['errorCode'] = "005";
                $message['error']['errorMessage'] = $tmp_card_arr['0']." ".$tmp_result['MESSAGE'];
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
	        } else {
			// 사용가능하나 사용한도가 초과될 경우 에러메시지 리턴
                //echo($tmp_result['BALANCEAMT']."&");
                //echo($tmp_card_arr['1']."\n");
				
			    if($tmp_result['BALANCEAMT'] < $tmp_card_arr['1']){
                    $message['rCode'] = "0006";
                    $message['error']['errorCode'] = "006";
                    $message['error']['errorMessage'] = $tmp_card_arr['0']." 금액 초과, 상품권 한도".$tmp_result['BALANCEAMT'];
                    writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                    echo json_encode($message);
                    exit;
				}
			}
		}
        // Ends of Log Write
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
        $message['message'] = count($tmp_card)."개의 상품권이 모두 사용가능!!";
		echo json_encode($message);
		//print_r($results);
        return;
		
    }
	
	//card조회 api
    public function check_card() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_check_card.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_check_card.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        
        $univcode     = $this->input->post('univcode',true);
		$sub_univcode = $this->input->post('sub_univcode',true);
		//$card_type    = $this->input->post('card_type',true);
		$card_no      = $this->input->post('card_no',true);
		
        if(substr($card_no,0,1) == "4"){
			$card_type = "G";
		} elseif(substr($card_no,0,1) == "5"){
            $card_type = "P";
		} else {
            $message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "card_type이 적합하지 않습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}

		/*
        $univcode     = $_POST['UnivCode'];
		$sub_univcode = $_POST['sub_univcode'];
		$card_type    = $_POST['card_type'];
		$card_no      = $_POST['card_no'];
        */

		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode가 Header에 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		if (!$sub_univcode) {      
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "sub_univcode가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		/*
		if (!$card_type) {      
            $message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "card_type이 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
		*/
		if (!$card_no) {      
            $message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "card_no가 없습니다.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        
        if($card_type == "G" || $card_type == 'g'){
			$dbName = "U_VOUCHER";
			$spName = "SP_VOUCHER_APISEL";
		} else if($card_type == "P" || $card_type == "p"){
			$dbName = "U_PREPAID";
			$spName = "SP_PREPAID_APISEL";
		}

        $params_array = array(
			'univcode' => $univcode,
			'card_no'  => $card_no,
		);

    	$tmp_result = $this->API->execSp($univcode, $dbName, $spName, $params_array);
        $tmp_result = convertMSEncoding($tmp_result);        
        
		writeLog("[{$sLogFileId}] result=" . json_encode($tmp_result,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

		if($card_type == "G" || $card_type == "g"){
            $results = array(
	    		'value'       => $tmp_result['VALUE'],
                'card_no'     => $tmp_result['VOUCHERNO'],
                'message'     => $tmp_result['MESSAGE'],
			    'balance_amt' => $tmp_result['BALANCEAMT'],
		    );
	    } else {
            $results = array(
	    		'value'       => $tmp_result['VALUE'],
                'card_no'     => $tmp_result['CARDNO'],
                'message'     => $tmp_result['MESSAGE'],
			    'balance_amt' => $tmp_result['BALANCEAMT'],
		    );
        }

        // Ends of Log Write
		writeLog("[{$sLogFileId}] result=" . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		echo json_encode($results);
		//print_r($results);
        return;
    }

	//주문정보 deploy api
    public function deploy_card() {
        
		$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_deploy_card.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/gftprecard/' . date('Ymd') . '_deploy_card.log';
        $bLogable    = true;

    	writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        //$receiveHeader = apache_request_headers();
        // UnivCode는 헤더에서 받아오기로 함 2020-02-26 고병수차장 autoload->database.php에서 처리함.
        
		// json data Receive
        //$deploy_arr = json_decode(file_get_contents('php://input'), true);  // json data name :order

        // array 파라메타 정의
        // univcode            char(05)        대학코드(필수,헤더,pkey)
        // sub_univcode        char(03)        캠퍼스코드(필수,헤더,pkey)
		// card_no             varchar(16)     카드번호(필수,pkey)
		// card_title          varchar(50)     카드제목(not null)
		// expire_term_start   char(08)        유효기간_start
		// expire_term_end     char(08)        유효기간_end
		// issue_date          char(08)        발급일자
		// issuer_name         varchar(30)     발급기관명칭
		// card_amount         float           금액
		// card_message        varchar(100)    카드메시지

        $params = array();
		//$params = file_get_contents('php://input');
		$params = $this->input->post('sPost',true);
        $params_arr = explode('|',$params);
		
        //print_r($params_arr);
		//exit;

		$phone_no          = $params_arr['0'];
		$univcode          = $params_arr['1'];
		$sub_univcode      = "001";
		$card_no           = $params_arr['2'];
		$card_title        = $params_arr['3'];
		$expire_term_start = $params_arr['4'];
		$expire_term_end   = $params_arr['5'];
		$issue_date        = $params_arr['6'];
		$issuer_name       = $params_arr['7'];
		$card_amount       = $params_arr['8'];
		$card_message      = $params_arr['9'];

		/*
		$message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
		*/
        $results = array(
			'VALUE'   => "TRUE",
            'CARDNO'  => $card_no,
            'MESSAGE' => "",
		);

        if($univcode == "00113"){
            $univ_name = "이화상점";
		} else if($univcode == "00123"){
            $univ_name = "충남대학교소비자";
		}  else if($univcode == "00116"){
            $univ_name = "전남대학교생활협동조합";
		}

        if(substr($card_no,0,1) == "4"){
            $card_type = "G";
		} else if(substr($card_no,0,1) == "5"){
            $card_type = "P";
		}

		//print_r($result);
		//exit;

		if (!$univcode) {      
            $results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "univcode가 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }
		if (!$card_no) {
			$results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "card_no가 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }
        if (!$card_title) {      
			$results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "card_title이 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }
        if (!$expire_term_start && $card_type == "G") {      
			$results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "expire_term_start가 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }
        if (!$expire_term_end && $card_type == "G") {      
			$results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "expire_term_end가 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }
        if (!$issue_date) {      
			$results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "issue_date이 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }
        if (!$issuer_name) {      
			$results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "issuer_name이 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }
        if (!$card_amount) {      
			$results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "card_amount가 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }
        if (!$phone_no) {      
			$results['VALUE']   = "FALSE";
            $results['MESSAGE'] = "phone_no가 없습니다.";
            writeLog("[{$sLogFileId}] result=".json_encode($results['MESSAGE'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($results);
            exit;
        }

        $deploy_arr = array(
		    'univcode'          => $univcode,
			'sub_univcode'      => $sub_univcode,
            'card_no'           => $card_no,
            'card_title'        => $card_title,
			'expire_term_start' => $expire_term_start,
            'expire_term_end'   => $expire_term_end,
            'issue_date'        => $issue_date,
			'issuer_name'       => $issuer_name,
			'card_amount'       => $card_amount,
			'card_message'      => $card_message,
			'card_type'         => $card_type,
			'phone_no'          => $phone_no,
			'univ_name'         => $univ_name,
		);

        //print_r($deploy_arr);
		//exit;

        writeLog("[{$sLogFileId}] deploy_arr=" . json_encode($deploy_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        /*
		$univcode          = $_POST['univcode'];
		$sub_univcode      = $_POST['sub_univcode'];
		$card_no           = $_POST['card_no'];
		$card_title        = $_POST['card_title'];
		$expire_term_start = $_POST['expire_term_start'];
		$expire_term_end   = $_POST['expire_term_end'];
		$issue_date        = $_POST['issue_date'];
		$issuer_name       = $_POST['issuer_name'];
		$card_amount       = $_POST['card_amount'];
		$card_message      = $_POST['card_message'];
        $card_type         = $_POST['card_type'];
		$phone_no          = $_POST['phone_no'];
		$univ_name         = $_POST['univ_name'];
		*/

        // 알림톡 발송 함수 호출
    	require_once(APPPATH.'controllers/alimtalk.php'); //include controller
        $alim_msg = new Alimtalk();
        $alim_result = $alim_msg->alimtalk_send($deploy_arr);
		//$alimtalk = json_decode(($alim_result));
        $alimtalk = (array)json_decode($alim_result);

		//print_r($alimtalk);
		//exit;

		//model로 던져 DB에 트랜잭션 처리를 위해 한방에 처리(단 널배열 처리방법 고민 is_array로 해결함.)
		// MS-SQL 2019이후 일부 디비에서 파라메타를 ,,, 이렇게 못넣어서 재구성함.
		
		//$insertResult = $this->API->insertDB($deploy_arr);

		if ($alimtalk['code'] != "success") {
            $results['VALUE']   = "FALSE";
            $results['MESSAGE'] = $alimtalk['msg'];
        }
		//print_r($results);
		//exit;

        // Ends of Log Write
		writeLog("[{$sLogFileId}] results=" . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

        echo json_encode($results);
		//return json_encode($results);
		return;
    }
	
	//주문정보 receive api
    public function ci_ver() {
        echo CI_VERSION;
	}
}    