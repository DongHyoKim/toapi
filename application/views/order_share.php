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

    <title>상품권/선불카드 배포</title>

</head>
<body>
<div class="wrap order_share">
    <section class="flex-column one my-0">
        <div class="content p-0">
            <div class="content_wrap">
                <div class="text-center qr_box">
                    <div class="h5 pt-4  font-weight-bold">
                        2022 신입생 생협상품권
                    </div>
					<div class="font-weight-bold text_green">\ 2,000</div>

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
                            <td>2022-03-02 ~ 2023-01-31</td>
                        </tr>
                        <tr>
                            <th>- 발 행 일</th>
                            <td>2022-01-26 (00003)</td>
                        </tr>
						<tr>
                            <th>- 발 행 자</th>
                            <td>이화여자대학교 인권센터</td>
                        </tr>
                        
                        </tbody>
                    </table>
                </div>


                    
                <hr class="w-80 my-4" />

<div class="font-weight text_green">생협상품권 이용 안내</div>
<div class="p-2 text-left pt-4  text_green small">1. 이 상품권은 1회에 한하여 이용 가능하며, 이용한 상품권은 회수합니다.<br/>
2. 유효기간이 만료한 상품권은 이용이 불가능합니다.<br/>
(유효기간 경과 후 미사용 상품권 가액은 생협 장학금으로 전환됨.)<br/>
3. 이 상품권 가액의 80%이상 구입시 잔액을 현금으로 지급받을 수 있습니다.<br/>
4. 이 상품권 가액을 초과하는 물품 구입시 초과분은 현금 부담하요야 합니다.<br/>
5. 이 상품권으로 구매한 상품을 반품할 경우 반드시 영수증을 지참하여야 합니다.<br/>
6. 바코드와 발행번호가 손상되면 사용하기 어려우니 주의하시기 바랍니다.</div>
               

				
				<div class="font-weight text_green">생협상품권 이용처</div>
<div class="p-2 text-left pt-4  text_green small">
학생문화관종합매장 | 학생문화관제과점 | 체육관매점 | 조형관매점 | 음악관매점 | 헬렌관매점 | 동창회관매점 |
도서관매점 | 과학관매점 | 공학관매점 | 목동의대매점 | 학관매점 | 법학관매점 | 교육관매점 | 경영관매점
대학원기숙사매점 | 마곡의대매점 | 나눔가게 | ECC이화기념품점 | 이화웰컴센터기념품점

</div>
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
makeCode('5113922012600027');
makeBarCode('5113922012600027');

$(function(){
	$('#qrcode img').css('margin','0 auto');
	$('#barcodeTarget').css('margin','0 auto');
	$('#barcodeTarget div').last().css('font-size','14px');
	
});
</script>
</body>
</html>