<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dauApi extends CT_Controller {

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
            'data'       => null
        ];

        try
        {
            $this->form_validation->set_rules('UnivCode', '', 'trim|required|xss_clean');
            
            writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);
            //$univcode = $_POST['UnivCode'];
            $univcode = $this->input->post('UnivCode', TRUE);
            writeLog("[{$sLogFileId}] post_UnivCode= " .json_encode($univcode,JSON_UNESCAPED_UNICODE), $sLogPath);
            // Post Data 검증
            if(empty($univcode)){
                $results['success'] = ERR_CODE_POST;
                $results['msg']     = "(".$univcode.") UniovCode가 POST전달되지 않았습니다!";
                throw new Exception("UniovCode가 POST전달되지 않았습니다!");
            }

            //$today = date("Ymd");
            $yesterday = date('Ymd', $_SERVER['REQUEST_TIME']-86400);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => TEST_URL.$yesterday,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => ['Authorization: Basic '.TEST_AUTH],
            ]);
            $receivejson = curl_exec($curl);
            curl_close($curl);
            
            //echo $receivejson;
            //exit;
            //$receiveHeader = apache_request_headers();
            // UnivCode는 헤더에서 받아오기로 함 2020-02-26 고병수차장 autoload->database.php에서 처리함.
        
		    // json data Receive
            $receivetemp = json_decode($receivejson, TRUE);
            $receivearr  = json_decode($receivetemp, TRUE);
            //$receivearr = stripslashes($receivearr);
            //print_r($receivearr);
            //exit;

            // json Data 검증
            //echo("isValid:".$receivearr['isValid']);
            //exit;
            if($receivearr['isValid'] != TRUE || !empty($receivearr['errorMsg'])){
                $results['success'] = ERR_CODE_JSON;
                $results['msg']     = "(".$receivearr['errorMsg'].") 수신 JSON 오류!";
                throw new Exception($receivearr['errorMsg']." 수신 JSON 오류!");
            }
        
            // Config정보 가져오기
			// 학교 매장코드 가져오기
			
            // 각 배열의 정의와 선언
            $receipts = [];         // 1단계기본배열:복수개
		    $receipts = $receivearr['receipts'];
            //print_r($receipts);
            //exit;


            $receiptProduct  = [];   // 2단계:판매상품:복수
			$baseParams = [];
            foreach($receipts as $key => $value){
                $baseParams = [
					'UNIVCODE'            => $univcode,
					'SUBUNIVCODE'         => '001',
					'SALEDATE'            => $value['saleDate'],
					'STORECODE'           => $value['storeCode'],
					'POSID'               => $value['posCode'],
					'BILLNUMBER'          => $value['billNo'],
					'CREATEDATE'          => date("YYYYMMDDHHmmss",time()),
					'ORDER_PLATFORMID'    => OFFER,
					'ORDER_PLATFORMTYPE'  => PLATFORMTYPE,
					'ORDER_AMOUNT'        => $value['saleAmount'],
					'ORDER_TOTALAMOUNT'   => $value['totalAmount'],
					'ORDER_DCAMOUNT'      => $value['dcAmount'],
					'ORDER_INAMOUNT'      => $value['totalAmount'],
					'ORDER_CHANGEMONEY'   => 0,
					'ORDER_DFREEAMOUNT'   => $value['saleTaxfreeAmount'],
					'ORDER_TAXAMOUNT'     => $value['saleTaxAmount'],
					'ORDER_SUPPLYAMOUNT'  => $value['supplyAmount'],
					'ORDER_VATAMOUNT'     => $value['taxAmount'],
					'ORDER_ORGSALEDATE'   => $value['orgSaleDate'],
					'ORDER_ORGPOSID'      => $value['orgPosCode'],
					'ORDER_ORGBILLNUMBER' => $value['orgBillNo'],
					'INSERTDATE'          => date("YYYYMMDDHHmmss",time()),
					'INSERTID'            => INSERTID,
				];

                $receiptProduct  = $value['receiptProduct'];
            }


			$codeType  = '01';       // 코드구분:학교매장코드
			$offerStorecode  = $receipts['0']['storeCode'];
			$storecode = $this->DAU->getStorecode($codeType,$offerStorecode);    // 키오스크 설치 매장코드 배열			

            exit;

            //$receiptProduct  = [];   // 2단계:판매상품:복수
            $receiptDiscount = [];   // 2단계:할인내역:복수
            $receiptPayment  = [];   // 2단계:결제내역:복수
            $receiptCard     = [];   // 2단계:카드내역:복수
            $receiptCash     = [];   // 2단계:현금내역:복수

            // 순서상 orderProducts(복)/payments(복)/order(단) 배열 먼저 분리(단수임)
		    $receiptProduct  = $receipts['0']['receiptProduct'];
		    $receiptDiscount = $receipts['receiptDiscount'];
		    $receiptPayment  = $receipts['receiptPayment'];
		    $receiptCard     = $receipts['receiptCard'];
		    $receiptCash     = $receipts['receiptCash'];

            print_r($receiptProduct);
            exit;


            unset($order['orderProducts']);
            unset($order['payments']);
            unset($order['benefits']);

		// 배열의 분리
		// products와 options의 분리
		if (is_array($products)) {
		    for ($i = 0;$i < count($products);$i++) {
				if(!empty($products[$i]['orderProductOptions'])) {
                    $options[$i] = $products[$i]['orderProductOptions'];
				} else {
					$options[$i] = '';
			    }
		        unset($products[$i]['orderProductOptions']);
		    }
		} else {
			$products = '';
			$options = '';
		}
		// payments와 card,coupon 분리
		if (is_array($payments)) {
		    for ($i = 0;$i < count($payments);$i++) {
				if(!empty($payments[$i]['cardPaymentDetail'])) {
                    $cards[$i] = $payments[$i]['cardPaymentDetail'];
				} else {
                    $cards[$i] = "";
				}
				if(!empty($payments[$i]['couponPaymentDetail'])) {
					$coupons[$i] = $payments[$i]['couponPaymentDetail'];
			    } else {
                    $coupons[$i] = "";
				}
   		        unset($payments[$i]['cardPaymentDetail']);
				unset($payments[$i]['couponPaymentDetail']);
		    }
		} else {
			$payments = '';
		    $cards = '';
			$coupons = '';
		}

        //echo "order : <br>\n<br>\n";print_r($order);echo"<br>\n<br>\n";
		//echo "products : <br>\n<br>\n";print_r($products);echo"<br>\n<br>\n";
		//echo "options : <br>\n<br>\n";print_r($options);echo"<br>\n<br>\n";
		//echo "payments : <br>\n<br>\n";print_r($payments);echo"<br>\n<br>\n";
		//echo "cards : <br>\n<br>\n";print_r($cards);echo"<br>\n<br>\n";
		//echo "coupons : <br>\n<br>\n";print_r($coupons);echo"<br>\n<br>\n";
		//echo "benefits : <br>\n<br>\n";print_r($benefits);echo"<br>\n<br>\n";
        //exit;

        // params 만들기
		// 1. 1차배열 order의 param 만들기
		$order_param = arrange_param($order,'order');

		// 2.1 복수배열을 보내자 products/options,
		if (is_array($products)) {
		    for ($i = 0;$i < count($products);$i++) {
 			    $products[$i]['univcode'] = $univcode;                 // univcode 보내주기
				$products[$i]['franchiseCd'] = $order['franchiseCd'];  // franchiseCd(=storecode)는 order에서만 보내주네요
			    $products[$i]['posNo'] = $order['posNo'];              // posNo는 products에 없네요
			    $Products_params[$i] = arrange_param($products[$i],'products');
			}
		} else {
            $Products_params[$i] = "";
		}
        //echo " options : ".count($options)." ea<br>\n";
		// 2.3 다차원배열을 보내자 options,
		if(is_array($options)) {
			for ($i = 0;$i < count($options);$i++) {
		        if(!empty($options[$i])) {
			        for ($j = 0;$j < count($options[$i]);$j++) {
						if(!empty($options[$i][$j])) {
                            $options[$i][$j]['univcode'] = $univcode;                 // franchiseCd(=storecode)는 order에서만 보내주네요
  			                $options[$i][$j]['franchiseCd'] = $order['franchiseCd'];  // franchiseCd(=storecode)는 order에서만 보내주네요
  		                    $options[$i][$j]['saleDay'] = $order['saleDay'];          // saleDay는 options 없네요
		    	            $options[$i][$j]['posNo'] = $order['posNo'];              // posNo는 options 없네요
   		                    $options[$i][$j]['billNo'] = $order['billNo'];            // billNo는 options 없네요
			                $options_params[$i][$j] = arrange_param($options[$i][$j],'options');
                        }
					}
                }  else {
					unset($options[$i]);
				}
			}
		} else {
            $options_params = "";
		}

		// 2.2 복수배열을 보내자 payments
		if (is_array($payments)) {
		    for ($i = 0;$i < count($payments);$i++) {
   			    $payments[$i]['univcode'] = $univcode;
				$payments[$i]['franchiseCd'] = $order['franchiseCd'];  // franchiseCd(=storecode)는 order에서만 보내주네요
			    $payments[$i]['posNo'] = $order['posNo'];              // posNo는 payments에 없네요
			    $payments_params[$i] = arrange_param($payments[$i],'payments');
		    } 
		} else {
            $payments_params[$i] = "";
		}
		
		// 2.3 복수배열을 보내자 cards
		for  ($i = 0;$i < count($cards);$i++) {
		    if (is_array($cards[$i])) {
				$cards[$i]['univcode'] = $univcode;
    	        $cards[$i]['franchiseCd'] = $order['franchiseCd'];
       	        $cards[$i]['saleDay'] = $order['saleDay'];
	            $cards[$i]['posNo'] = $order['posNo'];
				$cards_param[$i] = arrange_param($cards[$i],'cards');
			}
        }				

        // 2.4 복수배열을 보내자 coupons(고과장이 카드는 단수로 온다고 함 리마크 처리)
        // 배열이 없는 경우 에러(http 500)가 나서 일단 이대로 둠: is_array함수를 사용하여 에러를 잡음
		for  ($i = 0;$i < count($coupons);$i++) {
		    if (is_array($coupons[$i])) {
				$coupons[$i]['univcode'] = $univcode;
    	        $coupons[$i]['franchiseCd'] = $order['franchiseCd'];
       	        $coupons[$i]['saleDay'] = $order['saleDay'];
	            $coupons[$i]['posNo'] = $order['posNo'];
				$coupons_param[$i] = arrange_param($coupons[$i],'coupons');
			}
        }				

		// 2.5 추가) benefits array
    	if (is_array($benefits)) {
		    for ($i = 0;$i < count($benefits);$i++) {
				$benefits[$i]['univcode'] = $univcode;
  			    $benefits[$i]['franchiseCd'] = $order['franchiseCd'];  // franchiseCd(=storecode)는 order에서만 보내주네요
  		        $benefits[$i]['saleDay'] = $order['saleDay'];          // saleDay는 options 없네요
		    	$benefits[$i]['posNo'] = $order['posNo'];              // posNo는 options 없네요
   		        $benefits[$i]['billNo'] = $order['billNo'];            // billNo는 options 없네요
                $benefits[$i]['paymentSeq'] = $payments[$i]['paymentSeq']; // paymentSeq는 payments에 있어요
				$benefits_params[$i] = arrange_param($benefits[$i],'benefits');
			} 
		} else {
            $benefits_params = "";
		}

		//$options_params = sort($options_params);
        //echo "order_param : <br>\n";print_r($order_param);echo"<br>\n<br>\n";
		//echo "Products_params : <br>\n";print_r($Products_params);echo"<br>\n<br>\n";
		//echo "options_params : <br>\n";print_r($options_params);echo"<br>\n<br>\n";
		//echo "payments_params : <br>\n";print_r($payments_params);echo"<br>\n<br>\n";
		//echo "cards_param : <br>\n";print_r($cards_param);echo"<br>\n<br>\n";
		//echo "coupons_param : <br>\n";print_r($coupons_param);echo"<br>\n<br>\n";
		//echo "benefits_params : <br>\n";print_r($benefits_params);echo"<br>\n<br>\n";
        //exit;
        if (empty($Products_params)) $Products_params ='';
		if (empty($options_params)) $options_params ='';
		if (empty($payments_params)) $payments_params ='';
        if (empty($cards_param)) $cards_param ='';
		if (empty($coupons_param)) $coupons_param ='';
        if (empty($benefits_params)) $benefits_params ='';
		//model로 던져 DB에 트랜잭션 처리를 위해 한방에 처리(단 널배열 처리방법 고민 is_array로 해결함.)
		// MS-SQL2019이후 일부 디비에서 파라메타를 ,,, 이렇게 못넣어서 재구성함.
		//foreach($order as $key => $value){if(isset($value)) $order[$key] = '';}
		//$insertDB = $this->API->insertDB($order_param, $Products_params, $options_params, $payments_params, $cards_param, $coupons_param, $benefits_params);
		$insertDB = $this->API->insertDB($order_param, $Products_params, $options_params, $payments_params, $cards_param, $coupons_param, $benefits_params);

        if ($insertDB !== RES_CODE_SUCCESS) {
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "InsertDB 처리실패!!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'])." eMessage=".json_encode($message['error']['errorMessage']), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        
		// 이하는 모두 로그를 쓰는 루틴인데요 나중에 정리해야 합니다. 무식하게 로그한줄마다 for를 돌리고 있습니다. 애고 나중에 모아서 할 예정!!
		// order
		writeLog("[{$sLogFileId}] order=" . json_encode(implode( '|', $order_param ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		// Products
		for ($i = 0;$i < count($Products_params);$i++) {
			writeLog("[{$sLogFileId}] Products[".$i."] = " . json_encode(implode( '|', $Products_params[$i] ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		}
		// options
		if(is_array($options_params)) {
            for ($i = 0;$i < count($options_params);$i++) {
				if(empty($options_params[$i])) $i++;
                for ($j = 0;$j < count($options_params[$i]);$j++) {
					if(empty($options[$i][$j])) $j++;
				    writeLog("[{$sLogFileId}] Options[".$i."][".$j."] =" . json_encode(implode( '|', $options_params[$i][$j] ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
			    }
		    }
		} else {
            writeLog("[{$sLogFileId}] Options= 데이터가 없습니다.", $sLogPath, $bLogable);
		}
		// payments
		for ($i = 0;$i < count($payments_params);$i++) {
			writeLog("[{$sLogFileId}] payments[".$i."] = " . json_encode(implode( '|', $payments_params[$i] ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		}
		// cards
		if(!empty($cards_param)) {
            for ($i = 0;$i < count($cards_param);$i++) {
				if(empty($cards_param[$i])) $i++;
                writeLog("[{$sLogFileId}] cards=" . json_encode(implode( '|', $cards_param[$i] ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
			}
		} else {
            writeLog("[{$sLogFileId}] cards= 데이터가 없습니다.", $sLogPath, $bLogable);
		}
		// coupons
		if(!empty($coupons_param)) {
            for ($i = 0;$i < count($coupons_param);$i++) {
				if(empty($coupons_param[$i])) $i++;
			    writeLog("[{$sLogFileId}] coupons=" . json_encode(implode( '|', $coupons_param[$i] ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
			}
		} else {
            writeLog("[{$sLogFileId}] coupons= 데이터가 없습니다.", $sLogPath, $bLogable);
		}
		// benefits
		if (!empty($benefits_params)) {
    		for ($i = 0;$i < count($benefits_params);$i++) {
			    writeLog("[{$sLogFileId}] benefits[".$i."] = " . json_encode(implode( '|', $benefits_params[$i] ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		    }
		} else {
            writeLog("[{$sLogFileId}] benefits= 데이터가 없습니다.", $sLogPath, $bLogable);
		}

        // Ends of Log Write
		writeLog("[{$sLogFileId}] result=" . json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		echo json_encode($message);
        return;
    }
    catch (File_exception $e) {
        $results['success'] = $e->error;
        $results['msg']     = $e->message;
    }
}


    //주문정보 테스트
    public function ci_ver() {
        echo CI_VERSION;
	}
}    