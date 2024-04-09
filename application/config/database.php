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
*/

$json_env_array = array();

// $_POST['UnivCode']와 $receiveHeader['univcode']
$receiveHeader = apache_request_headers();

if(isset($receiveHeader['univcode'])){
    $_REQUEST['UnivCode'] = $receiveHeader['univcode'];
}

if(!isset($_REQUEST['UnivCode'])) {
   $json_env_array['status'] = -10;    
   $json_env_array['message'] = "대학교코드가 존재하지 않습니다.";
}
$UnivCode = $_REQUEST['UnivCode'] ;

$DB_CONNECTION = array(
  //  "00100"=> array("hostname"=>"0010001.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00100001") //씨웨이_테스트
  //, "00106"=> array("hostname"=>"0010601.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00106001") //강원대
  //, "00111"=> array("hostname"=>"0011101.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00111001") //제주대
  //, "00113"=> array("hostname"=>"0011301.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00113001") //이화여대
  //, "00116"=> array("hostname"=>"0011621.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00116001") //전남대
  //, "00121"=> array("hostname"=>"0012121.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00121001") //부경대
  //, "00123"=> array("hostname"=>"0012301.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00123001") //충남대
  //, "00103"=> array("hostname"=>"0010301.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00103001") //전북대
  //, "00112"=> array("hostname"=>"0011201.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00112001") //한국외대
  //, "00114"=> array("hostname"=>"0011401.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00114001") //경상대
  //, "00117"=> array("hostname"=>"0011701.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00117001") //상지대
  //, "00120"=> array("hostname"=>"0012001.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00120001") //충북대
  //, "00125"=> array("hostname"=>"0012501.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00125001") //한체대
  //, "10100"=> array("hostname"=>"1010001.cway.kr,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT10100001") //테스트모드
    "00100"=> array("hostname"=>"61.252.153.78,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00100001") //씨웨이_테스트_IP
  , "00106"=> array("hostname"=>"203.252.73.155,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00106001") //강원대_IP
  , "00109"=> array("hostname"=>"10.1.1.79,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00109001") //경북대_IP
  , "00111"=> array("hostname"=>"203.253.209.174,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00111001") //제주대_IP
  , "00113"=> array("hostname"=>"219.255.132.117,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00113001") //이화여대_IP
  , "00116"=> array("hostname"=>"10.1.1.78,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00116001") //전남대_IP
  , "00121"=> array("hostname"=>"10.1.1.75,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00121001") //부경대_IP
  , "00123"=> array("hostname"=>"168.188.72.211,8433","username"=>"dmk","password"=>"!@dmk8191","database"=>"CPT00123001") //충남대_IP
);

if(!$_REQUEST['UnivCode']){
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

$db['mysql']['hostname'] = 'localhost';
$db['mysql']['username'] = 'deploy_card';
$db['mysql']['password'] = 'deploy_card';
$db['mysql']['database'] = 'deploy_card';
$db['mysql']['dbdriver'] = 'mysqli';
$db['mysql']['dbprefix'] = '';
$db['mysql']['pconnect'] = TRUE;
$db['mysql']['db_debug'] = TRUE;
$db['mysql']['cache_on'] = FALSE;
$db['mysql']['cachedir'] = '';
$db['mysql']['char_set'] = 'utf8';
$db['mysql']['dbcollat'] = 'utf8mb4_general_ci';
$db['mysql']['swap_pre'] = '';
$db['mysql']['autoinit'] = TRUE;
$db['mysql']['stricton'] = FALSE;