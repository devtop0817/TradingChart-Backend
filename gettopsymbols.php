<?php
    require __DIR__ . '/vendor/autoload.php';
	use \Firebase\JWT\JWT;

    header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 

     $serverName = "DESKTOP-D48NNQG\SQLEXPRESS";
	$connectionOptions = array("Database"=>"csvdatas", "UID"=>"sa", "PWD"=>"Atiks11", "CharacterSet" => "UTF-8");
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if( $conn === false ) {
		die( print_r( sqlsrv_errors(), true));
    }
    if( !isset( $_POST['token'] ) || !isset( $_POST['market'] )) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		exit();
    }
    $token = $_POST['token'];
    $market = $_POST["market"];
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
    $indicatorquery = "SELECT TOP (170) * FROM [csvdatas].[dbo].[BigMove] WHERE market='".$market."' AND  [datadate] = (SELECT MAX(datadate) FROM [csvdatas].[dbo].[BigMove]  WHERE market='".$market."') order by [VolumeSpike] DESC";
    $result = sqlsrv_query($conn, $indicatorquery);
    if($result == false) {
        echo $indicatorquery;
        exit;
    }
    
    $indicators = [];
    while($obj = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $market = $obj['market'];
		$company = $obj['company'];
        $symbol = $obj['symbol'];
        $ele = [
            'exchange' => $market,
            'company' => $company,
            'symbol' => $symbol,
        ];
        array_push($indicators, $ele);
    }
    $returnVal = [
        'status' => true,
        'data' => $indicators
    ];
    echo json_encode($returnVal);
?>