<?php
/*
 http://www.imcore.net | hosihito@gmail.com
 Developer. Kyoungbin Lee
 2012.09.07

 AES256 EnCrypt / DeCrypt
*/
class AESUtil {
	//private $key = 'abcdefghijklmnopqrstuvwxyz123456';
	//private $iv = str_repeat(chr(0), 16);
	private $key = 'qpwoeirutya;sldkfjghz/x.c,vmbn10';
	private $iv = '';

	function AES_Encode($plain_text)
	{	    
		 return $plain_text;
	   //return base64_encode(openssl_encrypt($plain_text, "aes-256-cbc", $this->key, true, $this->iv));
	}

	function AES_Decode($base64_text)
	{	    
		return $base64_text;
	  //return openssl_decrypt(base64_decode($base64_text), "aes-256-cbc", $this->key, true, $this->iv);
	}
}

?>