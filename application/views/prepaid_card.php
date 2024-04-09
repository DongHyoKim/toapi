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

    <title>생협 선불카드  - <?=$card_name?></title>

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
                            <th>- 발 행 일</th>
                            <td><?=dateConvert($issue_date,'Ymd')?></td>
                        </tr>						
                        
                        </tbody>
                    </table>
                </div>


                    
                <hr class="w-80 my-4" />
<? if($univcode == "00113"){ //이화여대?>
<div class="font-weight text_green">생협선불카드 이용 안내</div>
<div class="p-2 text-left pt-4  text_green small">
1. 이 카드는 이화여자대학교 생활협동조합 선불카드이며, 모바일바코드로 발행합니다.<br/>
2. 이 카드는 잔액 소멸시까지 여러번 사용이 가능하며, 충전 가능한 선불카드입니다. <br/>
   [충전장소: 생협사무실]<br/>
3. 이 카드에 충전된 금액은 환불이 불가능하오니 주의하시기 바랍니다.<br/>
4. 이 카드는 금액을 초과하여 물품 구입이 가능하며, 초과분은 현금, 신용카드 등 원하시는 방법으로 결제 가능합니다.<br/>
5. 이 카드는 유효기간이 없으나 생협에서 정한 기간동안 사용내역이 없고 연락이 되지 않을 경우 사용이 중지될 수 있으니 주의하시기 바랍니다.<br/>
   (사용이 중지되고 생협에서 정한 일정 기간 종료 후 미사용 카드 금액은 생협장학금으로 전환됩니다.)<br/>
6. 이 카드로 구매한 상품을 반품할 경우 반드시 영수증을 지참하여야 합니다.
</div>
<? } elseif($univcode == "00123"){ //충남대학교?>

<div class="font-weight text_green">생협선불카드 이용 안내</div>
<div class="p-2 text-left pt-4  text_green small">
1. 본 카드는 충남대학교 소비자생활협동조합 선불카드입니다.<br/>
2. 본 카드는 충전 가능한 선불카드입니다. <br/>
  [충전장소 : 생협커피점, 생협사무실]<br/>
3. 본 카드에 충전된 금액은 환불 불가합니다.
</div>

<? } elseif($univcode == "00116"){ //전남대학교?>

<div class="font-weight text_green">생협선불카드 이용 안내</div>
<div class="p-2 text-left pt-4  text_green small">

1. 이 카드는 전남대학교 생활협동조합 선불카드입니다.<br/>
2. 이 카드는 충전 가능한 선불카드입니다.<br/>
3. 이 카드에 충전된 금액은 환불이 불가합니다.<br/>
3. 선불카드 훼손시 아래 연락처로 재발행 요청바랍니다.
</div>

<? } ?>              

<? if($univcode == "00113"){ //이화여대?>				
				<div class="font-weight text_green">생협선불카드 이용처</div>
<div class="p-2 text-left pt-4  text_green small">
학생문화관생협매장, 학생문화관제과점, ECC기념품점, 체육관생협매장, 조형관생협매장, 음악관생협매장, 헬렌관생협매장, 동창회관생협매장, 도서관생협매장, 과학관생협매장, 공학관생협매장, 학관생협매장, 법학관생협매장, 교육관생협매장, 경영관생협매장, 마곡의대생협매장, 산학협력관생협매장, 웰컴센터기념품점, 파빌리온기념품점, 이화인의나눔가게

</div>
<? } elseif($univcode == "00123"){ //충남대학교?>
	<div class="font-weight text_green">사용처 안내(생협 구내식당&커피점) </div>
<div class="p-2 text-left pt-4  text_green small">
커피점 : 제1, 2, 3 학생회관, <br/>중앙도서관1층, 생활과학대학1층, 국제교류본부1층<br/>
식  당 : 제2, 3, 4(상록)학생회관, 생활과학대학1층 <br/>

</div>
<? } elseif($univcode == "00116"){ //전남대학교?>
	<div class="font-weight text_green">선불카드 이용처 (광주 여수 생협 전 매장)</div>
<div class="p-2 text-left pt-4  text_green small">
1생쿱스켓,2생쿱스켓,공대쿱스켓,도서관쿱스켓,1생문구점,카페지젤,아띠끄2023
여수종합매장, 카페청경, 여수청경쿱스켓

</div>
<? } ?> 

<? if($univcode == "00113"){ //이화여대?>
  <div class="font-weight text_green">문의처</div>
<div class="p-2 text-left pt-4  text_green small">
생협사무실: 생활환경관 지하1층, 02-3277-3284, 3625<br/>
이메일: ewhacoop@ewha.ac.kr<br/>
생협홈페이지: coop.ewha.ac.kr<br/>
온라인기념품점: ewhagift.ewha.ac.kr<br/>
생협인스타: @ewhacoop  
</div>
<? } elseif($univcode == "00123"){ //충남대학교?>
	  <div class="font-weight text_green">문의처</div>
<div class="p-2 text-left pt-4  text_green small">
생협사무실: 제1학생회관 2층, 042-821-5061
</div>
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