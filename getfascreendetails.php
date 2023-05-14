<?php
require __DIR__ . '/vendor/autoload.php';

use \Firebase\JWT\JWT;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";
$connectionOptions = array("Database" => "FinicalDB", "UID" => "sa", "PWD" => "Atiks11");
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (!isset($_POST['token']) || !isset($_POST['symbol'])) {
    $returnValue = [
        "status" => false
    ];
    echo json_encode($returnValue);
    return;
}
$token = $_POST['token'];
$symbol = $_POST['symbol'];
$market = $_POST['market'];

if ($market == "SGX") {
    $market = "SGX";
} else if ($market == "HKEX") {
    $market = "HKSE";
    $symbol = "0" . substr($symbol, 0, 4);
} else if ($market == "NASD") {
    $market = "NAS";
} else if ($market == "NYSE") {
    $market = "NYSE";
} else if ($market == "SZSE") {
    $market = "SZSE";
    $symbol = "0" . substr($symbol, 0, 5);
} else if ($market == "SSE") {
    $market = "SHSE";
    $symbol = "0" . substr($symbol, 0, 6);
}

$secret_key = "stockchart_valentin123!@#";
try {
    $decoded = JWT::decode($token, $secret_key, array('HS256'));
} catch (UnexpectedValueException $e) {
    $returnValue = [
        "status" => false
    ];
    echo json_encode($returnValue);
    return;
}

$query = "SELECT * FROM [FinicalDB].[dbo].[Market] where Symbol_code = '$symbol' AND Fiscal_year <> '/current' order by Fiscal_year ASC"; 
$details = [];
$result = sqlsrv_query($conn, $query);
while($obj = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    array_push($details, $obj); 
}
$details = array_reverse($details);

$query = "SELECT * FROM [FinicalDB].[dbo].[Market] where Symbol_code = '$symbol' AND Fiscal_year = '/current'"; 
$result = sqlsrv_query($conn, $query);
while($obj = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
	array_unshift($details, $obj);
}

$returnVal = [
    'status' => true,
    'data' => $details
];
echo json_encode(utf8_converter($returnVal));


function utf8_converter($array){
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
            $item = utf8_encode($item);
        }
    }); 
    return $array;
}
?>
