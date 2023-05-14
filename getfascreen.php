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
		echo json_encode($returnValue);
		exit();
    }
    $token = $_GET['token'];
    $userid = '';
    $useremail = '';
    $secret_key = "stockchart_valentin123!@#";
    try {
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
        $useremail = $decoded->Email;
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
    $indicatorquery = "SELECT * FROM [csvdatasminichart].[dbo].[userscan] WHERE userid = '".$userid."'"  ; 
    $result = sqlsrv_query( $conn, $indicatorquery );  
    $customscan = [];
    while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
        $name = $obj['name'];
        $script = $obj['script'];
        $id = $obj['id'];
        $encrypt = $obj['encrypt'];
        $pass = $obj['pass'];
        $ele = [
            'id' => $id,
            'name' => $name,
        ];
        array_push($customscan, $ele);
    }
    $returnVal = [
        'status' => true,
        'scan' => $customscan
    ];
    echo json_encode($returnVal);
?>