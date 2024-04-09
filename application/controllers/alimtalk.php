<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alimtalk extends CT_Controller {

    private $json_array = array();

	public function __construct(){
		parent::__construct();
        $this->load->helper('alimtalk');
		$this->load->model('Alimtalk_model','API');
		//print_r($this);
    }
	
    
    public function index(){
    }

	//알림톡 발송
    public function alimtalk_send($arr) {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/alimtalk_send/' . date('Ymd') . '_alimtalk_send.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/alimtalk_send/' . date('Ymd') . '_alimtalk_send.log';
        $bLogable    = true;

        $results = array(
            'code' => 'success',
            'msg' => '성공',
            'data' => null
        );

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        //writeLog("[{$sLogFileId}] result=" . json_encode($arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        $univcode          = $arr['univcode'];
		$sub_univcode      = $arr['sub_univcode'];
		$card_no           = $arr['card_no'];
		$card_title        = $arr['card_title'];
		$expire_term_start = $arr['expire_term_start'];
		$expire_term_end   = $arr['expire_term_end'];
		$issue_date        = $arr['issue_date'];
		$issuer_name       = $arr['issuer_name'];
		$card_amount       = $arr['card_amount'];
		$card_message      = $arr['card_message'];
        $card_type         = $arr['card_type'];
		$phone_no          = $arr['phone_no'];
		$univ_name         = $arr['univ_name'];

		//임시조치 대학교생활협동조합생활협동조합 분리
		if($univcode == "00113") $univ_name = str_replace("생활협동조합","",$univ_name);
		
		if (!$univcode) {      
            $results = [
				'code' => 'fail',
				'msg' => 'univcode가 없습니다.',
				'data' => null
			];

            writeLog("[{$sLogFileId}] result=".json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), $sLogPath, $bLogable);
            //echo json_encode($results);
            //exit;
        }

        $alimitalk_arr = [
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
		];

		writeLog("[{$sLogFileId}] alimitalk_arr=" . json_encode($alimitalk_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

	
		//관리자에게 알림톡 전송
		if( $card_type == "P")  $alimtalk_msg = $this->config->item('ALIMTALK_PREPAIDCARD_MSG')[$univcode];//선불카드
		else $alimtalk_msg = $this->config->item('ALIMTALK_GIFTCARD_MSG')[$univcode]; //상품권
		

		//암호화 규칙 
		$tmp_data = "{$univcode}|{$sub_univcode}|{$card_no}|{$card_type}"; 
		$aes_data = rawurlencode(openssl_encrypt($tmp_data,'AES-256-CBC',AES_KEY,false,str_repeat(chr(0),16))); //AES-256 암호화

		//공통
		$alimtalk_msg = str_replace('#{고객폰번호}', $phone_no, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{대학명}', $univ_name, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{카드제목}', $card_title, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{카드번호}', $card_no, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{발급기관명칭}', $issuer_name, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{금액}', number_format($card_amount), $alimtalk_msg);
		$alimtalk_msg = str_replace('#{발급일자}', $issue_date, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{메시지}', $card_message, $alimtalk_msg);
		$alimtalk_fallback_sms_msg = $alimtalk_msg . " #{링크주소} " ;
		$alimtalk_msg = str_replace('#{링크주소}', "https://toapi.cway.co.kr/alimtalk/gftpre_card?UnivCode={$univcode}&sub_univcode={$sub_univcode}&enc_data={$aes_data}", $alimtalk_msg);
		$alimtalk_fallback_sms_msg = str_replace('#{링크주소}', "https://toapi.cway.co.kr/alimtalk/gftpre_card?UnivCode={$univcode}&sub_univcode={$sub_univcode}&enc_data={$aes_data}", $alimtalk_fallback_sms_msg);
		
		if( $card_type == "G") { //상품권
			$alimtalk_msg = str_replace('#{유효기간S}', $expire_term_start, $alimtalk_msg);
			$alimtalk_msg = str_replace('#{유효기간E}', $expire_term_end, $alimtalk_msg);
			$alimtalk_fallback_sms_msg = str_replace('#{유효기간S}', $expire_term_start, $alimtalk_fallback_sms_msg);
			$alimtalk_fallback_sms_msg = str_replace('#{유효기간E}', $expire_term_end, $alimtalk_fallback_sms_msg);
		}
		

		if( $card_type == "P") { //선불카드
			$template_code = $this->config->item('ALIMTALK_PREPAIDCARD_TEMPLATE_CODE')[$univcode];
			$template_name = $this->config->item('ALIMTALK_PREPAIDCARD_TEMPLATE_NAME')[$univcode];
			$button_name = "선불카드바코드링크";
			$button_link = "https://toapi.cway.co.kr/alimtalk/gftpre_card?UnivCode={$univcode}&sub_univcode={$sub_univcode}&enc_data={$aes_data}";
		} elseif( $card_type == "G") { //상품권
			$template_code = $this->config->item('ALIMTALK_GIFTCARD_TEMPLATE_CODE')[$univcode];
			$template_name = $this->config->item('ALIMTALK_GIFTCARD_TEMPLATE_NAME')[$univcode];
			$button_name = "상품권바코드링크";
			$button_link = "https://toapi.cway.co.kr/alimtalk/gftpre_card?UnivCode={$univcode}&sub_univcode={$sub_univcode}&enc_data={$aes_data}";
		}
				
		$button = ["name"=>$button_name ,
					  "type"=>"WL",
					  "url_mobile"=>$button_link,
					  "url_pc"=>$button_link
			];
		
		//알림톡 전송		
		$alimtal_arr = [				
				'alimtalk_id'              => $this->config->item('ALIMTALK_ID')[$univcode],
				'alimtalk_key'             => $this->config->item('ALIMTALK_KEY')[$univcode],
				'phone'					=> $phone_no,
				'custMsgSn'				=> $this->config->item('ALIMTALK_ID')[$univcode],				
				'msg'						=> $alimtalk_msg,
				'alimtalk_fallback_sms_msg'	=> $alimtalk_fallback_sms_msg,	
				'sender_key'			=> $this->config->item('ALIMTALK_SENDER_KEY')[$univcode],
				'univcode'				=> $univcode,
				'sub_univcode'        => $sub_univcode,
				'template_code'		=> $template_code,
				'template_name'       => $template_name,
				'button'					=> $button,
				'sms_number'					=> $this->config->item('ALIMTALK_FALLBACK_SMS_NUMBER')[$univcode],

			];

		$sendAlimtalk = sendAlimtalk(
			$alimtal_arr
		);
		
		//알림톡 발송로그 INSERT 동작
		$resultParam = [			
			'sender_key'           => $this->config->item('ALIMTALK_SENDER_KEY')[$univcode],
			'phone'                 => $phone_no,
			'template_code'      => $template_code,
			'msg'                   => $alimtalk_msg,
			'univcode'			  => $univcode,
			'sub_univcode'       => $sub_univcode,
			'user_key'			  => '',
			'mtype'				  => '',
			'auth_no'			  => '',
			'result_status'        => $sendAlimtalk['data']['result_status'],
			'result_code'          => $sendAlimtalk['data']['result_code'],
			'result_alt_code'      => $sendAlimtalk['data']['result_alt_code'],	
			'altMsg'					=> $sendAlimtalk['data']['altMsg'],	
			'result'                  => $sendAlimtalk['data']['result'],
		];

		$data_list = $this->API->insertAlimtalkLog($resultParam);
		
		$results = [
			'code' => $sendAlimtalk['data']['result_status'],
			'msg' => $sendAlimtalk['data']['result_alt_code'],
			'data' => $sendAlimtalk['data']['altMsg'],
		];
		
		/*
        $re_arr = array(
			'code' => "success",
			'msg'  => "0000",
			'data' => "",
		);
		*/


		// Ends of Log Write
		writeLog("[{$sLogFileId}] result=" . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		return json_encode($results);
		
	} 

	//알림톡 발송
    public function alimtalk_send_test() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/alimtalk_send_test/' . date('Ymd') . '_alimtalk_send.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/alimtalk_send_test/' . date('Ymd') . '_alimtalk_send.log';
        $bLogable    = true;

        $results = array(
            'code' => 'success',
            'msg' => '성공',
            'data' => null
        );

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        writeLog("[{$sLogFileId}] result=" . json_encode($_POST,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);


        $univcode				= $this->input->post('UnivCode', TRUE); 
		$sub_univcode			= $this->input->post('sub_univcode', TRUE); 
		$card_no					= $this->input->post('card_no', TRUE); 
		$card_title				= $this->input->post('card_title', TRUE); 
		$expire_term_start		= $this->input->post('expire_term_start', TRUE); 
		$expire_term_end		= $this->input->post('expire_term_end', TRUE); 
		$issue_date				= $this->input->post('issue_date', TRUE); 
		$issuer_name			= $this->input->post('issuer_name', TRUE); 
		$card_amount			= $this->input->post('card_amount', TRUE); 
		$card_message			= $this->input->post('card_message', TRUE); 
        $card_type				= $this->input->post('card_type', TRUE); 
		$phone_no				= $this->input->post('phone_no', TRUE); 
		$univ_name				= $this->input->post('univ_name', TRUE); 		
		
		if (!$univcode) {      
            $results = [
				'code' => 'fail',
				'msg' => 'univcode가 없습니다.',
				'data' => null
			];

            writeLog("[{$sLogFileId}] result=".json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), $sLogPath, $bLogable);
            //echo json_encode($results);
            //exit;
        }

        $alimitalk_arr = [
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
            'card_type'      => $card_type,
			'phone_no'      => $phone_no,
			'univ_name'      => $univ_name,
		];

		writeLog("[{$sLogFileId}] alimitalk_arr=" . json_encode($alimitalk_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

	
		//관리자에게 알림톡 전송
		if( $card_type == "P")  $alimtalk_msg = $this->config->item('ALIMTALK_PREPAIDCARD_MSG')[$univcode];//선불카드
		else $alimtalk_msg = $this->config->item('ALIMTALK_GIFTCARD_MSG')[$univcode]; //상품권		
		

		//암호화 규칙 
		$tmp_data = "{$univcode}|{$sub_univcode}|{$card_no}|{$card_type}"; 
		$aes_data = rawurlencode(openssl_encrypt($tmp_data,'AES-256-CBC',AES_KEY,false,str_repeat(chr(0),16))); //AES-256 암호화

		//공통
		$alimtalk_msg = str_replace('#{고객폰번호}', $phone_no, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{대학명}', $univ_name, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{카드제목}', $card_title, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{카드번호}', $card_no, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{발급기관명칭}', $issuer_name, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{금액}', number_format($card_amount), $alimtalk_msg);
		$alimtalk_msg = str_replace('#{발급일자}', $issue_date, $alimtalk_msg);
		$alimtalk_msg = str_replace('#{메시지}', $card_message, $alimtalk_msg);

		$alimtalk_fallback_sms_msg = $alimtalk_msg . " #{링크주소} " ;

		$alimtalk_msg = str_replace('#{링크주소}', "https://toapi.cway.co.kr/alimtalk/gftpre_card?UnivCode={$univcode}&sub_univcode={$sub_univcode}&enc_data={$aes_data}", $alimtalk_msg);

		$alimtalk_fallback_sms_msg = str_replace('#{링크주소}', "https://toapi.cway.co.kr/alimtalk/gftpre_card?UnivCode={$univcode}&sub_univcode={$sub_univcode}&enc_data={$aes_data}", $alimtalk_fallback_sms_msg);
		
		
		if( $card_type == "G") { //상품권
			$alimtalk_msg = str_replace('#{유효기간S}', $expire_term_start, $alimtalk_msg);
			$alimtalk_msg = str_replace('#{유효기간E}', $expire_term_end, $alimtalk_msg);
			$alimtalk_fallback_sms_msg = str_replace('#{유효기간S}', $expire_term_start, $alimtalk_fallback_sms_msg);
			$alimtalk_fallback_sms_msg = str_replace('#{유효기간E}', $expire_term_end, $alimtalk_fallback_sms_msg);
		}		

		if( $card_type == "P") { //선불카드
			$template_code = $this->config->item('ALIMTALK_PREPAIDCARD_TEMPLATE_CODE')[$univcode];
			$template_name = $this->config->item('ALIMTALK_PREPAIDCARD_TEMPLATE_NAME')[$univcode];
			$button_name = "선불카드바코드링크";
			$button_link = "https://toapi.cway.co.kr/alimtalk/gftpre_card?UnivCode={$univcode}&sub_univcode={$sub_univcode}&enc_data={$aes_data}";
		} elseif( $card_type == "G") { //상품권
			$template_code = $this->config->item('ALIMTALK_GIFTCARD_TEMPLATE_CODE')[$univcode];
			$template_name = $this->config->item('ALIMTALK_GIFTCARD_TEMPLATE_NAME')[$univcode];
			$button_name = "상품권바코드링크";
			$button_link = "https://toapi.cway.co.kr/alimtalk/gftpre_card?UnivCode={$univcode}&sub_univcode={$sub_univcode}&enc_data={$aes_data}";
		}
				
		$button = ["name"=>$button_name ,
					  "type"=>"WL",
					  "url_mobile"=>$button_link,
					  "url_pc"=>$button_link
			];
		
		//알림톡 전송		
		$alimtal_arr = [				
				'alimtalk_id'              => $this->config->item('ALIMTALK_ID')[$univcode],
				'alimtalk_key'             => $this->config->item('ALIMTALK_KEY')[$univcode],
				'phone'					=> $phone_no,
				'custMsgSn'				=> $this->config->item('ALIMTALK_ID')[$univcode],				
				'msg'						=> $alimtalk_msg,
				'alimtalk_fallback_sms_msg'	=> $alimtalk_fallback_sms_msg,				
				'sender_key'			=> $this->config->item('ALIMTALK_SENDER_KEY')[$univcode],
				'univcode'				=> $univcode,
				'sub_univcode'        => $sub_univcode,
				'template_code'		=> $template_code,
				'template_name'       => $template_name,
				'button'					=> $button,
				'sms_number'			=> $this->config->item('ALIMTALK_FALLBACK_SMS_NUMBER')[$univcode],
			];

		$sendAlimtalk = sendAlimtalk(
			$alimtal_arr
		);
		
		
		//알림톡 발송로그 INSERT 동작
		$resultParam = [			
			'sender_key'           => $this->config->item('ALIMTALK_SENDER_KEY')[$univcode],
			'phone'                => $phone_no,
			'template_code'     => $template_code,
			'msg'                   => $alimtalk_msg,
			'univcode'			  => $univcode,
			'sub_univcode'       => $sub_univcode,
			'user_key'			  => '',
			'mtype'				  => '',
			'auth_no'			  => '',
			'result_status'        => $sendAlimtalk['data']['result_status'],
			'result_code'         => $sendAlimtalk['data']['result_code'],
			'result_alt_code'     => $sendAlimtalk['data']['result_alt_code'],	
			'altMsg'				  => $sendAlimtalk['data']['altMsg'],	
			'result'                 => $sendAlimtalk['data']['result'],
		];

		$data_list = $this->API->insertAlimtalkLog($resultParam);
		
		$results = [
			'code' => $sendAlimtalk['data']['result_status'],
			'msg' => $sendAlimtalk['data']['result_alt_code'],
			'data' => $sendAlimtalk['data']['altMsg'],
		];
		
		/*
        $re_arr = array(
			'code' => "success",
			'msg'  => "0000",
			'data' => "",
		);
		*/

		// Ends of Log Write
		writeLog("[{$sLogFileId}] result=" . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		echo json_encode($results);
		
	} 
	
	
	//상품권
	public function gftpre_card() {
		
		$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/alimtalk_info/' . date('Ymd') . '_alimtalk.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/alimtalk_info/' . date('Ymd') . '_alimtalk.log';
        $bLogable    = true;

		 writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

		$univcode				= $this->input->get('UnivCode', TRUE);
		$sub_univcode			= $this->input->get('sub_univcode', TRUE); 
		$enc_data				= $this->input->get('enc_data', TRUE); 

		if (!$univcode || !$sub_univcode || !$enc_data) {        
			writeLog("[{$sLogFileId}] result=".json_encode($_GET, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), $sLogPath, $bLogable);
			writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		   js_alert_kakao_close('필수 항목이 누락되었습니다.');           
        }
		
		$decrypt_data = openssl_decrypt(rawurldecode($enc_data),"AES-256-CBC",AES_KEY,false,str_repeat(chr(0),16)); // 디코딩후 복호화		
		$tmp=explode("|",$decrypt_data);
		
		if ($tmp[0] != $univcode  || $tmp[1] !=$sub_univcode ) {               
		    writeLog("[{$sLogFileId}] result=".json_encode($_GET, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), $sLogPath, $bLogable);
			writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		   js_alert_kakao_close('해당 값이 잘못 전달 되었습니다.');                      
        }

		$arr_data = [
			'univcode'			=> $tmp[0],
			'sub_univcode'		=> $tmp[1],
			'card_no'				=> $tmp[2],
			'card_type'			=> strtoupper($tmp[3]),
		];
		
		 // 알림톡 발송 함수 호출
    	require_once(APPPATH.'controllers/commonapi.php'); //include controller
        $api = new Commonapi();
        $info_tmp = $api->info_card($arr_data);		
        $info = (array)json_decode($info_tmp);
		
		if($info['value'] == 'FALSE'){
			$tcard_name = $arr_data['card_type'] == "G"?"상품권":"선불카드";
			writeLog("[{$sLogFileId}] result=".json_encode($_GET, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), $sLogPath, $bLogable);
			writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
			js_alert_kakao_close("사용할수 없는 {$tcard_name} 입니다.");              
		}

		
		if($arr_data['card_type'] == "G") { // 상품권일 경우
			$info_data = [
				'univcode'			=> $tmp[0],
				'sub_univcode'		=> $tmp[1],
				'card_no'				=> $info['card_no'],
				'card_name'			=> $info['card_name'],
				'card_type'			=> $arr_data['card_type'] ,
				'value'				=> $info['value'],
				'issue_amount'		=> $info['issue_amount'], // 발급금액
				'issue_date'			=> $info['issue_date'],
				'start_date'			=> $info['start_date'],
				'end_date'			=> $info['end_date'],
				'remark1'			=> $info['remark1'],
				'remark2'			=> $info['remark2'],
				'balance_amt'			=> $info['balance_amt'], // 잔액

			];
		}else{ // 선불카드의 경우
			$info_data = [
				'univcode'			=> $tmp[0],
				'sub_univcode'		=> $tmp[1],
				'card_no'				=> $info['card_no'],
				'card_name'			=> $info['card_name'],
				'card_type'			=> $arr_data['card_type'] ,
				'value'				=> $info['value'], // 사용유무				
				'issue_date'			=> $info['issue_date'],// 발급일자	 			
				'balance_amt'		=> $info['balance_amt'], // 잔액

			];
		}
		
		//print_r($info_data);
		if($arr_data['card_type'] == "G") { // 상품권일 경우
			$this -> load ->view('gift_card', $info_data);			
		}else{
			$this -> load ->view('prepaid_card', $info_data);			
		}
		
	}

	//알림톡 인증번호 전송
    public function alimtalk_auth_send() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/alimtalk_auth_send/' . date('Ymd') . '_alimtalk_auth_send.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/alimtalk_auth_send/' . date('Ymd') . '_alimtalk_auth_send.log';
        $bLogable    = true;

        $results = array(
            'code' => 'success',
            'msg' => '성공',
            'data' => null
        );

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
		
		 $alimitalk_arr = [
			 	"univcode"				=> $this->input->post('UnivCode', TRUE),
			 	"sub_univcode"		=> $this->input->post('sub_univcode', TRUE),
                "user_key"				=> $this->input->post('user_key', TRUE),
                "user_phone"			=> $this->input->post('user_phone', TRUE),
                "user_name"			=> $this->input->post('user_name', TRUE),
           ];

		$univcode =  $this->input->post('UnivCode', TRUE);

		if (!$univcode) {      
            $results = [
				'code' => 'fail',
				'msg' => 'univcode가 없습니다.',
				'data' => null
			];

            writeLog("[{$sLogFileId}] result=".json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), $sLogPath, $bLogable);
           
        }

		writeLog("[{$sLogFileId}] alimitalk_arr=" . json_encode($alimitalk_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

	
		//알림톡 전송
		$alimtalk_msg = $this->config->item('ALIMTALK_AUTH_MSG')[$univcode];//인증번호	
		
		//임의 난수 6자리
        $auth_no = get_random_string(6,'09');
		//공통
		$alimtalk_msg = str_replace('#{인증번호}', $auth_no, $alimtalk_msg);		

	
		$template_code = $this->config->item('ALIMTALK_AUTH_TEMPLATE_CODE')[$univcode];
		$template_name = $this->config->item('ALIMTALK_AUTH_TEMPLATE_NAME')[$univcode];
		
		$button= "";
		
		//알림톡 전송		
		$alimtal_arr = [				
				'alimtalk_id'              => $this->config->item('ALIMTALK_ID')[$univcode],
				'alimtalk_key'             => $this->config->item('ALIMTALK_KEY')[$univcode],
				'phone'					=> $alimitalk_arr['user_phone'],
				'custMsgSn'				=> $this->config->item('ALIMTALK_ID')[$univcode],				
				'msg'						=> $alimtalk_msg,				
				'sender_key'			=> $this->config->item('ALIMTALK_SENDER_KEY')[$univcode],
				'univcode'				=> $alimitalk_arr['univcode'],
				'sub_univcode'        => $alimitalk_arr['sub_univcode'],
				'template_code'		=> $template_code,
				'template_name'       => $template_name,
				'button'					=> $button
			];
		
		$sendAlimtalk = sendAlimtalk(
			$alimtal_arr
		);

		//print_r($sendAlimtalk);
		//exit;
		
		//알림톡 발송로그 INSERT 동작
		$resultParam = [			
			'sender_key'           => $this->config->item('ALIMTALK_SENDER_KEY')[$univcode],
			'phone'                 => $alimitalk_arr['user_phone'],
			'template_code'      => $template_code,
			'msg'                   => $alimtalk_msg,
			'univcode'			  => $alimitalk_arr['univcode'],
			'sub_univcode'       => $alimitalk_arr['sub_univcode'],
			'mtype'				  => 'auth',
			'auth_no'				=> $auth_no,
			'user_key'			  => $alimitalk_arr['user_key'],
			'result_status'        => $sendAlimtalk['data']['result_status'],
			'result_code'          => $sendAlimtalk['data']['result_code'],
			'result_alt_code'      => $sendAlimtalk['data']['result_alt_code'],	
			'altMsg'					=> $sendAlimtalk['data']['altMsg'],	
			'result'                  => $sendAlimtalk['data']['result'],
		];

		$data_list = $this->API->insertAlimtalkLog($resultParam);
		
		$results = [
			'code' => $sendAlimtalk['data']['result_status'],
			'msg' => $sendAlimtalk['data']['result_alt_code'],
			'data' => $sendAlimtalk['data']['altMsg'],
		];
		

		// Ends of Log Write
		writeLog("[{$sLogFileId}] result=" . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		return json_encode($results);
		
	} 


	//알림톡 번호체크
    public function alimtalk_auth_check() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/alimtalk_auth_check/' . date('Ymd') . '_alimtalk_auth_check.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/alimtalk_auth_check/' . date('Ymd') . '_alimtalk_auth_check.log';
        $bLogable    = true;

        $results = array(
            'code' => 'success',
            'msg' => '성공',
            'data' => null
        );

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
		
		 $alimitalk_arr = [
			 	"univcode"				=> $this->input->post('UnivCode', TRUE),
			 	"sub_univcode"		=> $this->input->post('sub_univcode', TRUE),
                "user_key"				=> $this->input->post('user_key', TRUE),
                "user_phone"			=> $this->input->post('user_phone', TRUE),
                "user_name"			=> $this->input->post('user_name', TRUE),
				"auth_no"				=> $this->input->post('auth_no', TRUE),
			 
           ];

		$univcode =  $this->input->post('UnivCode', TRUE);

		if (!$univcode) {      
            $results = [
				'code' => 'fail',
				'msg' => 'univcode가 없습니다.',
				'data' => null
			];

            writeLog("[{$sLogFileId}] result=".json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), $sLogPath, $bLogable);
           
        }

		writeLog("[{$sLogFileId}] alimitalk_arr=" . json_encode($alimitalk_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		

		$datas = array();
        $datas = $this->API->getAlimtalkLog($alimitalk_arr);

		
		//비교시
		if($alimitalk_arr['auth_no'] != $datas['auth_no']){
			$results = array(
				'code'    => 'fail',
				'msg'        => '알림톡 인증번호가 잘못되었습니다.',
				'data'       => null
			);

			writeLog("[{$sLogFileId}] alimitalk_arr=" . json_encode($alimitalk_arr,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
			writeLog("[{$sLogFileId}] result=" . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
			writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
			echo json_encode($results);
			exit;
		}



		// Ends of Log Write
		writeLog("[{$sLogFileId}] result=" . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		echo json_encode($results);		
	} 


}    