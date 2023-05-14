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

    
    if( !isset( $_POST['token'] ) ) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		exit();
    }
    $token = $_POST['token'];
    $userid = '';
    $id = '';
    $secret_key = "stockchart_valentin123!@#";
    try {
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
        $userid = $decoded->Email;
        $id = $decoded->ID;
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
    $indicators = [];
    if($userid != 'p8@dealing.sg') {
        $indicatorquery = "SELECT userindicator.* FROM [csvdatasminichart].[dbo].[userindicator]  INNER JOIN [MiniAdmin].[dbo].[UserIndicators] ON userindicator.indicator = UserIndicators.Indicator WHERE userindicator.userid='p8@dealing.sg' AND UserIndicators.UserID='".$id."'"  ; 
        $result = sqlsrv_query( $conn, $indicatorquery );  
        
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
    }
    
    $returnVal = [
        'status' => true,
        'data' => $indicators
    ];
    echo json_encode($returnVal);
?>