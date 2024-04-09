<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
 * $t1 기준시간(없으면 현재시간으로 대체) 
 * $t2 비교시간
 * PHP 5.3 이상. 
 */ 
ini_set('display_errors', 1); // php 에러 표시하기

function passwdEncrypt($pw){
	// md5 + sha1 128 x5
	return sha1(md5(sha1(md5(sha1(md5(sha1(md5(sha1(md5($pw))))))))));
}

function DateDiff($dt1, $dt2) {
	/** 사용법
	echo DateDiff('2010-07-27 12:11:13', '2319-09-11 21:52:05'); // 309년 후
	echo DateDiff('2010-07-27 12:11:13', '2010-07-21 15:38:42'); // 5일 전
	echo DateDiff(null, '2010-06-11 15:38:42'); // (현재시간 기준) 1개월 전
	echo DateDiff(null, '2010-07-27 08:38:42'); // (현재시간 기준) 25분 전
	*/	 
	if(!$dt2) return; 
	//$trans = array('y' => '년', 'm' => '개월', 'd' => '일', 'h' => '시간', 'i' => '분', 's' => '초');
	//$ago = array(' 후', ' 전'); 
	$dt1 = new DateTime($dt1); 
	$dt2 = new DateTime($dt2); 
	$t1 = $dt1->diff($dt2);

	// $to_time = strtotime($dt1);
	// $from_time = strtotime($dt2); 
	// $minutes = round(abs($to_time - $from_time) / 60, 2);

	if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h == 0 && $t1->i <= 1)
		return "방금 전";
	else if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h == 0 && $t1->i <= 60 )
		return $t1->i . "분 전";
	else if($t1->y == 0 && $t1->m == 0 && $t1->d == 0 && $t1->h >= 1)
		return "{$t1->h}시간 전";
	else if($t1->y == 0 && ( $dt1->format('Y') == $dt2->format('Y') ) )
		return $dt2->format('m.d A h:i');
	else
		return $dt2->format('Y.m.d');
} 

// PRINT STREAM
function stripString($string){
	$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
	$string = nl2br($string);
	$string = stripslashes(str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string));
	return $string;
}

function stripString_HTML($string){
	$string = stripslashes(str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $string));
	return $string;
}

// AUTOLINK STREAM
function autoLink($text){
	//$pattern = "/(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$pattern = "/(((http[s]?:\/\/))?(([-a-z0-9]+\.)?[-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$pattern = "/(((http[s]?:\/\/))(([-a-z0-9]+\.)?[-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$text = preg_replace($pattern, " <a href='$1' target='_blank' class='link'>$1</a>", $text);
	$text = preg_replace("/href='www/", "href='http://www", $text);
	return $text;
}

function writeLog($str='', $filepath='' )
{
    /**
    * 기존 common_service/write_log
    */    

    $CI =& get_instance();
    $CI->load->helper('file');
    $sLogPath = (empty($filepath)) ? $_SERVER['DOCUMENT_ROOT'].'/../logs/'.date('Ymd', time()).'.log' : $filepath;
    $sLog = '['.date('Y-m-d H:i:s').'] '.$str."\n";
    
    //log_auto_file_delete($sLogPath);
    return write_file($sLogPath, $sLog, 'a+');

    $fp = fopen("./Log/$date.log", "a");
    fwrite($fp, $time."\t$LogContents\r\n");
    fclose($fp);

	return;

}

function log_auto_file_delete($dir)
{
    if(is_dir($dir)) {
        if($dh = opendir($dir)) {
            while(($entry = readdir($dh)) !== false) {
                if($entry == '.' || $entry == '..')
                    continue;
                $subdir = $dir.'/'.$entry;
                if(is_dir($subdir)) {
                    log_auto_file_delete($subdir);
                } else {
                    if($entry == 'index.php')
                        continue;
                    $sfile = $dir.'/'.$entry;
                    $mtime = @filemtime($sfile);
                    // 최종수정일이 30일 이상인 파일만 삭제
                    if(file_exists($sfile) && (time() - $mtime <= 24*60*60*LOG_FILE_AUTO_DELETE_DATE))
                        continue;
                    // 파일삭제
                    @unlink($sfile);
                }
            }
            closedir($dh);
        }
    }
}

//한글인코딩 변경 euc-kr변경
function convertMSEncoding($str)
{
    $str =  mb_convert_encoding($str,  "CP949" , "UTF-8");
    $str =  mb_convert_encoding($str,  "UTF-8" , "CP949");  
    return  $str;
}

function arrange_param($arr,$arrtype) 
{

	if ($arrtype == "order") {

		$params = array(
            'univcode'               => $arr['univcode'],    // order key  대학코드*          s5
            'saleDay'                => $arr['saleDay'],     // order key  영업일*            s10  YYYY-MM-DD
            'storeCode'              => trim($arr['franchiseCd']), // order key  지점코드            s30  연동처리 ********명칭변경 주의
            'posNo'                  => trim($arr['posNo']), // order key  포스번호*           s5   연동 기기번호
            'billNo'                 => trim($arr['billNo']),// order key  영수번호*           s30
    		'createdAt'              => trim($arr['createdAt']), //            등록일             s30
            'updatedAt'              => trim($arr['updatedAt']), //            수정일             s30
            'headOfficeId'           => trim($arr['headOfficeId']), //            본사id             s30
            'franchiseId'            => trim($arr['franchiseId']), //            가맹점id           s30
            'deviceId'               => trim($arr['deviceId']), //            기기id             s30 
            'deviceSeq'              => $arr['deviceSeq'],   //            기기번호            n
            'channelType'            => trim($arr['channelType']),  //     채널구분            s10  ch01:kiosk
            'outerBillno'            => trim($arr['outerBillno']),  //     외부연동영수번호      s30
            'tradeType'              => trim($arr['tradeType']),    //     거래구분            s2   S:매출 C:취소
            'serviceType'            => trim($arr['serviceType']),  //     매장/포장           s2   S:매장 P:포장
            'salesTarget'            => trim($arr['salesTarget']),  //     서비스대상          s2   G:일반 S:직원
            'totalAmount'            => $arr['totalAmount'], //            총주문금액          n
            'paymentAmount'          => $arr['paymentAmount'],      //     결재금액            n
            'discountAmount'         => $arr['discountAmount'],     //     총할인금액          n
            'couponAmount'           => $arr['couponAmount'],       //     쿠폰금액            n
            'cashableAmount'         => $arr['cashableAmount'],     //     현금화금액          n
            'taxationAmount'         => $arr['taxationAmount'],     //     과세대상금액        n
            'dutyAmount'             => $arr['dutyAmount'],         //     면세금액            n
            'totalTax'               => $arr['totalTax'],           //     부가세액            n
            'tableNo'                => trim($arr['tableNo']),      //     테이블번호          s3
            'orgBillNo'              => trim($arr['orgBillNo']),    //     원거래영수번호      s30  반품건원거래번호
            'orderStatus'            => trim($arr['orderStatus']),  //     주문상태            s5   1001주문중 9999주문취소 1000픽업주문취소 1003주문접수 1005주문확인 2007상품준비중 2009픽업대기 2020픽업완료 2085픽업지연 2090픽업지연완료 2099픽업미완료
            'paymentStatus'          => trim($arr['paymentStatus']),//     결재상태            s2   S성공 F실패(부분) F결재시 부분실패
            'cancelBillNo'           => trim($arr['cancelBillNo']), //     취소영수번호        s30  원거래건의취소영수번호
            'receiptPrintCountType'  => trim($arr['receiptPrintCountType']),// 영수증출력갯수타입  s20
            'exchangePrintCountType' => trim($arr['exchangePrintCountType']),// 교환건출력갯수타입  s20
            //'additionalInfo'         => $arr['additionalInfo'], //         부가정보              JSON형식 사용안함            
            'filler1'                => trim($arr['filler1']), // 비고1 => 조합원번호로 변경(2020/06/17) 123355
            'filler2'                => trim($arr['filler2']),      //      비고2               s500
            'filler3'                => trim($arr['filler3']),      //      비고3               s500
            'filler4'                => trim($arr['filler4']),      //      비고4               s500
            'closeYn'                => trim($arr['closeYn']),      //      마감처리여부        s255
            'closeDate'              => trim($arr['closeDate']),    //      마감일자            s255
            'salesDaySeq'            => $arr['salesDaySeq'], //             영업일자순번        n
        );
		if(isset($arr['additionalInfo']['mediaNo'])) {
			$params['filler1'] = $arr['additionalInfo']['mediaNo'];
		}
        //print_r($params);
		//exit;


    } else if ($arrtype == "products") {
        
        $params = array(
            'univcode'               => $arr['univcode'],    // products key  대학코드*          s5
            'saleDay'                => $arr['saleDay'],     // products key  영업일*            s10  YYYY-MM-DD
            'storeCode'              => trim($arr['franchiseCd']), // products key  지점코드            s30  연동처리 ********명칭변경 주의
            'posNo'                  => trim($arr['posNo']), // products key  포스번호*           s5   연동 기기번호
            'billNo'                 => trim($arr['billNo']), // products key  영수번호*           s30
            'orderProductSeq'        => $arr['orderProductSeq'], // products key 주문상품순번*    n
  	    	'createdAt'              => trim($arr['createdAt']), //            등록일             s30
            'updatedAt'              => trim($arr['updatedAt']), //            수정일             s30
            'channelType'            => trim($arr['channelType']), //      채널구분           s10  ch01:kiosk
            'headOfficeId'           => trim($arr['headOfficeId']), //            본사id             s30
            'franchiseId'            => trim($arr['franchiseId']), //            가맹점id            s30
            'deviceId'               => trim($arr['deviceId']),  //            기기id              s30 
            'tradeType'              => trim($arr['tradeType']), //        거래구분            s2   S:매출 C:취소
            'orderProductType'       => trim($arr['orderProductType']), //주문상품구분        s255 P:영수(payment) C:쿠폰(coupon) F:사은품(free)
            'salesTarget'            => trim($arr['salesTarget']), //      서비스대상          s255 G:일반 S:직원
            'serviceType'            => trim($arr['serviceType']), //      매장/포장           s255 S:매장 P:포장
            'mediaNo'                => trim($arr['mediaNo']), //          미디어번호          s255 쿠폰일경우 쿠폰번호
            'categoryId'             => trim($arr['categoryId']), //       카테고리아이디       s255
            'categoryName'           => trim($arr['categoryName']), //     카테고리아이디       s255
            'categoryMgrName'        => trim($arr['categoryMgrName']), //  카테고리관리명       s255
            'categoryExtrCd'         => trim($arr['categoryExtrCd']), //   카테고리외부연동코드  s20
            'productId'              => trim($arr['productId']), //        상품아이디           s255
            'productName'            => trim($arr['productName']), //      상품명               s255
            'productMgrName'         => trim($arr['productMgrName']), //   상품관리명           s255
            'extrCd'                 => trim($arr['extrCd']), //           상품외부연동코드      s20
            'extr2Cd'                => trim($arr['extr2Cd']), //          상품외부연동코드2     s20
            'extr3Cd'                => trim($arr['extr3Cd']), //          상품외부연동코드3     s20
            'primeCost'              => $arr['primeCost'],     //          상품원가              n
            'price'                  => $arr['price'],         //          상품단가              n
            'taxAmount'              => $arr['taxAmount'],     //          부가세                n
            'useTax'                 => $arr['useTax'],        //          과세상품여부           b
            'baseSaleQty'            => $arr['baseSaleQty'],   //          기본수량              n
            'productQty'             => $arr['productQty'],    //          주문상품수량           n
            'amount'                 => $arr['amount'],        //          합계금액              n
            'orgBillNo'              => trim($arr['orgBillNo']), //        원거래영수번호        s255
            //'itrPrinterAlias'        => $arr['itrPrinterAlias'], //        내부프린트정보        o 미사용
            //'etrPrinterAlias'        => $arr['etrPrinterAlias'], //        외부프린트정보        o 미사용
            'productPrintName'       => trim($arr['productPrintName']), // 주방출력상품명        s255 있을때만 출력
            'outputQty'              => $arr['outputQty'],     //          티켓출력수량          n
            'salesDaySeq'            => $arr['salesDaySeq'],   //          영업일자순번          n
            //'additionalInfo'         => $arr['additionalInfo'], //         부가정보              JSON형식 미사용
            'filler1'                => trim($arr['filler1']), //          비고1                s500
            'filler2'                => trim($arr['filler2']), //          비고2                s500
            'filler3'                => trim($arr['filler3']), //          비고3                s500
            'filler4'                => trim($arr['filler4']), //          비고4                s500
            );

	} else if ($arrtype == "options") {

        $params = array(
            'univcode'               => $arr['univcode'],    // Options key  대학코드*          s5
            'saleDay'                => $arr['saleDay'],     // Options key  영업일*            s10  YYYY-MM-DD
            'storeCode'              => trim($arr['franchiseCd']), // Options key  지점코드            s30  연동처리 ********명칭변경 주의
            'posNo'                  => trim($arr['posNo']), // Options key  포스번호*           s5   연동 기기번호
            'billNo'                 => trim($arr['billNo']), // Options key  영수번호*           s30
            'orderProductOptionSeq'  => $arr['orderProductOptionSeq'],// Options key  주문옵션순번* n
            'createdAt'              => trim($arr['createdAt']), //              등록일              s30
            'updatedAt'              => trim($arr['updatedAt']), //              수정일              s30
            'headOfficeId'           => trim($arr['headOfficeId']), //              본사id              s30
            'franchiseId'            => trim($arr['franchiseId']), //              가맹점id            s30
            'deviceId'               => trim($arr['deviceId']),  //              기기id              s30
            'orderProductSeq'        => $arr['orderProductSeq'], //          주문상품순번         n
            'optionGroupId'          => trim($arr['optionGroupId']), //      옵션그룹아이디       s255
            'optionGroupExtrCd'      => trim($arr['optionGroupExtrCd']), //  옵션그룹외부연동코드  s20
            'optionGroupName'        => trim($arr['optionGroupName']), //    옵션그룹명           s255
            'optionGroupMgrName'     => trim($arr['optionGroupMgrName']), // 옵션그룹관리명       s255
            'optionId'               => trim($arr['optionId']), //           옵션아이디           s255
            'optionName'             => trim($arr['optionName']), //         옵션명               s255
            'productMgrName'         => trim($arr['productMgrName']), //     옵션관리명           s255
            'optionPrintName'        => trim($arr['optionPrintName']), //    옵션명출력명         s255
            'extrCd'                 => trim($arr['extrCd']), //             외부연결코드1        s20
            'extr2Cd'                => trim($arr['extr2Cd']), //            외부연결코드2        s20
            'extr3Cd'                => trim($arr['extr3Cd']), //            외부연결코드3        s20
            'price'                  => $arr['price'],       //              단가                n
            'primeCost'              => $arr['primeCost'],   //              원가                n
            'taxAmount'              => $arr['taxAmount'],   //              부가세               n
            'useTax'                 => $arr['useTax'],      //              과세여부             s1 1:과세 0:면세
            'baseSaleQty'            => $arr['baseSaleQty'], //              기분수량             n
            'productQty'             => $arr['productQty'],  //              주문수량             n
            'amount'                 => $arr['amount'],      //              주문금액             n
            //'additionalInfo '        => trim($arr['additionalInfo ']), //    부가정보 JSON 사용안함
            'filler1'                => trim($arr['filler1']), //            비고1                s500
            'filler2'                => trim($arr['filler2']), //            비고2                s500
            'filler3'                => trim($arr['filler3']), //            비고3                s500
            'filler4'                => trim($arr['filler4']), //            비고4                s500
        );

    } else if ($arrtype == "payments") {

        $params = array(
            'univcode'               => $arr['univcode'],    // payments key  대학코드*          s5
            'saleDay'                => $arr['saleDay'],     // payments key  영업일*            s10  YYYY-MM-DD
            'storeCode'              => trim($arr['franchiseCd']), // payments key  지점코드            s30  연동처리 ********명칭변경 주의
            'posNo'                  => trim($arr['posNo']), // payments key  포스번호*           s5   연동 기기번호
            'billNo'                 => trim($arr['billNo']), // payments key  영수번호*           s30
            'paymentSeq'             => $arr['paymentSeq'],  // payments key  결재순번*           n
            'orgRegiDateTime'        => trim($arr['orgRegiDateTime']), //     원거래등록일        s30
            'createdAt'              => trim($arr['createdAt']), //               등록일             s30
            'updatedAt'              => trim($arr['updatedAt']), //               수정일             s30
            'headOfficeId'           => trim($arr['headOfficeId']), //               본사id             s30
            'franchiseId'            => trim($arr['franchiseId']), //               가맹점id           s30
            'deviceId'               => trim($arr['deviceId']), //               기기id             s30 
            'channelType'            => trim($arr['channelType']), //         채널구분           s255 ch01:kiosk
            'paymentPlatform'        => trim($arr['paymentPlatform']), //     결재플랫폼          s255 결재플랫폼 KIOSK, PAY:간편결재
            'paymentMethod'          => trim($arr['paymentMethod']), //       결재방법            s255 CASH:현금 CASH_RECEIPT:현금영수증 CARD:카드 COUPON:쿠폰 POINT-USE:사용포인트 POINT-SAVE:적립포인트 PAYCO:페이코 KAKAOPAY:카카오페이
            'tradeType'              => trim($arr['tradeType']), //           거래타입            s255
            'moduleId'               => trim($arr['moduleId']), //            모듈아이디          s255
            'payAmount'              => $arr['payAmount'],    //              결재금액            n
            'dutyAmount'             => $arr['dutyAmount'],  //               면세금액            n
            'supplyAmount'           => $arr['supplyAmount'], //              공급가액            n
            'taxAmount'              => $arr['taxAmount'],   //               부가세              n
            'mediaType'              => trim($arr['mediaType']), //           현금영수증매체타입   s255 INDIVIDUAL:개인 CORPORATION:법인
            'mediaNo'                => trim($arr['mediaNo']), //             현금영수증매체번호   s255 (주민번호,핸드폰번호,사업자번호,카드번호)
            'appNo'                  => trim($arr['appNo']), //               승인번호            s255
            'appDate'                => trim($arr['appDate']), //             승인일자            s255
            'orgPaymentSeq'          => $arr['orgPaymentSeq'], //             원거래결재순번       n
            'orgAppNo'               => trim($arr['orgAppNo']), //            원거래승인번호       s255
            'orgAppDate'             => trim($arr['orgAppDate']), //          원거래승인일자       s255
            'orgBillNo'              => trim($arr['orgBillNo']), //           원거래영수번호       s255
            //'approvalInfo'           => trim($arr['approvalInfo']), //        승인정보             o 사용안함
            'paymentStatus'          => trim($arr['paymentStatus']), //       결재상태             s255 S:성공 F:실패
            'salesDaySeq'            => $arr['salesDaySeq'], //               영업일자순번          n
            'cashableAmount'         => $arr['cashableAmount'], //            현금영수증가용금액    n
            'cashInSeq'              => $arr['cashInSeq'],   //               현금투입금순번        n
            'cashOutSeq'             => $arr['cashOutSeq'],  //               현금방출금순번        n
            'closeYn'                => trim($arr['closeYn']), //             마감여부              s255
            'closeDate'              => trim($arr['closeDate']), //           마감일시              s255
            'errorMessage'           => trim($arr['errorMessage']), //        에러메시지            t
            'filler1'                => trim($arr['filler1']),      //      비고1               s500
            'filler2'                => trim($arr['filler2']),      //      비고2               s500
            'filler3'                => trim($arr['filler3']),      //      비고3               s500
            'filler4'                => trim($arr['filler4']),      //      비고4               s500
        );

    } else if ($arrtype == "cards") {

		$params = array(
            'univcode'               => $arr['univcode'],    // card key  대학코드*            s5
            'saleDay'                => $arr['saleDay'],     // card key  영업일*              s10  YYYY-MM-DD
            'storeCode'              => trim($arr['franchiseCd']), // card key  지점코드             s30  연동처리 ********명칭변경 주의
            'posNo'                  => trim($arr['posNo']), // card key  포스번호*            s5   연동 기기번호
            'billNo'                 => trim($arr['billNo']), // card key  영수번호*            s30
            'paymentSeq'             => $arr['paymentSeq'],  // card key  결재순번*            n
    		'createdAt'              => trim($arr['createdAt']), //            등록일              s30
            'updatedAt'              => trim($arr['updatedAt']), //            수정일              s30
            'vanCd'                  => trim($arr['vanCd']), //            밴코드              s255
            'issueCd'                => trim($arr['issueCd']), //          발급사코드          s255
            'issueName'              => trim($arr['issueName']), //        발급사명            s255
            'acquirerCd'             => trim($arr['acquirerCd']), //       매입사코드          s255            
            'acquirerName'           => trim($arr['acquirerName']), //     매입사명            s255
            'storeNo'                => trim($arr['storeNo']), //          가맹점번호          s255
            'installment'            => trim($arr['installment']), //      할부개월            s255
            'cardNo'                 => trim($arr['cardNo']), //           카드번호            s255
            //'additionalInfo'         => trim($arr['additionalInfo']) //    부가정보            JSON 사용안함
            'filler1'                => trim($arr['filler1']), //          비고1               s500
            'filler2'                => trim($arr['filler2']), //          비고2               s500
            'filler3'                => trim($arr['filler3']), //          비고3               s500
        );

    } else if ($arrtype == "coupons") {

        $params = array(
            'univcode'               => $arr['univcode'],           // coupon key  대학코드*          s5
            'saleDay'                => $arr['saleDay'],            // coupon key  영업일*            s10  YYYY-MM-DD
            'storeCode'              => trim($arr['franchiseCd']),  // coupon key  지점코드           s30  연동처리 ********명칭변경 주의
            'posNo'                  => trim($arr['posNo']),        // coupon key  포스번호*          s5   연동 기기번호
            'billNo'                 => trim($arr['billNo']),       // coupon key  영수번호*          s30
            'paymentSeq'             => $arr['paymentSeq'],         // coupon key  결재순번*          n
    		'createdAt'              => trim($arr['createdAt']),    //             등록일              s30
            'updatedAt'              => trim($arr['updatedAt']),    //             수정일              s30
            'affiliateCd'            => trim($arr['affiliateCd']),  //             제휴사코드          s255 OMNITEL:옵니텔 PAYCO:페이코 ZLGOON:즐거운 PAYS:페이즈 OKCASHBAG:오케이캐쉬백 TOUCHING:터칭포인트
            'couponType'             => trim($arr['couponType']),   //             쿠폰구분            s255 EXCHANGE:교환권 AMOUNT:금액권 POINT:포인트(SK,OKCASHBAK,단골,터칭)
            'couponExplanation'      => trim($arr['couponExplanation']), //        쿠폰설명            s255
            'mediaNo'                => trim($arr['mediaNo']),      //             인증코드            s255
            'usePoint'               => $arr['usePoint'],           //             사용포인트           n
            'occurPoint'             => $arr['occurPoint'],         //             발생포인트           n
            'remainPoint'            => $arr['remainPoint'],        //             남은포인트           n
            'availablePoint'         => $arr['availablePoint'],     //             유효포인트           n
            //'additionalInfo'         => trim($arr['additionalInfo']), //           부가정보             JSON 사용안함
            'filler1'                => trim($arr['filler1']),      //             비고1               s500
            'filler2'                => trim($arr['filler2']),      //             비고2               s500
            'filler3'                => trim($arr['filler3']),      //             비고3               s500
        );

	} else if ($arrtype == "benefits") {
        
        if (isset($arr['approvalInfo']['cpt_amount'])) {
			$cpt_amount = $arr['approvalInfo']['cpt_amount'];         // 적립총액
		} else {
			$cpt_amount = 0;
		}

		//if (substr($arr['mediaNo'],1,3) == '700' && $cpt_amount != 0) { // 적립총액이 0이 아니고 조합원번호의 첫3자리가 700인 경우만
    	$params = array(
            'univcode'               => $arr['univcode'],           // benefits key  대학코드*          s5
            'saleDay'                => $arr['saleDay'],            // benefits key  영업일*            s10  YYYY-MM-DD
            'storeCode'              => trim($arr['franchiseCd']),  // benefits key  지점코드           s30  연동처리 ********명칭변경 주의
            'posNo'                  => trim($arr['posNo']),        // benefits key  포스번호*          s5   연동 기기번호
            'billNo'                 => trim($arr['billNo']),       // benefits key  영수번호*          s30
            'paymentSeq'             => $arr['paymentSeq'],         // benefits key  결재순번*          n
    	    'createdAt'              => trim($arr['createdAt']),    //               등록일             s30
            'updatedAt'              => trim($arr['updatedAt']),    //               수정일             s30
            'moduleId'               => $arr['moduleId'],           //               포인트적립모듈
            'CPT_MEMBER'             => $arr['mediaNo'],            //               조합원번호
		    'CPT_AMOUNT'             => $cpt_amount,                //               적립가능 상품의 총금액
    	);
		//} else {
		//	$params = '';
		//}

	}
    return $params;
}


function js_location($val)
{
    if ($val < 0) {
        echo "<script language='javascript'>history.go(${val});</script>";
    } else if ($val == 'close') {
        echo "<script language='javascript'>self.close();</script>";
    } else if ($val == "parent") {
        echo "<script language='javascript'>opener.location.reload();self.close();</script>";
    } else if ($val == "reload") {
        echo "<script language='javascript'>location.reload();</script>";
    } else {
        echo "<script language='javascript'>location.replace ('$val');</script>";
    }
    exit;
}

function js_alert($val)
{
    echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
    echo "<script language='javascript'>alert ('$val');</script>";
}

function js_alert_location($val, $val2)
{
    js_alert($val);
    js_location($val2);
    exit;
}

// 팝업창 닫고 부모창을 이동시김
function js_alert_opener_location($val, $val2)
{
    js_alert($val);
    echo "<script language='javascript'>opener.location.replace ('$val2');self.close();</script>";
    exit;
}

// 팝업창 닫고 부모창에 값전달  $val2 인자로 FrmUserInfo.ID.value=''는 opener의 ID값을 ''로 넘겨준다.
function js_alert_opener_valuesend($val, $val2)
{
    js_alert($val);
    echo "<script language='javascript'>opener.${val2};self.close();</script>";
    exit;
}

// 로그인시 IFrame에 이용

function js_iframe_alert($val, $val2)
{
    js_alert($val);
    echo "<script language='javascript'>top.document.${val2}.ID.value= '' ; top.document.${val2}.PWD.value= '' ; top.document.${val2}.ID.focus();</script>";
    exit;
}

function js_iframe_location($val)
{
    echo "<script language='javascript'>top.window.location.href='$val';</script>";
    exit;
}

function close_kakao_layer()
{
    echo "<script language='javascript'>
            var _ua = window.navigator.userAgent || window.navigator.vendor || window.opera;
            if (_ua.toLocaleLowerCase().includes('kakaotalk')) {
                window.location.href = (/iPad|iPhone|iPod/.test(_ua)) ? 'kakaoweb://closeBrowser' : 'kakaotalk://inappbrowser/close';
            } else {
                window.close();
            }
         </script>";
    exit;
}

function js_alert_kakao_close($val)
{
    js_alert($val);
    close_kakao_layer();
    exit;
}


function dateConvert($Str,$type){
	$TStr = "";
	if($type == "Ymd"){
		$TStr = substr($Str,0,4) . "-" . substr($Str,4,2) . "-".substr($Str,6,2) ;
	}else if($type == "YmdHis"){
		$TStr = substr($Str,0,4) . "-" . substr($Str,4,2) . "-".substr($Str,6,2) . " " .substr($Str,8,2) . ":" .substr($Str,10,2)  . ":" .substr($Str,12,2) ;
	}else if($type == "YmdHi"){
		$TStr = substr($Str,0,4) . "-" . substr($Str,4,2) . "-".substr($Str,6,2) . " " .substr($Str,8,2) . ":" .substr($Str,10,2)   ;
	}

	return $TStr;
}