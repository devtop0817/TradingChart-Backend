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
 
    $indicatorquery = "SELECT * FROM [csvdatasminichart].[dbo].[userindicator] WHERE userid = '".$userid."' ORDER BY indicator ASC"  ; 
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
    $returnVal = [
        'status' => true,
        'data' => $indicators
    ];
    echo $_GET['callback'] . '('.json_encode($returnVal).')';
?>