<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('LOG_FILE_AUTO_DELETE_DATE', 30); // ���� �α� ������ ���� (30���� �α� ����)
define('RES_CODE_SUCCESS','0000');
define('ERR_CODE_POST','0001');
define('ERR_CODE_JSON','0002');

// COUNT OF ENTITY ARRAY
define('COUNT_OF_ORDER','38');
define('COUNT_OF_PRODUCTS','42');
define('COUNT_OF_OPTIONS','33');
define('COUNT_OF_PAYMENTS','41');
define('COUNT_OF_CARD','19');
define('COUNT_OF_COUPON','19');

// EWHACOOP ESHOP DEPARTMENT_CODE
define('EWHACOOP_ESHOP_DEPARTMENT_CODE','7000100');

define('AES_KEY', 'toapi_gftpre_card');//AES ��ȣȭ Ű:makebot.ai�� AES256 ��ȯ��
define('KEY_128', substr(AES_KEY, 0, 128 / 8));
define('KEY_256', substr(AES_KEY, 0, 256 / 8));

define('TEST_URL', 'https://testapi.kiwoompaypos.co.kr/api/SaleReceipt/sales?searchDate=');
define('TEST_AUTH', 'Y2VnbTlXdDNlZUlnL3h4M0xsaXFvQT09OmhScXNGeEVSSWJiUmdjQmh4RFZJL1E9PQ==');
define('CAMPUS', '001');

/* End of file constants.php */
/* Location: ./application/config/constants.php */