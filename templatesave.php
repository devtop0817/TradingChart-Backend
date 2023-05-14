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
    

    if( !isset( $_POST['token'] ) || !isset( $_POST['name'] ) || !isset( $_POST['script'] ) ) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		return;
    }
    $templatename = $_POST['name'];
	$token = $_POST['token'];
    $templatescript = $_POST['script'];
	
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
            "message" => 1
		];
		echo json_encode($returnValue);
		return;
    }
    $query = "SELECT * FROM [csvdatasminichart].[dbo].[usertemplate] WHERE userid = '".$userid."' AND [name] = '".$templatename."'";
    $templates = sqlsrv_query( $conn, $query );
    if( $templates === false )  {
        $returnValue = [
            "status" => false,
            "message" => 3
        ];
        echo json_encode($query);
		return;
    }
        
    if(sqlsrv_has_rows($templates)) {
        $returnValue = [
            "status" => false,
            "message" => 2
        ];
        echo json_encode($returnValue);
		return;
    }
	$currenttime = date('Y-m-d H:i:s');
    $insertquery = "INSERT INTO [csvdatasminichart].[dbo].[usertemplate] (userid, [name], script, datetime) VALUES('".$userid."', '".$templatename."', '".$templatescript."', '".$currenttime."')";
    $insertIndicators = sqlsrv_query($conn, $insertquery);
	if( $insertIndicators === false ) {
        $returnValue = [
            "status" => false,
            "message" => 4
        ];
        echo json_encode($returnValue);
		return;
    }
    $returnSuccessValue = [
        "status" => true
    ];
    echo json_encode($returnSuccessValue);
    return;
?>