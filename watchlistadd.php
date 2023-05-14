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
	$token = $_POST['token'];
    $name = $_POST['name'];
    $script = str_replace("'", "''", $_POST['script']);
	
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
    
    $query = "SELECT * FROM [csvdatasminichart].[dbo].[watchlist] WHERE userid = '".$userid."' AND name = '".$name."'";
    $templates = sqlsrv_query( $conn, $query );
    if( $templates === false )  {
        
        $returnValue = [
            "status" => false,
            "message" => 3
        ];
        echo json_encode($returnValue);
		return;
    }
        
    if(sqlsrv_has_rows($templates)) {
        
        $obj = sqlsrv_fetch_array($templates, SQLSRV_FETCH_ASSOC);
        $temp = json_decode($obj['script'], true);
        if ($temp == null)
            $script_temp = json_encode(array_unique(json_decode($script, true), SORT_REGULAR));
        else {
            $temp = array_merge($temp, json_decode($script, true));
            $temp1 = array_unique($temp, SORT_REGULAR);
            $script_temp = json_encode($temp1);
        }
        $currenttime = date('Y-m-d H:i:s');
        $indicatorquery = "UPDATE [csvdatasminichart].[dbo].[watchlist] SET script='".$script_temp."', datetime='".$currenttime."' WHERE userid = '".$userid."' AND name='".$name."'"; 
        sqlsrv_query( $conn, $indicatorquery );  
        $returnVal = [
            'status' => true,
            "id" => $obj['id']/*,
            'sql' => $indicatorquery*/
        ];
        echo json_encode($returnVal);
        return;
    } else {
        
        $currenttime = date('Y-m-d H:i:s');
        $insertquery = "INSERT INTO [csvdatasminichart].[dbo].[watchlist] (userid, name, script, datetime) VALUES('".$userid."', '".$name."', '".$script."', '".$currenttime."')";
        $insertresult = sqlsrv_query($conn, $insertquery);
        
        if($insertresult === false) {
            $returnValue = [
                "status" => false,
                "message" => 3
            ];
            echo json_encode($returnValue);
            return;
        }
        $watchlistquery = "SELECT * FROM [csvdatasminichart].[dbo].[watchlist] WHERE userid = '".$userid."' AND name = '".$name."'";
        $result = sqlsrv_query( $conn, $watchlistquery );  
        $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC );
        $Id = $obj['id'];
    
        $returnSuccessValue = [
            "status" => true,
            "id" => $Id
        ];
        echo json_encode($returnSuccessValue);
        return;
    }
?>