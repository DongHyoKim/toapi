<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Realtime_pos extends CT_Controller {

    private $json_array = array();

    public function __construct(){
		parent::__construct();
		$this->load->model('Pos_api_model','API');
    }	


    function encrypt($plaintext, $password) {
        $method = "AES-256-CBC";
        $key = hash('sha256', $password, true);
        $iv = openssl_random_pseudo_bytes(16);

        $ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $ciphertext, $key, true);

        return $iv . $hash . $ciphertext;
    }

    function decrypt($ivHashCiphertext, $password) {
        $method = "AES-256-CBC";
        $iv = substr($ivHashCiphertext, 0, 16);
        $hash = substr($ivHashCiphertext, 16, 32);
        $ciphertext = substr($ivHashCiphertext, 48);
        $key = hash('sha256', $password, true);
    
        if (hash_hmac('sha256', $ciphertext, $key, true) !== $hash) return null;
    
        return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
    }


    public function index(){    
        $tmp_keyword = $this->input->post('tmp_keyword',true);
        $password = "qpwoeirutya;sldkfjghz/x.c,vmbn10";
        //$encrypted = $this->encrypt($tmp_keyword, $password); // this yields a binary string

        echo  "\$tmp_keyword : " . $this->decrypt($tmp_keyword, $password);    
    }

    public function ticket_machine_mileage() 
    {	  
        
		$univcode = $this->input->post('univcode',true);
        $saletotal_member = $this->input->post('saletotal_member',true);   
        
        $tmp_keyword = $this->aesutil->AES_Decode($this->input->post('tmp_keyword',true));
        //print_r($tmp_keyword);

        if (!$univcode) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "대학코드가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }

        if (!$saletotal_member) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "조합원번호가 존재하지 않습니다.";
            echo json_encode($json_array);      
            exit;
        }
    
        $tmp_result = $this->API->getTotalMileage($saletotal_member);
        $json_array['status']       = 1;
        $json_array['totalmileage'] = $tmp_result['0']['MILEAGE'];
        echo json_encode($json_array);
        exit;    

	}

    public function ticket_machine_mileage_insert()
    {
        $logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/copartner/' . date('Ymd') . '_data.log',
            'bLogable'      => true,
        );
        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/copartner/' . date('Ymd') . '_data.log';
        $bLogable    = true;
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);    
    
        $univcode                     = $this->input->post('univcode',true);
        $saletotal_date               = $this->input->post('saletotal_date',true);  //--* 매출일자
        $saletotal_store              = $this->input->post('saletotal_store',true); //--* 매장코드
        $saletotal_posid              = $this->input->post('saletotal_posid',true); //--* 자판기코드
        $saletotal_billnumber         = $this->input->post('saletotal_billnumber',true); //--* 식권번호
        $saletype                     = $this->input->post('saletype',true);  //--  매출구분(매출1 / 반품-1)
        $cashcredit                   = $this->input->post('cashcredit',true);  //--  매출형태(현금1 / 신용카드2)
        $saletotal_cardvan            = $this->input->post('saletotal_cardvan',true); //--  신용카드밴사명(현금일 경우 space)
        $saletotal_joinno             = $this->input->post('saletotal_joinno',true); //--  밴사매입사코드(현금일 경우space)
        $pointtype                    = $this->input->post('pointtype',true); //--  포인트처리(적립001 / 사용002 / 미사용000)
        $saletotal_member             = $this->input->post('saletotal_member',true); //--  포인트처리 조합원번호(상지대7001 1700 1*** ***a:*는 연번/a는 체크sum, 미사용시 space)
        $saletotal_profit             = $this->input->post('saletotal_profit',true); //--  포인트사용시 사용포인트 금액, 미사용시 0.0
        $amount                       = $this->input->post('amount',true); //--  포인트사용시 사용포인트 금액, 미사용시 0.0
        $realdatetime                 = $this->input->post('realdatetime',true); //--  실판매시간

        $params = array(
            'univcode'             => $univcode,
            'saletotal_date'       => $saletotal_date,
            'saletotal_store'      => $saletotal_store,
            'saletotal_posid'      => $saletotal_posid,
            'saletotal_billnumber' => $saletotal_billnumber,
            'saletype'             => $saletype,
            'cashcredit'           => $cashcredit,
            'saletotal_cardvan'    => $saletotal_cardvan,
            'saletotal_joinno'     => $saletotal_joinno,
            'pointtype'            => $pointtype,
            'saletotal_member'     => $saletotal_member,
            'saletotal_profit'     => $saletotal_profit,
            'amount'               => $amount,
            'realdatetime'         => $realdatetime,
        );

        if (!$UnivCode) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "대학코드가 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$saletotal_date) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "매출일자가 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$saletotal_store) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "매장코드가 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$saletotal_posid) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "자판기 코드가 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$saletotal_billnumber) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "식권번호가 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
        
        if (!$saletype) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "매출구분이 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$cashcredit) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "매출형태가 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$realdatetime) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "실판매시간이 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
    
        if (!$amount) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "판매금액이 존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
        
        if ($cashcredit == "2" ){ // 카드일경우      
            if (!$saletotal_cardvan) {      
                $json_array['status'] = -1;    
                $json_array['message'] = "밴사명이 존재하지 않습니다.";
                writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
                echo json_encode($json_array);      
                exit;
            }
            if (!$saletotal_joinno) {      
                $json_array['status'] = -1;    
                $json_array['message'] = "밴매입사코드가 존재하지 않습니다.";
                writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
                echo json_encode($json_array);      
                exit;
            }        
        }
    
        if (!$pointtype) {      
            $json_array['status'] = -1;    
            $json_array['message'] = "포인트처리 구분자가  존재하지 않습니다.";
            writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
            echo json_encode($json_array);      
            exit;
        }
    
        if ($pointtype != "000" ){ // 적립금 미사용이 아닌경우 	     
            if (!$saletotal_profit) {      
                $json_array['status'] = -1;    
                $json_array['message'] = "사용포인트 금액이  존재하지 않습니다.";
                writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
                echo json_encode($json_array);      
                exit;
            }
            if (!$amount) {      
                $json_array['status'] = -1;    
                $json_array['message'] = "사용 금액이  존재하지 않습니다.";
                writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
                echo json_encode($json_array);      
                exit;
            }        
        } else {
            $saletotal_member = " " ; // 미사용시는 공백 
            $saletotal_profit = 0.0 ; // 미사용시는 0.0 
        }
        
        $tmp_result = $this->API->insertSaleTotalMileage($UnivCode, $saletotal_date, $saletotal_store, $saletotal_posid, $saletotal_billnumber, $saletype,  $cashcredit, $saletotal_cardvan, $saletotal_joinno, $pointtype, $saletotal_member, $saletotal_profit, $amount, $realdatetime);
        
        if($tmp_result){
            $json_array['status']  = 1;
            $json_array['message'] = "DB에 저장 완료";
            writeLog("[{$sLogFileId}] params=".json_encode(implode('|', $params))., $sLogPath, $bLogable);
        } else {
            $json_array['status']  = -1; 
            $json_array['message'] = "DB에러가 발생하였습니다.";
            
        }
        writeLog("[{$sLogFileId}] status=".json_encode($json_array['status'])." message=".json_encode($json_array['message']), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);    
        echo json_encode($json_array);      
        exit;    
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */