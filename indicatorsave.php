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
    

    if( !isset( $_POST['token'] ) || !isset( $_POST['indicatorname'] ) || !isset( $_POST['script'] )  || !isset( $_POST['overlay'] ) || !isset( $_POST['encrypt'] ) || !isset( $_POST['password'] )  ) {
		$returnValue = [
			"status" => false
		];
		echo $_POST['callback'] . '('.json_encode($returnValue).')';
		return;
    }
    $indicatorname = $_POST['indicatorname'];
	$token = $_POST['token'];
    $script = $_POST['script'];
    $overlay = $_POST['overlay'];
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
    $indicatorquery = "SELECT * FROM [csvdatasminichart].[dbo].[userindicator] WHERE userid = '".$userid."' AND indicator = '".$indicatorname."' "  ; 
    $indicators = sqlsrv_query( $conn, $indicatorquery );
    if( $indicators === false )  
        die( FormatErrors( sqlsrv_errors() ) );  
    if(sqlsrv_has_rows($indicators)) {
        $returnValue = [
            "status" => false,
            "message" => 2
        ];
        echo json_encode($returnValue);
		return;
    }

    $insertquery = "INSERT INTO [csvdatasminichart].[dbo].[userindicator] (userid, indicator, script, overlay, encrypt, pass) VALUES('".$userid."', '".$indicatorname."', '".$script."', ".$overlay.", ".$encrypt.", '".$password."')";
    $insertIndicators = sqlsrv_query($conn, $insertquery);
    $returnSuccessValue = [
        "status" => true
    ];
    echo json_encode($returnSuccessValue);
    return;
?>