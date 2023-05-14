<?php
    require __DIR__ . '/vendor/autoload.php';
	use \Firebase\JWT\JWT;

    header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 

    $serverName = "DESKTOP-D48NNQG\SQLEXPRESS";
    
	$connectionOptions = array("Database"=>"csvdatasminichart", "UID"=>"sa", "PWD"=>"Atiks11");
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if( $conn === false ) {
		die( print_r( sqlsrv_errors(), true));
    }

    
    if( !isset( $_GET['token'] ) || !isset( $_GET['id'] ) ) {
		$returnValue = [
			"status" => false
		];
		echo $_GET['callback'] . '('.json_encode($returnValue).')';
		exit();
    }

    $id = $_GET['id'];
    $token = $_GET['token'];
    
    $userid = '';
    $secret_key = "stockchart_valentin123!@#";
    try {
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
        $userid = $decoded->Email;
	}
	catch (UnexpectedValueException $e) {
		$returnValue = [
            "status" => false,
            "message" => 1
		];
		echo $_GET['callback'] . '('.json_encode($returnValue).')';
		exit();
    }

    if($userid == '') {
        $returnValue = [
            "status" => false,
            "message" => 1
		];
		echo $_GET['callback'] . '('.json_encode($returnValue).')';
		exit();
    }
    $indicatorquery = "DELETE FROM [csvdatasminichart].[dbo].[userindicator] WHERE id = '".$id."'";
    sqlsrv_query( $conn, $indicatorquery );  
    $returnVal = [
        'status' => true
    ];
    echo $_GET['callback'] . '('.json_encode($returnVal).')';
?>