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
    if( !isset( $_POST['token'] ) || !isset( $_POST['name'] ) || !isset( $_POST['script'] ) || !isset( $_POST['id'] ) || !isset( $_POST['encrypt'] ) || !isset( $_POST['password'] )) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		exit();
    }
    
    $indicatorname = $_POST['name'];
	$token = $_POST['token'];
    $script =  $_POST['script'];
    $id = $_POST['id'];
    $encrypt = $_POST['encrypt'];
    $password = $_POST['password'];

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
		echo json_encode($returnValue);
		exit();
    }

    if($userid == '') {
        $returnValue = [
            "status" => false,
            "message" => 1
		];
		echo json_encode($returnValue);
		exit();
    }
    $indicatorquery = "UPDATE [csvdatasminichart].[dbo].[userindicator] SET indicator='".$indicatorname."', script='".$script."', encrypt='".$encrypt."', pass='".$password."' WHERE id = ".$id; 
    sqlsrv_query( $conn, $indicatorquery );  
    $returnVal = [
        'status' => true/*,
        'sql' => $indicatorquery*/
    ];
    echo json_encode($returnVal);
?>