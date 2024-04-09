<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class CT_Controller extends CI_Controller {
	

	public function __construct(){
		parent::__construct();

	}

	function curl($url, $data){
		$post = array( "param" => json_encode($data) );
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_SSL_VERIFYPEER => FALSE,		// 추후 개선 필요
			CURLOPT_USERAGENT => 'CWAY AGENT',
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $post
		));

		$resp = curl_exec($curl);
		if(!curl_exec($curl)){
			$resp = array( "result" => "fail" );
			//die('Error: "' . print_r($curl) . '" - Code: ' . curl_errno($curl));
		}
		curl_close($curl);

		return (object) json_decode($resp);
	}
}