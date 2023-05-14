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

    
    if( !isset( $_GET['token'] ) ) {
		$returnValue = [
			"status" => false
		];
		echo $_GET['callback'] . '('.json_encode($returnValue).')';
		exit();
    }
    $token = $_GET['token'];
    $userid = '';
    $userEmail = '';
    $secret_key = "stockchart_valentin123!@#";
    try {
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
        $userEmail = $decoded->Email;
        $userid = $decoded->ID;
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
    $indicatorquery = "SELECT * FROM [csvdatasminichart].[dbo].[userindicator] WHERE userid = '".$userEmail."'"  ; 
    $result = sqlsrv_query( $conn, $indicatorquery );
    $indicators = [];
    while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
        $indicatorname = $obj['indicator'];
        $script = $obj['script'];
        $overlay = $obj['overlay'];
        $indicatorId = $obj['id'];
        $encrypt = $obj['encrypt'];
        $pass = $obj['pass'];
        $ele = [
            'id' => $indicatorId,
            'name' => $indicatorname,
            'script' => $script,
            'overlay' => $overlay,
            'encrypt' => $encrypt,
            'password' => $pass
        ];
        array_push($indicators, $ele);
    }

    $templatequery = "SELECT * FROM [csvdatasminichart].[dbo].[usertemplate] WHERE userid = '".$userid."'"  ; 
    $templateresult = sqlsrv_query( $conn, $templatequery );  
    $templates = [];
    while( $obj = sqlsrv_fetch_array( $templateresult, SQLSRV_FETCH_ASSOC )) {
        $name = $obj['name'];
        $script = $obj['script'];
        $templateId = $obj['id'];
        $ele = [
            'id' => $templateId,
            'name' => $name,
            'script' => $script,
        ];
        array_push($templates, $ele);
    }


    $returnVal = [
        'status' => true,
        'indicators' => $indicators,
        'templates' => $templates
    ];
    echo $_GET['callback'] . '('.json_encode($returnVal).')';
?>