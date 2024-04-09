<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function sendAlimtalk($req)
{

    try {

        $row = [];
		$return = [];
		
        // The data to send to the API
		if(!empty($req['button'])){
			$postData = [[
				'senderKey' => $req['sender_key'],
				'custMsgSn' => $req['custMsgSn'],
				'phoneNum' => $req['phone'],
				'templateCode' => $req['template_code'],
				'message' => $req['msg'],
				'button' => [$req['button']],
				'smsSndNum'=> $req['sms_number'],
				'smsKind' => 'L',
				'lmsMessage' => $req['alimtalk_fallback_sms_msg'],
			]];

		}else{
			$postData = [[
				'senderKey' => $req['sender_key'],
				'custMsgSn' => $req['custMsgSn'],
				'phoneNum' => $req['phone'],
				'templateCode' => $req['template_code'],
				'message' => $req['msg'],
				'smsSndNum'=> $req['sms_number'],
				'smsKind' => 'L',
				'lmsMessage' => $req['alimtalk_fallback_sms_msg'],
				
			]];

		}
		
		//echo json_encode($postData);
		//exit;

		/*
        if(!empty($req['btn'][0])){

            if(count($req['btn'])>=2){
                $btn_arr[0] = $req['btn'][0];
                $btn_arr[1] = $req['btn'][1];
            }else{
                $btn_arr[0] = $req['btn'][0];
            }
            $postData[0]['button'] = $btn_arr;
        }
		*/


        // Setup cURL
		$url = "https://bzm-api.carrym.com:8443/v3/A/{$req['alimtalk_id']}/messages";		
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Content-type: application/json; charset=utf-8',
				'Authorization: Bearer ' . $req['alimtalk_key']
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));

        // Send the request
        $response = curl_exec($ch);
		//print_r($response);
		//exit;
        // Check for errors
        if ($response === FALSE) {
            $responseData["code"] = 0;
            $responseData["sn"] = "";
            $responseData["altCode"] = "";
            $row["msg"] = curl_error($ch);
            //die(curl_error($ch));
        } else {
            // Decode the response
            $responseData = json_decode($response, TRUE);
        }

        $resultStatus = ($responseData[0]["altCode"] == 0000) ? 'success' : 'fail';

        $sendResult = [
            'mtype' => 'alimtalk'
            , 'result_status' => (string)$resultStatus
            , 'result_code' => (string)$responseData[0]["code"]
			, 'result_alt_code' => (string)$responseData[0]["altCode"]
            , 'result' => (string)$responseData[0]["sn"]
			, 'altMsg' => (string)$responseData[0]["altMsg"]
				
        ];

        $return['code'] = 0;
        $return['msg'] = '';

        $return['data'] = $sendResult;
    } catch (Exception $e) {
        $return['code'] = $e->getCode();
        $return['msg']  = $e->getMessage();
    } finally {
        return $return;
    }
}


//��ȣȭ ��Ʈ��
function get_random_string($len = 6, $type = '') {

    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numeric = '0123456789';
    $special = '`~!@#$%^&*()-_=+\\|[{]};:\'",<.>/?';
    $key = '';
    $token = '';
    if ($type == '') {
        $key = $lowercase.$uppercase.$numeric;
    } else {
        if (strpos($type,'09') > -1) $key .= $numeric;
        if (strpos($type,'az') > -1) $key .= $lowercase;
        if (strpos($type,'AZ') > -1) $key .= $uppercase;
        if (strpos($type,'$') > -1) $key .= $special;
    }

    for ($i = 0; $i < $len; $i++) {
        $token .= $key[mt_rand(0, strlen($key) - 1)];
    }
    return $token;
}

//��뿹
/*
echo '�⺻ : ' . get_random_string() . '<br />';
echo '���ڸ� : ' . get_random_string('09') . '<br />';
echo '���ڸ� 30���� : ' . get_random_string('09', 30) . '<br />';
echo '�ҹ��ڸ� : ' . get_random_string('az') . '<br />';
echo '�빮�ڸ� : ' . get_random_string('AZ') . '<br />';
echo '�ҹ���+�빮�� : ' . get_random_string('azAZ') . '<br />';
echo '�ҹ���+���� : ' . get_random_string('az09') . '<br />';
echo '�빮��+���� : ' . get_random_string('AZ09') . '<br />';
echo '�ҹ���+�빮��+���� : ' . get_random_string('azAZ09') . '<br />';
echo 'Ư�����ڸ� : ' . get_random_string('$') . '<br />';
echo '����+Ư������ : ' . get_random_string('09$') . '<br />';
echo '�ҹ���+Ư������ : ' . get_random_string('az$') . '<br />';
echo '�빮��+Ư������ : ' . get_random_string('AZ$') . '<br />';
echo '�ҹ���+�빮��+Ư������ : ' . get_random_string('azAZ$') . '<br />';
echo '�ҹ���+�빮��+����+Ư������ : ' . get_random_string('azAZ09$') . '<br />';
*/
// echo " get_random_string : " . get_random_string(8,'az09');