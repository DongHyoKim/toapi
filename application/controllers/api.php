<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CT_Controller {

    private $json_array = array();

    public function __construct(){
		parent::__construct();
		$this->load->model('Api_model2','API');
    }	
    
    public function index(){
    }

    //�ֹ����� receive api
    public function receivedata() {
        
    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/receivedata/' . date('Ymd') . '_data.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        $receivejson = array();
        //$receiveHeader = apache_request_headers();
        // UnivCode�� ������� �޾ƿ���� �� 2020-02-26 ���������� autoload->database.php���� ó����.
        
		// json data Receive
        $receivejson = json_decode(file_get_contents('php://input'), true);  // json data name :order
        $univcode = $_POST['UnivCode'];
		
		if (!$univcode) {      
            $message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "univcode�� Header�� �����ϴ�.";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        writeLog("[{$sLogFileId}] univcode=" . json_encode($univcode,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        
        //array dividing
        // �� �迭�� ���ǿ� ����
        $order = array();    // 1�ܰ�⺻�迭:�ܼ�
		$order = $receivejson['order'];

		$products = array(); // 2�ܰ�:����
        $options = array();  // 3�ܰ�:����
		$payments = array(); // 2�ܰ�:����
		$benefits = array(); // 2�ܰ�:����
		$cards = array();    // 3�ܰ�:�ܼ�
        $coupons = array();  // 3�ܰ�:�ܼ�


        // ������ orderProducts(��)/payments(��)/order(��) �迭 ���� �и�(�ܼ���)
		$order['univcode'] = $univcode;
		$products = $order['orderProducts'];
		$payments = $order['payments'];
		$benefits = $order['benefits'];
        unset($order['orderProducts']);
        unset($order['payments']);
        unset($order['benefits']);

		// �迭�� �и�
		// products�� options�� �и�
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
		// payments�� card,coupon �и�
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

        // params �����
		// 1. 1���迭 order�� param �����
		$order_param = arrange_param($order,'order');

		// 2.1 �����迭�� ������ products/options,
		if (is_array($products)) {
		    for ($i = 0;$i < count($products);$i++) {
 			    $products[$i]['univcode'] = $univcode;                 // univcode �����ֱ�
				$products[$i]['franchiseCd'] = $order['franchiseCd'];  // franchiseCd(=storecode)�� order������ �����ֳ׿�
			    $products[$i]['posNo'] = $order['posNo'];              // posNo�� products�� ���׿�
			    $Products_params[$i] = arrange_param($products[$i],'products');
			}
		} else {
            $Products_params[$i] = "";
		}
        //echo " options : ".count($options)." ea<br>\n";
		// 2.3 �������迭�� ������ options,
		if(is_array($options)) {
			for ($i = 0;$i < count($options);$i++) {
		        if(!empty($options[$i])) {
			        for ($j = 0;$j < count($options[$i]);$j++) {
						if(!empty($options[$i][$j])) {
                            $options[$i][$j]['univcode'] = $univcode;                 // franchiseCd(=storecode)�� order������ �����ֳ׿�
  			                $options[$i][$j]['franchiseCd'] = $order['franchiseCd'];  // franchiseCd(=storecode)�� order������ �����ֳ׿�
  		                    $options[$i][$j]['saleDay'] = $order['saleDay'];          // saleDay�� options ���׿�
		    	            $options[$i][$j]['posNo'] = $order['posNo'];              // posNo�� options ���׿�
   		                    $options[$i][$j]['billNo'] = $order['billNo'];            // billNo�� options ���׿�
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

		// 2.2 �����迭�� ������ payments
		if (is_array($payments)) {
		    for ($i = 0;$i < count($payments);$i++) {
   			    $payments[$i]['univcode'] = $univcode;
				$payments[$i]['franchiseCd'] = $order['franchiseCd'];  // franchiseCd(=storecode)�� order������ �����ֳ׿�
			    $payments[$i]['posNo'] = $order['posNo'];              // posNo�� payments�� ���׿�
			    $payments_params[$i] = arrange_param($payments[$i],'payments');
		    } 
		} else {
            $payments_params[$i] = "";
		}
		
		// 2.3 �����迭�� ������ cards
		for  ($i = 0;$i < count($cards);$i++) {
		    if (is_array($cards[$i])) {
				$cards[$i]['univcode'] = $univcode;
    	        $cards[$i]['franchiseCd'] = $order['franchiseCd'];
       	        $cards[$i]['saleDay'] = $order['saleDay'];
	            $cards[$i]['posNo'] = $order['posNo'];
				$cards_param[$i] = arrange_param($cards[$i],'cards');
			}
        }				

        // 2.4 �����迭�� ������ coupons(�������� ī��� �ܼ��� �´ٰ� �� ����ũ ó��)
        // �迭�� ���� ��� ����(http 500)�� ���� �ϴ� �̴�� ��: is_array�Լ��� ����Ͽ� ������ ����
		for  ($i = 0;$i < count($coupons);$i++) {
		    if (is_array($coupons[$i])) {
				$coupons[$i]['univcode'] = $univcode;
    	        $coupons[$i]['franchiseCd'] = $order['franchiseCd'];
       	        $coupons[$i]['saleDay'] = $order['saleDay'];
	            $coupons[$i]['posNo'] = $order['posNo'];
				$coupons_param[$i] = arrange_param($coupons[$i],'coupons');
			}
        }				

		// 2.5 �߰�) benefits array
    	if (is_array($benefits)) {
		    for ($i = 0;$i < count($benefits);$i++) {
				$benefits[$i]['univcode'] = $univcode;
  			    $benefits[$i]['franchiseCd'] = $order['franchiseCd'];  // franchiseCd(=storecode)�� order������ �����ֳ׿�
  		        $benefits[$i]['saleDay'] = $order['saleDay'];          // saleDay�� options ���׿�
		    	$benefits[$i]['posNo'] = $order['posNo'];              // posNo�� options ���׿�
   		        $benefits[$i]['billNo'] = $order['billNo'];            // billNo�� options ���׿�
                $benefits[$i]['paymentSeq'] = $payments[$i]['paymentSeq']; // paymentSeq�� payments�� �־��
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
		//model�� ���� DB�� Ʈ����� ó���� ���� �ѹ濡 ó��(�� �ι迭 ó����� ���� is_array�� �ذ���.)
		// MS-SQL2019���� �Ϻ� ��񿡼� �Ķ��Ÿ�� ,,, �̷��� ���־ �籸����.
		//foreach($order as $key => $value){if(isset($value)) $order[$key] = '';}
		//$insertDB = $this->API->insertDB($order_param, $Products_params, $options_params, $payments_params, $cards_param, $coupons_param, $benefits_params);
		$insertDB = $this->API->insertDB($order_param, $Products_params, $options_params, $payments_params, $cards_param, $coupons_param, $benefits_params);

        if ($insertDB !== RES_CODE_SUCCESS) {
            $message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "InsertDB ó������!!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'])." eMessage=".json_encode($message['error']['errorMessage']), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
        }
        
		// ���ϴ� ��� �α׸� ���� ��ƾ�ε��� ���߿� �����ؾ� �մϴ�. �����ϰ� �α����ٸ��� for�� ������ �ֽ��ϴ�. �ְ� ���߿� ��Ƽ� �� ����!!
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
            writeLog("[{$sLogFileId}] Options= �����Ͱ� �����ϴ�.", $sLogPath, $bLogable);
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
            writeLog("[{$sLogFileId}] cards= �����Ͱ� �����ϴ�.", $sLogPath, $bLogable);
		}
		// coupons
		if(!empty($coupons_param)) {
            for ($i = 0;$i < count($coupons_param);$i++) {
				if(empty($coupons_param[$i])) $i++;
			    writeLog("[{$sLogFileId}] coupons=" . json_encode(implode( '|', $coupons_param[$i] ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
			}
		} else {
            writeLog("[{$sLogFileId}] coupons= �����Ͱ� �����ϴ�.", $sLogPath, $bLogable);
		}
		// benefits
		if (!empty($benefits_params)) {
    		for ($i = 0;$i < count($benefits_params);$i++) {
			    writeLog("[{$sLogFileId}] benefits[".$i."] = " . json_encode(implode( '|', $benefits_params[$i] ),JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
		    }
		} else {
            writeLog("[{$sLogFileId}] benefits= �����Ͱ� �����ϴ�.", $sLogPath, $bLogable);
		}

        // Ends of Log Write
		writeLog("[{$sLogFileId}] result=" . json_encode($message,JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);

		echo json_encode($message);
        return;
    }
	
	//�ֹ����� receive api
    public function ci_ver() {
        echo CI_VERSION;
	}
}    