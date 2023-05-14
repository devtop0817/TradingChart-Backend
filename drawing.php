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
    

    if( !isset( $_POST['token'] ) || !isset( $_POST['script'] ) ) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		return;
    }
	$token = $_POST['token'];
    $script = $_POST['script'];
	
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
		return;
    }
    if($userid == '') {
        $returnValue = [
            "status" => false,
            "message" => 2
		];
		echo json_encode($returnValue);
		return;
    }
    
    $query = "DELETE FROM [csvdatasminichart].[dbo].[userdrawing] WHERE userid = '".$userid."'";

    $templates = sqlsrv_query( $conn, $query );
    if( $templates === false )  {
        $returnValue = [
            "status" => false,
            "message" => 3
        ];
        echo json_encode($returnValue);
		return;
    }
	$currenttime = date('Y-m-d H:i:s');
	$script = str_replace("'", "''", $script);
	$insertquery = "INSERT INTO [csvdatasminichart].[dbo].[userdrawing] (userid, script, datetime) VALUES('".$userid."', '".$script."', '".$currenttime."')";
	$insertresult = sqlsrv_query($conn, $insertquery);
	if($insertresult === false) {
		$returnValue = [
			"status" => false,
			"message" => 5
		];
		echo json_encode($returnValue);
		return;
	}
	$returnSuccessValue = [
		"status" => true,
	];
	echo json_encode($returnSuccessValue);
	return;
?>