<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Hand_terminal extends CT_Controller {

    private $json_array = array();

	public function __construct(){
		parent::__construct();
		$this->load->model('Hand_terminal_model','API');
		//print_r($this);
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
        header("Content-type:text/html;charset=utf-8");
    }

    public function index(){
    }

    public function user_check(){

    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/handterminal/' . date('Ymd') . '_usercheck.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/handterminal/' . date('Ymd') . '_usercheck.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        
        $db_name       = "U_BASIC";              // DB명

        $UNIVCODE   = $this->input->post('UNIVCODE',true);     // 경북대학코드 00109
		$CAMPUSCODE = $this->input->post('CAMPUSCODE',true);   // 대구캠퍼스 001 / 상주캠퍼스 002
        $USERID     = $this->input->post('USERID',true);       // 매장코드 대구서점 5295 / 상주서점 1194
		$PASS       = $this->input->post('PASS',true);         // 패스워드 대구서점 5295 / 상주서점 1194

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        if(empty($UNIVCODE)){
			$message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "대학코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($CAMPUSCODE)){
			$message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "캠퍼스코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($USERID)){
			$message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "사용자코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($PASS)){
			$message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "비밀번호가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}

        $params = [
			'UNIVCODE'   => $UNIVCODE,
			'CAMPUSCODE' => $CAMPUSCODE,
			'dbname'     => $db_name,
			'USERID'     => $USERID,
			'PASS'       => $PASS,
		];
		
		writeLog("[{$sLogFileId}] params=".json_encode($params,JSON_UNESCAPED_UNICODE));

		$chk_results = $this->API->user_check($params);
		//print_r($chk_results);
		//echo("chk_results=".$chk_results['0']);
        writeLog("[{$sLogFileId}] chk_results=".json_encode($chk_results,JSON_UNESCAPED_UNICODE));

		if($chk_results == 0){
			$message['rCode'] = "1000";
            $message['error']['errorCode'] = "1000";
            $message['error']['errorMessage'] = "비밀번호가 적합하지 않습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
	}

    public function user_check2(){

    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/handterminal/' . date('Ymd') . '_usercheck2.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/handterminal/' . date('Ymd') . '_usercheck2.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        
        $db_name       = "U_BASIC";              // DB명

        $UNIVCODE   = $this->input->post('UNIVCODE',true);     // 경북대학코드 00109
        $USERID     = $this->input->post('USERID',true);       // 매장코드 대구서점 5295 / 상주서점 1194
		$PASS       = $this->input->post('PASS',true);         // 패스워드 대구서점 5295 / 상주서점 1194

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        if(empty($UNIVCODE)){
			$message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "대학코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($USERID)){
			$message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "사용자코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($PASS)){
			$message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "비밀번호가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}

        // STEP1 ID/PW 확인
        $params = [
			'UNIVCODE'   => $UNIVCODE,
			'dbname'     => $db_name,
			'USERID'     => $USERID,
			'PASS'       => $PASS,
		];
		
		writeLog("[{$sLogFileId}] params=".json_encode($params,JSON_UNESCAPED_UNICODE));

		$chk_results = $this->API->user_check_idpw($params);
		//print_r($chk_results);
		//echo("chk_results=".$chk_results['0']);
        writeLog("[{$sLogFileId}] 1st_results=".json_encode($chk_results,JSON_UNESCAPED_UNICODE));

		if($chk_results == 0){
			$message['rCode'] = "1000";
            $message['error']['errorCode'] = "1000";
            $message['error']['errorMessage'] = "비밀번호가 적합하지 않습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}elseif($chk_results == 1){
		    // STEP2 캠퍼스코드,사용자명_유저명,매장코드(SUBUNIVCODE,USERNAME,STORECODE) 가져오기
			$results_arr = $this->API->get_store_info($params);

			if(!empty($results_arr['SUBUNIVCODE'])){
		    // STEP3 캠퍼스코드로 STORENAME 가져오기->한번에 가져오는 걸로 수정
				//$params['SUBUNIVCODE'] = $results_arr['SUBUNIVCODE'];
                //$params['STORECODE']   = $results_arr['STORECODE'];
				//$results_storename = $this->API->get_store_name($params);
                //$results_arr['STORENAME'] = $results_storename['DEPTNAME'];

                writeLog("[{$sLogFileId}] results_arr=".json_encode($results_arr,JSON_UNESCAPED_UNICODE));

    			$message['SUBUNIVCODE'] = $results_arr['SUBUNIVCODE'];
	    		$message['USERNAME']    = $results_arr['USERNAME'];
	            $message['STORECODE']   = $results_arr['STORECODE'];
                $message['STORENAME']   = $results_arr['STORENAME'];
				if(strstr($results_arr['BOOKTYPE'], 'BOOKPOS') || strstr($results_arr['BOOKTYPE'], 'U_BOOK')){
					$message['BOOKTYPE'] = "B";
				} else {
                    $message['BOOKTYPE'] = "S";
				}
			} else {
                $message['rCode'] = "2000";
                $message['error']['errorCode'] = "2000";
                $message['error']['errorMessage'] = "캠퍼스코드를 확인할수 없습니다. 입력한 ID,PW를 확인후 다시 시도해주세요!";
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
			}
        }
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
	}

    public function insert_silsa(){

    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/handterminal/' . date('Ymd') . '_insertsilsa.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/handterminal/' . date('Ymd') . '_insertsilsa.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );

        $db_name     = "U_BOOK";              // DB명

        $UNIVCODE    = $this->input->post('UNIVCODE',true);     // 경북대학코드 00109
		$SUBUNIVCODE = $this->input->post('SUBUNIVCODE',true);  // 대구캠퍼스 001 / 상주캠퍼스 002
        $STORECODE   = $this->input->post('STORECODE',true);    // 대구서점 6001000 / 상주서점 6101000
		$SILSADATE   = $this->input->post('SILSADATE',true);    // 실사재고일자 char(8)
		$BOOKCODE    = $this->input->post('BOOKCODE',true);     // 도서코드 char(13)
		$SILSAQTY    = $this->input->post('SILSAQTY',true);     // 실사재고수량 float
		$SILSATYPE   = $this->input->post('SILSATYPE',true);    // 실사재고TYPE char(1) default '1'
		$HANDID      = $this->input->post('HANDID',true);       // 핸드터미널DB ID

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        if(empty($UNIVCODE)){
			$message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "대학코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($SUBUNIVCODE)){
			$message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "캠퍼스코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($STORECODE)){
			$message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "매장코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($SILSADATE)){
			$message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "실사재고일자가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($BOOKCODE)){
			$message['rCode'] = "0005";
            $message['error']['errorCode'] = "0005";
            $message['error']['errorMessage'] = "도서코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($SILSAQTY)){
			$message['rCode'] = "0006";
            $message['error']['errorCode'] = "0006";
            $message['error']['errorMessage'] = "실사재고 수량이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($SILSATYPE)){
			$message['rCode'] = "0007";
            $message['error']['errorCode'] = "0007";
            $message['error']['errorMessage'] = "실사재고 구분이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($HANDID)){
			$message['rCode'] = "0008";
            $message['error']['errorCode'] = "0008";
            $message['error']['errorMessage'] = "ID가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
	    if(strlen($BOOKCODE) > 13){  // BOOKCODE 13자리 이상이면
		    $message['rCode'] = "0009";
            $message['error']['errorCode'] = "0009";
            $message['error']['errorMessage'] = "도서코드가 13자리 이상입니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }


        $params = [
			'dbname'      => $db_name,
			'UNIVCODE'    => $UNIVCODE,
			'SUBUNIVCODE' => $SUBUNIVCODE,
			'STORECODE'   => $STORECODE,
			'SILSADATE'   => $SILSADATE,
			'BOOKCODE'    => $BOOKCODE,
			'SILSAQTY'    => $SILSAQTY,
			'SILSATYPE'   => $SILSATYPE,
			'INSERTDATE'  => date("YmdHis",time()),
			'HANDID'      => $HANDID,
		];

		writeLog("[{$sLogFileId}] params=".json_encode($params,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

		$insert_results = $this->API->insert_silsa($params);
		//echo("insert_results=".$insert_results);

		writeLog("[{$sLogFileId}] insert_results=".json_encode($insert_results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

		if($insert_results == 0){
			$message['rCode'] = "1000";
            $message['error']['errorCode'] = "1000";
            $message['error']['errorMessage'] = "실사재고내역이 저장되지 않았습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		} else {
			$message['handid'] = $HANDID;
		}
		writeLog("[{$sLogFileId}] message=".json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
	}

    public function insert_silsa_multi(){

    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/handterminal/' . date('Ymd') . '_insert_silsa_multi.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/handterminal/' . date('Ymd') . '_insert_silsa_multi.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );

        $db_name     = "U_BOOK";              // DB명

        $UNIVCODE    = $this->input->post('UNIVCODE',true);     // 경북대학코드 00109
		$SUBUNIVCODE = $this->input->post('SUBUNIVCODE',true);  // 대구캠퍼스 001 / 상주캠퍼스 002
        $STORECODE   = $this->input->post('STORECODE',true);    // 대구서점 6001000 / 상주서점 6101000
		$SILSADATE   = $this->input->post('SILSADATE',true);    // 실사재고일자 char(8)
		$SILSATYPE   = $this->input->post('SILSATYPE',true);    // 실사재고TYPE char(1) default '1'
		$STOCK_TMP   = $this->input->post('STOCK',true);        // 재고조사배열(100개기준) 도서코드,재고수량,핸드ID|도서코드,재고수량,핸드ID ...

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
		writeLog("[{$sLogFileId}] STOCK_TMP=".json_encode($STOCK_TMP,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

        if(empty($UNIVCODE)){
			$message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "대학코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($SUBUNIVCODE)){
			$message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "캠퍼스코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($STORECODE)){
			$message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "매장코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($SILSADATE)){
			$message['rCode'] = "0004";
            $message['error']['errorCode'] = "0004";
            $message['error']['errorMessage'] = "실사재고일자가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		if(empty($SILSATYPE)){
			$message['rCode'] = "0007";
            $message['error']['errorCode'] = "0007";
            $message['error']['errorMessage'] = "실사재고 구분이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		
		$STOCK_ROW   = [];
        $STOCK_ROW   = explode("|",$STOCK_TMP);

		$STOCK_ARRAY = [];
		foreach($STOCK_ROW as $key => $value){
            
			$bookstock = explode(",",$value); // 0:BOOKCODE , 1:SILSAQTY, 2:HANDID

		    if(empty($bookstock['0'])){
			    $message['rCode'] = "0005";
                $message['error']['errorCode'] = "0005";
                $message['error']['errorMessage'] = $key."번 도서코드가 없습니다!";
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
		    }
    		if(empty($bookstock['1'])){
	    		$message['rCode'] = "0006";
                $message['error']['errorCode'] = "0006";
                $message['error']['errorMessage'] = $key."번 실사재고 수량이 없습니다!";
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
		    }
		    if(empty($bookstock['2'])){
			    $message['rCode'] = "0008";
                $message['error']['errorCode'] = "0008";
                $message['error']['errorMessage'] = $key."번 ID가 없습니다!";
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
		    }
		    if(strlen($bookstock['0']) > 13){  // BOOKCODE 13자리 이상이면
			    $message['rCode'] = "0009";
                $message['error']['errorCode'] = "0009";
                $message['error']['errorMessage'] = $key."번 도서코드가 13자리 이상입니다!";
                writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
                echo json_encode($message);
                exit;
		    }
			
			$STOCK_ARRAY[$key]['BOOKCODE'] = $bookstock['0'];
			$STOCK_ARRAY[$key]['SILSAQTY'] = $bookstock['1'];
			$STOCK_ARRAY[$key]['HANDID']   = $bookstock['2'];
		}
        //print_r($STOCK_ARRAY);
		//exit;

        $params = [
			'dbname'      => $db_name,
			'UNIVCODE'    => $UNIVCODE,
			'SUBUNIVCODE' => $SUBUNIVCODE,
			'STORECODE'   => $STORECODE,
			'SILSADATE'   => $SILSADATE,
			'SILSATYPE'   => $SILSATYPE,
		    'STOCK'       => $STOCK_ARRAY,
			'INSERTDATE'  => date("YmdHis",time()),
		];

		writeLog("[{$sLogFileId}] params=".json_encode($params,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

		$insert_results = $this->API->insert_silsa_multi($params);
		//echo("insert_results=".$insert_results);

		writeLog("[{$sLogFileId}] insert_results=".json_encode($insert_results,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);

		if($insert_results == 0){
			$message['rCode'] = "1000";
            $message['error']['errorCode'] = "1000";
            $message['error']['errorMessage'] = "실사재고내역이 저장되지 않았습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		} elseif($insert_results == -1){
			$message['rCode'] = "2000";
            $message['error']['errorCode'] = "2000";
            $message['error']['errorMessage'] = "저장하는중 에러로 트랜잭션이 롤백되었습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		} else {
			// 수정해야함.
			$message['handid'] = array_column($STOCK_ARRAY,'HANDID');
		}
		writeLog("[{$sLogFileId}] message=".json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		
		echo json_encode($message,JSON_UNESCAPED_UNICODE);
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