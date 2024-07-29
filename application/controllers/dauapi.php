<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dauapi extends CT_Controller {

    //private $json_array = array();

    public function __construct(){
		parent::__construct();
        $this->load->library('form_validation');;
		$this->load->helper('common_helper');
		$this->load->model('daumodel','DAU');
    }	
    
    public function index(){
        echo("Now init!!");
    }

    //주문정보 receive api
    public function receive()
    {
		//error_reporting(E_ALL&~E_WARNING);

    	$logs = [
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/dauapi/' . date('Ymd') . '_receive.log',
            'bLogable'      => true
        ];

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/dauapi/' . date('Ymd') . '_receive.log';
        $bLogable    = true;

        $results = [
            'success'    => RES_CODE_SUCCESS,
            'msg'        => '',
        ];

        try
        {
            $this->form_validation->set_rules('univcode', '', 'trim|required|xss_clean');
            $this->form_validation->set_rules('receive_date', '', 'trim|required|xss_clean');
            $this->form_validation->set_rules('receive_store', '', 'trim|required|xss_clean');
            
            writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
            //$univcode = $_POST['UnivCode'];
            $univcode      = $this->input->post('univcode', TRUE);
            $receive_date  = $this->input->post('receive_date', TRUE);
            $receive_store = $this->input->post('receive_store', TRUE);

			$post_arr = [
                'univcode'      => $univcode,
				'receive_date'  => $receive_date,
				'receive_store' => $receive_store,
			];

            // Post Data 검증
            if(empty($univcode)){
                $results['success'] = ERR_CODE_POST;
                $results['msg']     = "(".$univcode.")가 POST로 전달되지 않았습니다!";
                throw new Exception("(".$univcode.")가 POST로 전달되지 않았습니다!");
            }
			if(empty($receive_date)){
                $results['success'] = ERR_CODE_POST;
                $results['msg']     = "receive_date가 POST전달되지 않았습니다!";
                throw new Exception("receive_date가 POST전달되지 않았습니다!");
            }
			if(empty($receive_store)){
                $results['success'] = ERR_CODE_POST;
                $results['msg']     = "receive_store가 POST전달되지 않았습니다!";
                throw new Exception("receive_store가 POST전달되지 않았습니다!");
            }
			writeLog("[{$sLogFileId}] post_arr= " .json_encode($post_arr,JSON_UNESCAPED_UNICODE), $sLogPath);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => REAL_URL.$receive_date,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => ['Authorization: Basic '.REAL_AUTH],
            ]);
            $receive_json = curl_exec($curl);
            curl_close($curl);
            //echo $receive_json;
            //exit;
        
		    // json data Receive
			$receive_arr  = [];
            $receive_temp = json_decode($receive_json, TRUE);
            //echo $receive_temp;
            //exit;
			
            $receive_arr  = json_decode($receive_temp, TRUE);
            //$receive_arr = stripslashes($receive_arr);
            //print_r($receive_arr);
            //exit;

            // json Data 검증
            //echo("isValid:".$receivearr['isValid']);
            //exit;
            if($receive_arr['isValid'] != TRUE || !empty($receive_arr['errorMsg'])){
                $results['success'] = ERR_CODE_JSON;
                $results['msg']     = "(".$receive_arr['errorMsg'].") 수신 JSON 오류!";
                throw new Exception($receive_arr['errorMsg']." 수신 JSON 오류!");
            }
        
            // Config정보 가져오기
			
            // 각 배열의 정의와 선언
            $receipts = [];         // 1단계기본배열:복수개
		    $receipts = $receive_arr['receipts'];
            //print_r($receipts);
            //exit;

			// 학교 매장코드 가져오기
			$code_type            = '01';       // 코드구분:학교매장코드
			//$receive_storecode    = $receipts['0']['storeCode'];
			//$receive_storecode    = $receipts['0']['storeCode'];
			$univstore_code       = $this->DAU->getStorecode($code_type,$receive_store);
            //print_r($univstore_code);
			//exit;
			//$univcode             = substr($univstore_code,0,5);
			$conversion_storecode = substr($univstore_code['univstore_code'],-7,7);
			$processdate          = date("YmdHis",time());

			$paymenttype_arr   = [];
			$saletype_arr      = [];
			$discounttype_arr  = [];
			$cardapproval_arr  = [];
			$cardpurchaser_arr = [];
			$standard_codes    = $this->DAU->getStandardcode();  // 표준 변환코드 가져오기
			foreach($standard_codes as $key => $value){
				if($value['CODETYPE'] == '11'){          // 결제구분(현금/페이코/신용카드/알리페이/)
                    $paymenttype_arr[$value['OFFERCODE']] = $value['CWAYCODE'].'|'.$value['CODENAME'];
				} else if($value['CODETYPE'] == '12') {  // 판매구분(정상/반품)
                    $saletype_arr[$value['OFFERCODE']] = $value['CWAYCODE'];
				} else if($value['CODETYPE'] == '13') {  // 할인구분(금액할인)
					$discounttype_arr[$value['OFFERCODE']] = $value['CWAYCODE'];
				} else if($value['CODETYPE'] == '21') {  // 카드등록구분(임의/POS/CAT/VCAT/)
					$cardapproval_arr[$value['OFFERCODE']] = $value['CWAYCODE'];
				} else if($value['CODETYPE'] == '22') {  // 매입사코드
					$cardpurchaser_arr[$value['OFFERCODE']] = $value['CWAYCODE'];
				}
			}

			$baseParams = [];
			$logParams  = [];
            foreach($receipts as $basekey => $basevalue){
                $baseParams = [
					'UNIVCODE'              => $univcode,
					'SUBUNIVCODE'           => CAMPUS,
					'SALEDATE'              => $basevalue['saleDate'],
					'STORECODE'             => $conversion_storecode,
					'POSID'                 => substr($basevalue['posCode'],-2,2),
					'BILLNUMBER'            => $basevalue['billNo'],
					'SALETYPE'              => $saletype_arr[$basevalue['saleType']], // 코드변환
					'CREATEDATE'            => $processdate,
					'TOORDER_PLATFORMID'    => OFFER,
					'TOORDER_PLATFORMTYPE'  => PLATFORMTYPE,
					'TOORDER_ITEMCOUNT'     => 0,
					'TOORDER_AMOUNT'        => $basevalue['saleAmount'],
					'TOORDER_TOTALAMOUNT'   => $basevalue['totalAmount'],
					'TOORDER_DCAMOUNT'      => $basevalue['dcAmount'],
					'TOORDER_INAMOUNT'      => $basevalue['totalAmount'],
					'TOORDER_CHANGEMONEY'   => 0,
					'TOORDER_DFREEAMOUNT'   => $basevalue['saleTaxfreeAmount'],
					'TOORDER_TAXAMOUNT'     => $basevalue['saleTaxAmount'],
					'TOORDER_SUPPLYAMOUNT'  => $basevalue['supplyAmount'],
					'TOORDER_VATAMOUNT'     => $basevalue['taxAmount'],
					'TOORDER_ORGSALEDATE'   => $basevalue['orgSaleDate'],
					'TOORDER_ORGPOSID'      => substr($basevalue['orgPosCode'],-2,2),
					'TOORDER_ORGBILLNUMBER' => $basevalue['orgBillNo'],
					'INSERTDATE'            => $processdate,
					'INSERTID'              => INSERTID,
				];
				$logParams = [
					'UNIVCODE'            => $univcode,
					'SUBUNIVCODE'         => CAMPUS,
					'SALEDATE'            => $basevalue['saleDate'],
					'STORECODE'           => $conversion_storecode,
					'POSID'               => substr($basevalue['posCode'],-2,2),
					'BILLNUMBER'          => $basevalue['billNo'],
                    'JSONLOG'             => json_encode($basevalue,TRUE),
					'INSERTDATE'          => $processdate,
					'INSERTID'            => INSERTID,
				];

                $receiptProduct  = $basevalue['receiptProduct'];
				$baseParams['TOORDER_ITEMCOUNT'] = count($receiptProduct);

                $receiptDiscount = $basevalue['receiptDiscount'];
				//print_r($receiptDiscount);
				//exit;
                $receiptPayment  = $basevalue['receiptPayment'];
                $receiptCard     = $basevalue['receiptCard'];
                $receiptCash     = $basevalue['receiptCash'];

				writeLog("[{$sLogFileId}] baseParams= " .json_encode($baseParams,JSON_UNESCAPED_UNICODE), $sLogPath);

				$productParams  = [];
				foreach($receiptProduct as $productkey => $productvalue){
					$productParams[] = [
						'UNIVCODE'             => $univcode,
						'SUBUNIVCODE'          => CAMPUS,
						'SALEDATE'             => $basevalue['saleDate'],
						'STORECODE'            => $conversion_storecode,
					    'POSID'                => substr($basevalue['posCode'],-2,2),
					    'BILLNUMBER'           => $basevalue['billNo'],
						'PRODUCT_SEQ'          => $productvalue['seq'],
					    'SALETYPE'             => $saletype_arr[$basevalue['saleType']], // 코드변환
						'CREATEDATE'           => $processdate,
						'PRODUCT_ITEMCODE'     => $productvalue['productCode'],
						'PRODUCT_ITEMNAME'     => (mb_strlen($productvalue['productName'],"UTF-8")<=15)?$productvalue['productName']:mb_substr($productvalue['productName'], 0, 15, "UTF-8"),
						'PRODUCT_COST'         => $productvalue['productPrice'],
						'PRODUCT_PRICE'        => $productvalue['productPrice'],
						'PRODUCT_QTY'          => $productvalue['saleQty'],
						'PRODUCT_AMOUNT'       => $productvalue['saleAmount'],
						'PRODUCT_SALEAMOUNT'   => $productvalue['totalAmount'],
						//'PRODUCT_DCTYPE'       => (!empty($discounttype_arr[$receiptDiscount[$productkey]['dcType']]))?$discounttype_arr[$receiptDiscount[$productkey]['dcType']]:'',  // 코드변환
						'PRODUCT_DCTYPE'       => (!empty($receiptDiscount[$productkey]['dcType']))?$receiptDiscount[$productkey]['dcType']:'',  // 코드변환
						'PRODUCT_DCAMOUNT'     => $productvalue['dcAmount'],
						'PRODUCT_TAXTYPE'      => ($productvalue['taxAmount'] == 0)?'F':'T',
						'PRODUCT_SUPPLYAMOUNT' => $productvalue['supplyAmount'],
						'PRODUCT_VATAMOUNT'    => $productvalue['taxAmount'],
						'INSERTDATE'           => $processdate,
					    'INSERTID'             => INSERTID,
					];
				}
				writeLog("[{$sLogFileId}] productParams= " .json_encode($productParams,JSON_UNESCAPED_UNICODE), $sLogPath);

				$paymentParams  = [];
				foreach($receiptPayment as $paymentkey => $paymentvalue){
					$payment_type = explode("|",$paymenttype_arr[$paymentvalue['payCode']]);
					$paymentParams[] = [
						'UNIVCODE'             => $univcode,
						'SUBUNIVCODE'          => CAMPUS,
						'SALEDATE'             => $basevalue['saleDate'],
						'STORECODE'            => $conversion_storecode,
					    'POSID'                => substr($basevalue['posCode'],-2,2),
					    'BILLNUMBER'           => $basevalue['billNo'],
						'PAYMENT_SEQ'          => $paymentvalue['seq'],
					    'SALETYPE'             => $saletype_arr[$basevalue['saleType']], // 코드변환
						'CREATEDATE'           => $processdate,
						'PAYMENT_METHODCODE'   => !(empty($payment_type['0']))?$payment_type['0']:' ', // 코드변환explode
						'PAYMENT_METHODNAME'   => !(empty($payment_type['1']))?$payment_type['1']:' ', // 코드변환explode
						'PAYMENT_AMOUNT'       => $paymentvalue['payAmount'],
						'PAYMENT_INAMOUNT'     => $paymentvalue['payAmount'],
						'PAYMENT_CHANGEMONEY'  => 0,
						'INSERTDATE'           => $processdate,
					    'INSERTID'             => INSERTID,
					];
				}
				writeLog("[{$sLogFileId}] paymentParams= " .json_encode($paymentParams,JSON_UNESCAPED_UNICODE), $sLogPath);

				$cardParams  = [];
				foreach($receiptCard as $cardkey => $cardvalue){
					$cardParams[] = [
						'UNIVCODE'             => $univcode,
						'SUBUNIVCODE'          => CAMPUS,
						'SALEDATE'             => $basevalue['saleDate'],
						'STORECODE'            => $conversion_storecode,
						'POSID'                => substr($basevalue['posCode'],-2,2),
						'BILLNUMBER'           => $basevalue['billNo'],
						'CARD_SEQ'             => $cardvalue['seq'],
						'SALETYPE'             => $saletype_arr[$basevalue['saleType']], // 코드변환
						'CREATEDATE'           => $processdate,
						'CARD_PAYNAME'         => $paymentParams[$cardkey]['PAYMENT_METHODCODE'],  // 하단재처리 : 결제구분 코드
						'CARD_VANNAME'         => OFFER,                      // VAN사명
						'CARD_TID'             => $cardvalue['tid'],          // TID
						'CARD_APPTYPE'         => $cardapproval_arr[$cardvalue['appType']], // 범례 N:임의,P:POS,C:CAT/VCAT
						'CARD_CARDNO'          => $cardvalue['cardNo'],       // 카드번호
						'CARD_APPROVALNO'      => $cardvalue['appNo'],        // 승인번호
						'CARD_AMOUNT'          => $cardvalue['cardAmount'],   // 하단재처리: 결제금액
						'CARD_CARDAMOUNT'      => $cardvalue['cardAmount'],   // 하단재처리: 카드(페이)금액
						'CARD_POINTAMOUNT'     => FLOATZERO,                  // 하단재처리: 포인트금액
						'CARD_COUPONAMOUNT'    => FLOATZERO,                  // 하단재처리: 상품권선불카드금액
						'CARD_INSTALLMENT'     => installmentConvert($cardvalue['installment']), // 할부개월수(문자열)
						'CARD_ISSUERCODE'      => $cardvalue['issuerCode'],   // 발급사코드
						'CARD_ISSUERNAME'      => $cardvalue['issuerName'],   // 발급사명
						'CARD_ACQUIRERCODE'    => (!empty($cardpurchaser_arr[$cardvalue['acquirerCode']]))?$cardpurchaser_arr[$cardvalue['acquirerCode']]:' ',  // 매입사코드(씨웨이코드 변환)
						'CARD_ACQUIRERNAME'    => $cardvalue['acquirerName'], // 매입사명
						'CARD_TRADEDATE'       => date("YmdHis",strtotime($cardvalue['trDateTime'])),  // 거래일시
						'CARD_APPROVALDATE'    => $cardvalue['appDate'],      // 승인일시
						'CARD_ORGAPPDATE'      => $cardvalue['orgAppDate'],   // 반품시 원거래 판매일
						'CARD_ORGAPPNO'        => $cardvalue['orgAppNo'],     // 반품시 원거래 승인번호
						'INSERTDATE'           => $processdate,
						'INSERTID'             => INSERTID,
					];
					
					$var_arr = [
						'payment_seq'   => $cardkey,
                        'payment_type'  => $paymentParams[$cardkey]['PAYMENT_METHODCODE'],
						'payment_name'  => $paymentParams[$cardkey]['PAYMENT_METHODNAME'],
						'issuer_code'   => $cardvalue['issuerCode'],
						'issuer_name'   => $cardvalue['issuerName'],
						'acquirerCode'  => $cardvalue['acquirerCode'],
						'acquirer_name' => $cardvalue['acquirerName'],
						'card_amount'   => $cardvalue['cardAmount'],
					];
					writeLog("[{$sLogFileId}] cardPaynameSelect_arr= " .json_encode($var_arr,JSON_UNESCAPED_UNICODE), $sLogPath);
					$payName = cardPaynameSelect($var_arr);
					writeLog("[{$sLogFileId}] cardPaynameSelect_res= " .json_encode($payName,JSON_UNESCAPED_UNICODE), $sLogPath);
					if($payName['rename_flag'] == 'Y'){
						$paymentParams[$cardkey]['PAYMENT_METHODCODE'] = $payName['PAYMENT_METHODCODE'];
						$paymentParams[$cardkey]['PAYMENT_METHODNAME'] = $payName['PAYMENT_METHODNAME'];
						$cardParams[$cardkey]['CARD_PAYNAME']                    = $payName['CARD_PAYNAME'];
						$cardParams[$cardkey]['CARD_AMOUNT']                     = $payName['CARD_AMOUNT'];
						$cardParams[$cardkey]['CARD_CARDAMOUNT']                 = $payName['CARD_CARDAMOUNT'];
						$cardParams[$cardkey]['CARD_POINTAMOUNT']                = $payName['CARD_POINTAMOUNT'];
						$cardParams[$cardkey]['CARD_COUPONAMOUNT']               = $payName['CARD_COUPONAMOUNT'];
					}
				}
				writeLog("[{$sLogFileId}] cardParams= " .json_encode($cardParams,JSON_UNESCAPED_UNICODE), $sLogPath);

				$cashParams  = [];
				foreach($receiptCash as $cashkey => $cashvalue){
					//$payment_type = explode("|",$paymenttype_arr[$paymentvalue['payCode']]);
					$cashParams[] = [
						'UNIVCODE'             => $univcode,
						'SUBUNIVCODE'          => CAMPUS,
						'SALEDATE'             => $basevalue['saleDate'],
						'STORECODE'            => $conversion_storecode,
						'POSID'                => substr($basevalue['posCode'],-2,2),
						'BILLNUMBER'           => $basevalue['billNo'],
						'CASH_SEQ'             => $cashvalue['seq'],
						'SALETYPE'             => $saletype_arr[$basevalue['saleType']], // 코드변환
						'CREATEDATE'           => $processdate,
						'CASH_VANNAME'         => OFFER,                     // VAN사명
						'CASH_TYPE'            => $cashvalue['type'],        // 현금영수증유형(1:소득공제,2:지출증빙)
						'CASH_TID'             => $cashvalue['tid'],         // 단말기번호
						'CASH_AMOUNT'          => $cashvalue['cashAmount'],  // 결제금액
						'CASH_CARDNO'          => $cashvalue['cardNo'],      // 카드번호
						'CASH_APPTYPE'         => $cardapproval_arr[$cashvalue['appType']], // 범례 N:임의,P:POS,C:CAT/VCAT
						'CASH_APPROVALNO'      => $cashvalue['appNo'],       // 승인번호
						'CASH_TRADEDATE'       => date("YmdHis",strtotime($cardvalue['trDateTime'])),  // 거래일시
						'CASH_APPROVALDATE'    => $cardvalue['appDate'],     // 승인일시
						'CASH_ORGAPPDATE'      => $cardvalue['orgAppDate'],  // 반품시 원거래 판매일
						'CASH_ORGAPPNO'        => $cardvalue['orgAppNo'],    // 반품시 원거래 승인번호
						'INSERTDATE'           => $processdate,
						'INSERTID'             => INSERTID,
					];
				}
				writeLog("[{$sLogFileId}] cashParams= " .json_encode($cashParams,JSON_UNESCAPED_UNICODE), $sLogPath);
				writeLog("[{$sLogFileId}] logParams= " .json_encode($logParams,JSON_UNESCAPED_UNICODE), $sLogPath);

                $insert_results = $this->DAU->save2Order($baseParams,$productParams,$paymentParams,$cardParams,$cashParams,$logParams);
				if($insert_results != "0000"){
					writeLog("[{$sLogFileId}] error bill= " .json_encode($baseParams['BILLNUMBER'],JSON_UNESCAPED_UNICODE), $sLogPath);
				} else {
					writeLog("[{$sLogFileId}] ok bill= " .json_encode($baseParams['BILLNUMBER'],JSON_UNESCAPED_UNICODE), $sLogPath);
				}
            }

		} 
		catch(File_exception $e) 
		{
			$results['success'] = $e->error;
			$results['msg']     = $e->message;
		}
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		return $results;
	}

    //crontab 자동화
    public function cron_receive() {

		//error_reporting(E_ALL&~E_WARNING);

    	$logs = [
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/dauapi/' . date('Ymd') . '_cron_receive.log',
            'bLogable'      => true
        ];

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/dauapi/' . date('Ymd') . '_cron_receive.log';
        $bLogable    = true;

        $results = [
            'success'    => RES_CODE_SUCCESS,
            'msg'        => '',
        ];
		writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        try
        {
			// $receiveHeader['univcode']
            $receiveHeader = apache_request_headers();
			$univcode  = $receiveHeader['univcode'];
			
			$yesterday = date('Ymd', $_SERVER['REQUEST_TIME']-86400);
			//$yesterday = '20240401';

			$params = [
				'UNIVCODE'    => $univcode,    // 이대
				'SUBUNIVCODE' => '001',      // 서울캠퍼스
				'SALEDATE'    => $yesterday,
				'STORECODE'   => '3000200'   // 김옥길기념관카페
			];
			
			writeLog("[{$sLogFileId}] delete_params=" . json_encode($params,JSON_UNESCAPED_UNICODE), $sLogPath);
			$delete_result = $this->DAU->delete2Order($params);
			//echo("delete_result: ".$delete_result);

			if($delete_result == "0000"){
				$custom_arr = [
					'00113' => '107580001',
				];
				writeLog("[{$sLogFileId}] custom_arr=" . json_encode($custom_arr,JSON_UNESCAPED_UNICODE), $sLogPath);
				
				$curl = curl_init();
				curl_setopt_array($curl, [
					CURLOPT_URL            => REAL_URL.$yesterday,
					CURLOPT_RETURNTRANSFER => TRUE,
					CURLOPT_ENCODING       => '',
					CURLOPT_MAXREDIRS      => 10,
					CURLOPT_TIMEOUT        => 0,
					CURLOPT_FOLLOWLOCATION => TRUE,
					CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST  => 'GET',
					CURLOPT_HTTPHEADER     => ['Authorization: Basic '.REAL_AUTH],
				]);
				$receive_json = curl_exec($curl);
				curl_close($curl);
				//echo $receive_json;
				//exit;
			
				// json data Receive
				$receive_arr  = [];
				$receive_temp = json_decode($receive_json, TRUE);
				//echo $receive_temp;
				//exit;
				
				$receive_arr  = json_decode($receive_temp, TRUE);
				//$receive_arr = stripslashes($receive_arr);
				//print_r($receive_arr);
				//exit;
	
				// json Data 검증
				//echo("isValid:".$receivearr['isValid']);
				//exit;
				if($receive_arr['isValid'] != TRUE || !empty($receive_arr['errorMsg'])){
					$results['success'] = ERR_CODE_JSON;
					$results['msg']     = "(".$receive_arr['errorMsg'].") 수신 JSON 오류!";
					throw new Exception($receive_arr['errorMsg']." 수신 JSON 오류!");
				}

			    // 학교 매장코드 가져오기
			    $code_type            = '01';       // 코드구분:학교매장코드
				$receive_store        = $custom_arr['00113'];
			    //$receive_storecode    = $receipts['0']['storeCode'];
			    //$receive_storecode    = $receipts['0']['storeCode'];
			    $univstore_code       = $this->DAU->getStorecode($code_type,$receive_store);
				$conversion_storecode = substr($univstore_code['univstore_code'],-7,7);

                // 각 배열의 정의와 선언
                $receipts = [];         // 1단계기본배열:복수개
		        $receipts = $receive_arr['receipts'];

			    $paymenttype_arr   = [];
			    $saletype_arr      = [];
			    $discounttype_arr  = [];
			    $cardapproval_arr  = [];
			    $cardpurchaser_arr = [];
			    $standard_codes    = $this->DAU->getStandardcode();  // 표준 변환코드 가져오기
			    foreach($standard_codes as $key => $value){
			    	if($value['CODETYPE'] == '11'){          // 결제구분(현금/페이코/신용카드/알리페이/)
                        $paymenttype_arr[$value['OFFERCODE']] = $value['CWAYCODE'].'|'.$value['CODENAME'];
			    	} else if($value['CODETYPE'] == '12') {  // 판매구분(정상/반품)
                        $saletype_arr[$value['OFFERCODE']] = $value['CWAYCODE'];
			    	} else if($value['CODETYPE'] == '13') {  // 할인구분(금액할인)
			    		$discounttype_arr[$value['OFFERCODE']] = $value['CWAYCODE'];
			    	} else if($value['CODETYPE'] == '21') {  // 카드등록구분(임의/POS/CAT/VCAT/)
			    		$cardapproval_arr[$value['OFFERCODE']] = $value['CWAYCODE'];
			    	} else if($value['CODETYPE'] == '22') {  // 매입사코드
			    		$cardpurchaser_arr[$value['OFFERCODE']] = $value['CWAYCODE'];
			    	}
			    }
				$processdate          = date("YmdHis",time());

			    $baseParams = [];
			    $logParams  = [];
                foreach($receipts as $basekey => $basevalue){
                    $baseParams = [
			    		'UNIVCODE'              => $univcode,
			    		'SUBUNIVCODE'           => CAMPUS,
			    		'SALEDATE'              => $basevalue['saleDate'],
			    		'STORECODE'             => $conversion_storecode,
			    		'POSID'                 => substr($basevalue['posCode'],-2,2),
			    		'BILLNUMBER'            => $basevalue['billNo'],
			    		'SALETYPE'              => $saletype_arr[$basevalue['saleType']], // 코드변환
			    		'CREATEDATE'            => $processdate,
			    		'TOORDER_PLATFORMID'    => OFFER,
			    		'TOORDER_PLATFORMTYPE'  => PLATFORMTYPE,
			    		'TOORDER_ITEMCOUNT'     => 0,
			    		'TOORDER_AMOUNT'        => $basevalue['saleAmount'],
			    		'TOORDER_TOTALAMOUNT'   => $basevalue['totalAmount'],
			    		'TOORDER_DCAMOUNT'      => $basevalue['dcAmount'],
			    		'TOORDER_INAMOUNT'      => $basevalue['totalAmount'],
			    		'TOORDER_CHANGEMONEY'   => 0,
			    		'TOORDER_DFREEAMOUNT'   => $basevalue['saleTaxfreeAmount'],
			    		'TOORDER_TAXAMOUNT'     => $basevalue['saleTaxAmount'],
			    		'TOORDER_SUPPLYAMOUNT'  => $basevalue['supplyAmount'],
			    		'TOORDER_VATAMOUNT'     => $basevalue['taxAmount'],
			    		'TOORDER_ORGSALEDATE'   => $basevalue['orgSaleDate'],
			    		'TOORDER_ORGPOSID'      => substr($basevalue['orgPosCode'],-2,2),
			    		'TOORDER_ORGBILLNUMBER' => $basevalue['orgBillNo'],
			    		'INSERTDATE'            => $processdate,
			    		'INSERTID'              => INSERTID_CRON,
			    	];
                    /*
                    $tempjson_basevalue  = json_encode($basevalue,JSON_UNESCAPED_UNICODE);
					$strip_basevalue     = str_replace('\\','',$tempjson_basevalue);
					$array_basevalue     = json_decode($strip_basevalue,TRUE);
					$json_basevalue      = json_encode($array_basevalue,JSON_UNESCAPED_UNICODE);
					*/

			    	$logParams = [
			    		'UNIVCODE'            => $univcode,
			    		'SUBUNIVCODE'         => CAMPUS,
			    		'SALEDATE'            => $basevalue['saleDate'],
			    		'STORECODE'           => $conversion_storecode,
			    		'POSID'               => substr($basevalue['posCode'],-2,2),
			    		'BILLNUMBER'          => $basevalue['billNo'],
                        'JSONLOG'             => json_encode($basevalue,JSON_UNESCAPED_UNICODE),
			    		'INSERTDATE'          => $processdate,
			    		'INSERTID'            => INSERTID_CRON,
			    	];
    
                    $receiptProduct  = $basevalue['receiptProduct'];
			    	$baseParams['TOORDER_ITEMCOUNT'] = count($receiptProduct);
    
                    $receiptDiscount = $basevalue['receiptDiscount'];
			    	//print_r($receiptDiscount);
			    	//exit;
                    $receiptPayment  = $basevalue['receiptPayment'];
                    $receiptCard     = $basevalue['receiptCard'];
                    $receiptCash     = $basevalue['receiptCash'];
    
			    	writeLog("[{$sLogFileId}] baseParams= " .json_encode($baseParams,JSON_UNESCAPED_UNICODE), $sLogPath);
    
			    	$productParams  = [];
			    	foreach($receiptProduct as $productkey => $productvalue){
					    $productParams[] = [
					    	'UNIVCODE'             => $univcode,
					    	'SUBUNIVCODE'          => CAMPUS,
					    	'SALEDATE'             => $basevalue['saleDate'],
					    	'STORECODE'            => $conversion_storecode,
					        'POSID'                => substr($basevalue['posCode'],-2,2),
					        'BILLNUMBER'           => $basevalue['billNo'],
					    	'PRODUCT_SEQ'          => $productvalue['seq'],
					        'SALETYPE'             => $saletype_arr[$basevalue['saleType']], // 코드변환
					    	'CREATEDATE'           => $processdate,
					    	'PRODUCT_ITEMCODE'     => $productvalue['productCode'],
					    	'PRODUCT_ITEMNAME'     => (mb_strlen($productvalue['productName'],"UTF-8")<=15)?$productvalue['productName']:mb_substr($productvalue['productName'], 0, 15, "UTF-8"),
					    	'PRODUCT_COST'         => $productvalue['productPrice'],
					    	'PRODUCT_PRICE'        => $productvalue['productPrice'],
					    	'PRODUCT_QTY'          => $productvalue['saleQty'],
					    	'PRODUCT_AMOUNT'       => $productvalue['saleAmount'],
					    	'PRODUCT_SALEAMOUNT'   => $productvalue['totalAmount'],
					    	//'PRODUCT_DCTYPE'       => (!empty($discounttype_arr[$receiptDiscount[$productkey]['dcType']]))?$discounttype_arr[$receiptDiscount[$productkey]['dcType']]:'',  // 코드변환
					    	'PRODUCT_DCTYPE'       => (!empty($receiptDiscount[$productkey]['dcType']))?$receiptDiscount[$productkey]['dcType']:'',  // 코드변환
					    	'PRODUCT_DCAMOUNT'     => $productvalue['dcAmount'],
					    	'PRODUCT_TAXTYPE'      => ($productvalue['taxAmount'] == 0)?'F':'T',
					    	'PRODUCT_SUPPLYAMOUNT' => $productvalue['supplyAmount'],
					    	'PRODUCT_VATAMOUNT'    => $productvalue['taxAmount'],
					    	'INSERTDATE'           => $processdate,
					        'INSERTID'             => INSERTID_CRON,
					    ];
				    }
				    writeLog("[{$sLogFileId}] productParams= " .json_encode($productParams,JSON_UNESCAPED_UNICODE), $sLogPath);

				    $paymentParams  = [];
				    foreach($receiptPayment as $paymentkey => $paymentvalue){
				    	$payment_type = explode("|",$paymenttype_arr[$paymentvalue['payCode']]);
				    	$paymentParams[] = [
				    		'UNIVCODE'             => $univcode,
				    		'SUBUNIVCODE'          => CAMPUS,
				    		'SALEDATE'             => $basevalue['saleDate'],
				    		'STORECODE'            => $conversion_storecode,
				    	    'POSID'                => substr($basevalue['posCode'],-2,2),
				    	    'BILLNUMBER'           => $basevalue['billNo'],
				    		'PAYMENT_SEQ'          => $paymentvalue['seq'],
				    	    'SALETYPE'             => $saletype_arr[$basevalue['saleType']], // 코드변환
				    		'CREATEDATE'           => $processdate,
				    		'PAYMENT_METHODCODE'   => !(empty($payment_type['0']))?$payment_type['0']:' ', // 코드변환explode
				    		'PAYMENT_METHODNAME'   => !(empty($payment_type['1']))?$payment_type['1']:' ', // 코드변환explode
				    		'PAYMENT_AMOUNT'       => $paymentvalue['payAmount'],
				    		'PAYMENT_INAMOUNT'     => $paymentvalue['payAmount'],
				    		'PAYMENT_CHANGEMONEY'  => 0,
				    		'INSERTDATE'           => $processdate,
				    	    'INSERTID'             => INSERTID_CRON,
				    	];
				    }
				    writeLog("[{$sLogFileId}] paymentParams= " .json_encode($paymentParams,JSON_UNESCAPED_UNICODE), $sLogPath);

				    $cardParams  = [];
				    foreach($receiptCard as $cardkey => $cardvalue){
				    	$cardParams[] = [
				    		'UNIVCODE'             => $univcode,
				    		'SUBUNIVCODE'          => CAMPUS,
				    		'SALEDATE'             => $basevalue['saleDate'],
				    		'STORECODE'            => $conversion_storecode,
				    		'POSID'                => substr($basevalue['posCode'],-2,2),
				    		'BILLNUMBER'           => $basevalue['billNo'],
				    		'CARD_SEQ'             => $cardvalue['seq'],
				    		'SALETYPE'             => $saletype_arr[$basevalue['saleType']], // 코드변환
				    		'CREATEDATE'           => $processdate,
				    		'CARD_PAYNAME'         => $paymentParams[$cardkey]['PAYMENT_METHODCODE'],  // 하단재처리 : 결제구분 코드
				    		'CARD_VANNAME'         => OFFER,                      // VAN사명
				    		'CARD_TID'             => $cardvalue['tid'],          // TID
				    		'CARD_APPTYPE'         => $cardapproval_arr[$cardvalue['appType']], // 범례 N:임의,P:POS,C:CAT/VCAT
				    		'CARD_CARDNO'          => $cardvalue['cardNo'],       // 카드번호
				    		'CARD_APPROVALNO'      => $cardvalue['appNo'],        // 승인번호
				    		'CARD_AMOUNT'          => $cardvalue['cardAmount'],   // 하단재처리: 결제금액
				    		'CARD_CARDAMOUNT'      => $cardvalue['cardAmount'],   // 하단재처리: 카드(페이)금액
				    		'CARD_POINTAMOUNT'     => FLOATZERO,                  // 하단재처리: 포인트금액
				    		'CARD_COUPONAMOUNT'    => FLOATZERO,                  // 하단재처리: 상품권선불카드금액
				    		'CARD_INSTALLMENT'     => installmentConvert($cardvalue['installment']), // 할부개월수(문자열)
				    		'CARD_ISSUERCODE'      => $cardvalue['issuerCode'],   // 발급사코드
				    		'CARD_ISSUERNAME'      => $cardvalue['issuerName'],   // 발급사명
				    		'CARD_ACQUIRERCODE'    => (!empty($cardpurchaser_arr[$cardvalue['acquirerCode']]))?$cardpurchaser_arr[$cardvalue['acquirerCode']]:' ',  // 매입사코드(씨웨이코드 변환)
				    		'CARD_ACQUIRERNAME'    => $cardvalue['acquirerName'], // 매입사명
				    		'CARD_TRADEDATE'       => date("YmdHis",strtotime($cardvalue['trDateTime'])),  // 거래일시
				    		'CARD_APPROVALDATE'    => $cardvalue['appDate'],      // 승인일시
				    		'CARD_ORGAPPDATE'      => $cardvalue['orgAppDate'],   // 반품시 원거래 판매일
				    		'CARD_ORGAPPNO'        => $cardvalue['orgAppNo'],     // 반품시 원거래 승인번호
				    		'INSERTDATE'           => $processdate,
				    		'INSERTID'             => INSERTID_CRON,
				    	];
				    	
				    	$var_arr = [
				    		'payment_seq'   => $cardkey,
                            'payment_type'  => $paymentParams[$cardkey]['PAYMENT_METHODCODE'],
				    		'payment_name'  => $paymentParams[$cardkey]['PAYMENT_METHODNAME'],
				    		'issuer_code'   => $cardvalue['issuerCode'],
				    		'issuer_name'   => $cardvalue['issuerName'],
				    		'acquirerCode'  => $cardvalue['acquirerCode'],
				    		'acquirer_name' => $cardvalue['acquirerName'],
				    		'card_amount'   => $cardvalue['cardAmount'],
				    	];
				    	writeLog("[{$sLogFileId}] cardPaynameSelect_arr= " .json_encode($var_arr,JSON_UNESCAPED_UNICODE), $sLogPath);
				    	$payName = cardPaynameSelect($var_arr);
				    	writeLog("[{$sLogFileId}] cardPaynameSelect_res= " .json_encode($payName,JSON_UNESCAPED_UNICODE), $sLogPath);
				    	if($payName['rename_flag'] == 'Y'){
				    		$paymentParams[$cardkey]['PAYMENT_METHODCODE'] = $payName['PAYMENT_METHODCODE'];
				    		$paymentParams[$cardkey]['PAYMENT_METHODNAME'] = $payName['PAYMENT_METHODNAME'];
				    		$cardParams[$cardkey]['CARD_PAYNAME']                    = $payName['CARD_PAYNAME'];
				    		$cardParams[$cardkey]['CARD_AMOUNT']                     = $payName['CARD_AMOUNT'];
				    		$cardParams[$cardkey]['CARD_CARDAMOUNT']                 = $payName['CARD_CARDAMOUNT'];
				    		$cardParams[$cardkey]['CARD_POINTAMOUNT']                = $payName['CARD_POINTAMOUNT'];
				    		$cardParams[$cardkey]['CARD_COUPONAMOUNT']               = $payName['CARD_COUPONAMOUNT'];
				    	}
				    }
				    writeLog("[{$sLogFileId}] cardParams= " .json_encode($cardParams,JSON_UNESCAPED_UNICODE), $sLogPath);

				    $cashParams  = [];
				    foreach($receiptCash as $cashkey => $cashvalue){
				    	//$payment_type = explode("|",$paymenttype_arr[$paymentvalue['payCode']]);
				    	$cashParams[] = [
				    		'UNIVCODE'             => $univcode,
				    		'SUBUNIVCODE'          => CAMPUS,
				    		'SALEDATE'             => $basevalue['saleDate'],
				    		'STORECODE'            => $conversion_storecode,
				    		'POSID'                => substr($basevalue['posCode'],-2,2),
				    		'BILLNUMBER'           => $basevalue['billNo'],
				    		'CASH_SEQ'             => $cashvalue['seq'],
				    		'SALETYPE'             => $saletype_arr[$basevalue['saleType']], // 코드변환
				    		'CREATEDATE'           => $processdate,
				    		'CASH_VANNAME'         => OFFER,                     // VAN사명
				    		'CASH_TYPE'            => $cashvalue['type'],        // 현금영수증유형(1:소득공제,2:지출증빙)
				    		'CASH_TID'             => $cashvalue['tid'],         // 단말기번호
				    		'CASH_AMOUNT'          => $cashvalue['cashAmount'],  // 결제금액
				    		'CASH_CARDNO'          => $cashvalue['cardNo'],      // 카드번호
				    		'CASH_APPTYPE'         => $cardapproval_arr[$cashvalue['appType']], // 범례 N:임의,P:POS,C:CAT/VCAT
				    		'CASH_APPROVALNO'      => $cashvalue['appNo'],       // 승인번호
				    		'CASH_TRADEDATE'       => date("YmdHis",strtotime($cardvalue['trDateTime'])),  // 거래일시
				    		'CASH_APPROVALDATE'    => $cardvalue['appDate'],     // 승인일시
				    		'CASH_ORGAPPDATE'      => $cardvalue['orgAppDate'],  // 반품시 원거래 판매일
				    		'CASH_ORGAPPNO'        => $cardvalue['orgAppNo'],    // 반품시 원거래 승인번호
				    		'INSERTDATE'           => $processdate,
				    		'INSERTID'             => INSERTID_CRON,
				    	];
				    }
				    writeLog("[{$sLogFileId}] cashParams= " .json_encode($cashParams,JSON_UNESCAPED_UNICODE), $sLogPath);
				    writeLog("[{$sLogFileId}] logParams= " .json_encode($logParams,JSON_UNESCAPED_UNICODE), $sLogPath);

                    $insert_results = $this->DAU->save2Order($baseParams,$productParams,$paymentParams,$cardParams,$cashParams,$logParams);
				    if($insert_results != "0000"){
				      	writeLog("[{$sLogFileId}] error bill= " .json_encode($baseParams['BILLNUMBER'],JSON_UNESCAPED_UNICODE), $sLogPath);
				    } else {
				      	writeLog("[{$sLogFileId}] ok bill= " .json_encode($baseParams['BILLNUMBER'],JSON_UNESCAPED_UNICODE), $sLogPath);
				    }
				}
			}
		} 
		catch(File_exception $e) 
		{
			$results['success'] = $e->error;
			$results['msg']     = $e->message;
		}
		writeLog("[{$sLogFileId}] results= " . json_encode($results,JSON_UNESCAPED_UNICODE), $sLogPath);
		writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		return $results;
	}

    //주문정보 테스트
    public function ci_ver() {
        echo CI_VERSION;
	}
}
/* End of file dauapi.php */
/* Location: ./application/controllers/dauapi.php */