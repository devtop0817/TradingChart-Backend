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

    if( !isset( $_POST['token'] ) || !isset( $_POST['name'] ) ) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		exit();
    }
    $token = $_POST['token'];
    $name = $_POST['name'];

    $userid = '';
    $secret_key = "stockchart_valentin123!@#";
    try {
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
        $userid = $decoded->ID;
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
    
    $query = "DELETE [csvdatasminichart].[dbo].[usertemplate] WHERE userid='".$userid."' AND name='".$name."'";
    $result = sqlsrv_query( $conn, $query );
    
    $returnVal = [
        'status' => true
    ];
    echo json_encode($returnVal);
?>