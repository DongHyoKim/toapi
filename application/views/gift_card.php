<!doctype html>
<html lang="ko">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/static/assets/css/lib/bootstrap/bootstrap.min.css">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="/static/assets/fonts/fontawesome/css/font-awesome.min.css">

    <!-- custom CSS -->
    <link rel="stylesheet" href="/static/assets/css/front.css">

	<script src="/static/lib/jquery/jquery-3.4.1.js"></script>
	<script type="text/javascript" src="/static/lib/qrcode/qrcode.min.js"></script>
	<script type="text/javascript" src="/static/lib/barcode/jquery-barcode.min.js"></script>

    <title>생협 상품권  - <?=$card_name?></title>

</head>
<body>
<div class="wrap order_share">
    <section class="flex-column one my-0">
        <div class="content p-0">
            <div class="content_wrap">
                <div class="text-center qr_box">
                    <div class="h5 pt-4  font-weight-bold">
                        <?=$card_name?>
                    </div>
					<div class="font-weight-bold text_green">￦ <?=number_format($balance_amt)?></div>

                    <div id="qrcode" class="text-center" style="margin: 0 50px"></div>
                    
                </div>
                <hr class="w-80 my-4" />
				
				<div class="text-center qr_box">
                   
                    <div id="barcodeTarget" class="font-weight-bold "  style=" margin-top:15px;"></div>
					
					<hr class="w-80 my-4" />
					
                <div class="order_detail px-3">

                    <table class="w-100">
                        <tbody>
                        <tr>
                            <th>- 유효기간</th>
                            <td><?=dateConvert($start_date,'Ymd')?> ~ <?=dateConvert($end_date,'Ymd')?></td>
                        </tr>
                        <tr>
                            <th>- 발 행 일</th>
                            <td><?=dateConvert($issue_date,'Ymd')?></td>
                        </tr>
						<tr>
                            <th>- 발 행 자</th>
                            <td><?=$remark2?></td>
                        </tr>
                        
                        </tbody>
                    </table>
                </div>


                    
                <hr class="w-80 my-4" />
<? if($univcode == "00113"){ //이화여대?>
<div class="font-weight text_green">이화상점상품권 이용 안내</div>
<div class="p-2 text-left pt-4  text_green small">
1. 이 상품권은 이화상점 상품권이며, 모바일상품권으로 발행합니다.<br/>
2. 이 상품권은 잔액 소멸시까지 여러번 사용이 가능하며, 사용한 상품권은 회수하지 않습니다.<br/>
3. 이 상품권은 금액을 초과하여 물품 구입이 가능하며, 초과분은 현금, 신용카드 등 원하시는 방법으로 결제 가능합니다.<br/>
4. 유효기간이 만료된 상품권은 사용이 불가능하오니 주의하시기 바랍니다.<br/>
   (유효기간 경과 후 미사용 상품권 금액은 장학금 등으로 전환됩니다.)<br/>
5. 이 상품권으로 구매한 상품을 반품할 경우 반드시 영수증을 지참하여야 합니다.<br/>
</div>
<? } elseif($univcode == "00123"){ //충남대학교?>
<? } elseif($univcode == "00116"){ //전남대학교?>
<div class="font-weight text_green">생협상품권 이용 안내</div>
<div class="p-2 text-left pt-4 text_green small">

    <span style="color: red;"><strong>1. 쿱스캣 매장 이용시에는 무인셀프로는 사용이 불가 합니다. 직원이 근무중일때 이용해 주세요.</strong></span><br/>
    2. 이 상품권은 1회에 한하여 이용 가능하며, 이용한 상품권은 회수합니다.<br/>
    3. 이 상품권 가액의 70% 이상 구입시 차액을 현금으로 지급 받을수 있습니다.<br/>
    4. 이 상품권 가액을 초과하는 물품 구입시 초과분은 현금으로 결제 하여야 합니다.<br/>
    5. 이 상품권으로 구매한 상품을 반품할 경우 반드시 영수증을 지참하여야 합니다.<br/>
    6. 바코드와 발행번호가 훼손되면 사용하실 수 없으므로 주의 하시기 바랍니다.<br/>

<? } ?>                  

<? if($univcode == "00113"){ //이화여대?>				
				<div class="font-weight text_green">이용처</div>
<div class="p-2 text-left pt-4  text_green small">
학생문화관카페, 학관카페, ECC기념품점, ECC문구점, 이화인의나눔가게, 온라인기념품점
</div>
<? } elseif($univcode == "00123"){ //충남대학교?>
<? } elseif($univcode == "00116"){ //전남대학교?>
    				<div class="font-weight text_green">생협상품권 이용처(광주 여수 생협 전 매장)</div>
<div class="p-2 text-left pt-4  text_green small">
1생쿱스켓,2생쿱스켓,공대쿱스켓,도서관쿱스켓,1생문구점,1생서점,카페지젤,아띠끄2023
여수종합매장,여수청경쿱스켓,카페청경
<? } ?> 

<? if($univcode == "00113"){ //이화여대?>
  <div class="font-weight text_green">문의처</div>
<div class="p-2 text-left pt-4  text_green small">
사무실: 알프스관 지하1층, 02-3277-3284 / 3625<br/>
이메일: ewhaisu@ewhaisu.co.kr<br/>
온라인기념품점: www.ewhastore.co.kr<br/>
인스타: @ewhastoresouvener<br/>
</div>
<? } elseif($univcode == "00123"){ //충남대학교?>
<? } elseif($univcode == "00116"){ //전남대학교?>
      <div class="font-weight text_green">문의처</div>
<div class="p-2 text-left pt-4  text_green small">
생협사무실: 용봉문화관 4층  062-530-1092, 1094, 1095 홈페이지: coop.jnu.ac.kr
</div>
<? } ?> 

                </div>
            </div>
        </div>
    </section>
</div><!-- /.wrap -->

<script type="text/javascript">
var qrcode = new QRCode(document.getElementById("qrcode"), {
	width : 100,
	height : 100
});
function makeCode (str) {			
	qrcode.makeCode(str);
}

 function makeBarCode(str){
        var value = str;
        var btype = 'code128';
        var renderer = 'css';
        
		
        var settings = {
          output:renderer,
          bgColor: '#FFFFFF',
          color: '#000000',
          barWidth: '2',
          barHeight: '50',
          moduleSize: '5',
          posX: '10',
          posY: '20',
          addQuietZone: '1'
        };
        
		$("#barcodeTarget").barcode(value, btype, settings);
		
      }
makeCode('<?=$card_no?>');
makeBarCode('<?=$card_no?>');

$(function(){
	$('#qrcode img').css('margin','0 auto');
	$('#barcodeTarget').css('margin','0 auto');
	$('#barcodeTarget div').last().css('font-size','14px');
	
});
</script>
</body>
</html>