<?php if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Jnu_book extends CT_Controller {

    private $json_array = array();

	public function __construct(){
		parent::__construct();
		$this->load->model('Jnu_book_model','API');
		//print_r($this);
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
        header("Content-type:text/html;charset=utf-8");
    }

    public function index(){
    }

    public function bookSearch(){

    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../../logs/jnubook/' . date('Ymd') . '_bookSearch.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../../logs/jnubook/' . date('Ymd') . '_bookSearch.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );
        
        $rows_per_page = 10;                // 한페이지당 출력row수
        $dbName        = "BOOKCODE";        // DB명
        $spName_org    = "RP_BOOKMETAWEBPAGE";    // SP명 내역조회
        $spName_cnt    = "RP_BOOKMETACOUNT";  // SP명 건수조회

        //$UNIVCODE      = $this->input->post('UNIVCODE',true);           // 대학코드 00116

		
		$UNIVCODE      = $_REQUEST['UNIVCODE'];           // 대학코드 00116
		$STORECODE     = $_REQUEST['STORECODE'];          // 매장코드 6000100 광주서점 / 6000200 여수서점
		$ISBN          = $_REQUEST['ISBN'];               // ISBN코드
        $BOOKNAME      = $_REQUEST['BOOKNAME'];           // 도서명
		$AUTHORNAME    = $_REQUEST['AUTHORNAME'];         // 저자명
        $KEYWORD       = $_REQUEST['KEYWORD'];            // 복합검색여부
        //$PAGE          = $_REQUEST['PAGE_NO'];          // 현재페이지
		$PAGE          = $_REQUEST['PAGE'];               // 현재페이지
		if(empty($PAGE)) $PAGE = 1 ;
		
		$TOTAL         = 0;                               // 총페이지수

        writeLog("[{$sLogFileId}] -------------------------------- START --------------------------------", $sLogPath, $bLogable);

        if(empty($UNIVCODE)){
			$message['rCode'] = "0001";
            $message['error']['errorCode'] = "0001";
            $message['error']['errorMessage'] = "대학코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		
		if(empty($STORECODE)){
			$message['rCode'] = "0002";
            $message['error']['errorCode'] = "0002";
            $message['error']['errorMessage'] = "서점코드가 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}

		if($KEYWORD != "ALL" && empty($ISBN) && empty($BOOKNAME) && empty($AUTHORNAME)){
			$message['rCode'] = "0003";
            $message['error']['errorCode'] = "0003";
            $message['error']['errorMessage'] = "검색조건(ISBN/도서명/저자)이 없습니다!";
            writeLog("[{$sLogFileId}] eCode=".json_encode($message['error']['errorCode'],JSON_UNESCAPED_UNICODE)." eMessage=".json_encode($message['error']['errorMessage'],JSON_UNESCAPED_UNICODE), $sLogPath, $bLogable);
            echo json_encode($message);
            exit;
		}
		
		if(empty($TOTAL) || $TOTAL == 0){
		    $spName = $spName_cnt;  // count of Total
            $params = [
				'UNIVCODE'       => $UNIVCODE,
			    'STORECODE'      => $STORECODE,
			    'ISBN'           => $ISBN,
			    'BOOKNAME'       => convertMSEncoding($BOOKNAME),
			    //'BOOKNAME'       => $BOOKNAME,
                'AUTHORNAME'     => convertMSEncoding($AUTHORNAME),
                //'AUTHORNAME'     => $AUTHORNAME,
		    ];
			writeLog("[{$sLogFileId}] count params=".json_encode($params,JSON_UNESCAPED_UNICODE));
			$TOTAL = $this->API->countbook($UNIVCODE, $dbName, $spName, $params);
			writeLog("[{$sLogFileId}] TOTAL=".json_encode($TOTAL,JSON_UNESCAPED_UNICODE));

			//$PAGE = (int)($TOTAL / $rows_per_page);
			//echo $TOTAL;
			//exit;
		}

        if(!$PAGE) $PAGE = 1;
		$spName = $spName_org;  // 조회rp
		$params['PAGE_ROW'] = $rows_per_page;
        $params['PAGE_NO']  = $PAGE;
        writeLog("[{$sLogFileId}] select params=".json_encode($params,JSON_UNESCAPED_UNICODE));
		//print_r($params);
        //exit;

   	    $tmp_results = $this->API->selectbook($UNIVCODE, $dbName, $spName, $params);

        //$tmp_results = convertMSEncoding($tmp_results);
		//$tmp_results = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "",  $tmp_results);
		//$tmp_results = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "",  $tmp_results);

		$results = [
			'TOTAL' => $TOTAL,
			'PAGE'  => $PAGE,
			'DATA'  => $tmp_results,
		];
        writeLog("[{$sLogFileId}] select=".json_encode($tmp_results,JSON_UNESCAPED_UNICODE));
		//print_r($results);
		//exit;

        writeLog("[{$sLogFileId}] -------------------------------- END --------------------------------", $sLogPath, $bLogable);
		
		echo json_encode($results,JSON_UNESCAPED_UNICODE);
		//echo json_encode($results);
		//echo $results;
		//return results;
	
	}

    public function bookSearch2(){

    	$logs = array(
            'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
            'sLogPath'      => BASEPATH . '../../logs/jnubook/' . date('Ymd') . '_bookSearch.log',
            'bLogable'      => true
        );

        $sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
        $sLogPath    = BASEPATH . '../../logs/jnubook/' . date('Ymd') . '_bookSearch.log';
        $bLogable    = true;

        $message = array(
            'rCode' => RES_CODE_SUCCESS,
            'error' => array (  'errorCode'     => null,
                                'errorMessage'  => null, ),
        );

        $results = [
		    'TOTAL'=>'11',   // 전체 갯수
            'PAGE'=>'1',     // 현재 페이지수
            'DATA' => [      // 복수개의 데이터
		        [ 
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
				],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
				[
			        'BOOKCODE'    => '0000004864509',
			        'ISBNCODE'    => '0000004864509',
                    'BOOKNAME'    => '최신조선용어사전',
		 	        'AUTHORNAME'  => '해문도서편찬회',
			        'PUBLISHNAME' => '잘해냄출판사',
			        'SALEPRICE'   => '30000',
			        'STOCKQTY'    => '3',
			    ],
			]
		];

		echo json_encode($results,JSON_UNESCAPED_UNICODE);
	
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