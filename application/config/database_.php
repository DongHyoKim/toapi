<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/


$active_group = 'default';
$active_record = TRUE;


/*
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = '';
$db['default']['password'] = '';
$db['default']['database'] = '';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

$logs = array(
    'sLogFileId'    => time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5),
    'sLogPath'      => '/home/toapi/logs/posApi/' . date('Ymd') . '_data.log',
    'bLogable'      => true
);

$sLogFileId  = time() . '_' . substr(md5(uniqid(rand(), true)), 0, 5);
$sLogPath    = '../../logs/posapi/success_log/' . date('Ymd') . '_data.log';
$eLogPath    = '../../logs/posapi/error_log/' . date('Ymd') . '_data.log';
$bLogable    = true;

$message = array(
    'rCode' => RES_CODE_SUCCESS,
    'error' => array (  'errorCode'     => null,
                        'errorMessage'  => null, ),
);
*/

$json_env_array = array();

// $_POST['UnivCode']와 $receiveHeader['univcode']
$receiveHeader = apache_request_headers();

if(isset($receiveHeader['univcode'])){
    $_POST['UnivCode'] = $receiveHeader['univcode'];
}

if(!isset($_POST['UnivCode'])) {
   $json_env_array['status'] = -10;    
   $json_env_array['message'] = "대학교코드가 존재하지 않습니다.";
}
$UnivCode = $_POST['UnivCode'];

$DB_CONNECTION = array(
    "00106"=> array("hostname"=>"0010601.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00106001") //KangWon =>연결됨
  , "00114"=> array("hostname"=>"0011401.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00114001") //KyungSang
  //, "00121"=> array("hostname"=>"0012101.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00121001") //BuKyung
  , "00121"=> array("hostname"=>"0012121.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00121001") //BuKyung
  , "00117"=> array("hostname"=>"0011701.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00117001") //SangJi
  //, "00113"=> array("hostname"=>"0011301.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00113001") //Ewha =>연결됨
  , "00113"=> array("hostname"=>"219.255.132.117,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00113001") //Ewha =>연결됨
  , "00116"=> array("hostname"=>"0011601.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00116001") //JeonNam =>연결됨
  //, "00116"=> array("hostname"=>"220.71.99.157,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00116001") //JeonNam =>연결됨
  , "00103"=> array("hostname"=>"0010301.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00103001") //JeonBuk
  , "00111"=> array("hostname"=>"0011101.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00111001") //Jeju_test
  //, "00111"=> array("hostname"=>"0011101.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"VENDINGM")  //Jeju
  , "00123"=> array("hostname"=>"0012301.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00123001") //ChungNam
  , "00120"=> array("hostname"=>"0012001.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00120001") //ChungBuk
  //, "00125"=> array("hostname"=>"0012501.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00125001") //한체대
  //, "00112"=> array("hostname"=>"0011201.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00112001") //Hufs
  //, "00112"=> array("hostname"=>"203.253.68.97:8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00112001") //Hufs
  //, "00100"=> array("hostname"=>"0010001.cway.kr,28433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00100001")//CwayTest
  , "10100"=> array("hostname"=>"1010001.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT10100001") //테스트모드
);

if(!$_POST['UnivCode']){
   $json_env_array['status'] = -1;    
   $json_env_array['message'] = "존재하지 않는 대학교코드입니다.";
   //echo json_encode($json_env_array);      
   //exit;
}

foreach($DB_CONNECTION as $key => $value){
	if($key == $UnivCode) {
		foreach($value as $k => $v){
			$db['default'][$k] = $v;
        }
		$db['default']['dbdriver'] = 'sqlsrv';
        $db['default']['dbprefix'] = '';
        $db['default']['pconnect'] = FALSE;
        $db['default']['db_debug'] = TRUE;
        $db['default']['cache_on'] = FALSE;
        $db['default']['cachedir'] = '';
        $db['default']['char_set'] = 'utf8';
        $db['default']['dbcollat'] = 'utf8_general_ci';
        $db['default']['swap_pre'] = '';
        $db['default']['autoinit'] = TRUE;
        $db['default']['stricton'] = FALSE;
	}
}

/*
$db['default']['hostname'] = $DB_CONNECTION[$UnivCode]['hostname'];
$db['default']['username'] = $DB_CONNECTION[$UnivCode]['username'];
$db['default']['password'] = $DB_CONNECTION[$UnivCode]['password'];
$db['default']['database'] = $DB_CONNECTION[$UnivCode]['database'];
$db['default']['dbdriver'] = 'sqlsrv';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;
*/

//echo $db['default']['hostname']."<br>";
//echo $db['default']['username']."<br>";
//echo $db['default']['password']."<br>";
//echo $db['default']['database']."<br>";
//echo json_encode($db);
//exit;