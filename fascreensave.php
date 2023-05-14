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
    

    if( !isset( $_POST['token'] ) || !isset( $_POST['name'] ) || (!isset($_POST['value1']) &&  !isset($_POST['value2']) && !isset($_POST['value3']) && !isset($_POST['value4']) && !isset($_POST['value5']) && !isset($_POST['value6']) && !isset($_POST['value7']) && !isset($_POST['value8']) && !isset($_POST['value9']) && !isset($_POST['value10']))) {
    // if( !isset( $_POST['token'] ) || !isset( $_POST['name'] ) || !isset( $_POST['script'] ) || !isset( $_POST['encrypt'] ) || !isset( $_POST['password'] )  ) {
		$returnValue = [
			"status" => false
		];
        echo json_encode($returnValue);
		return;
    }
    $name = $_POST['name'];
	$token = $_POST['token'];
    // $script = $_POST['script'];
	$encrypt = 0; //$_POST['encrypt'];
    $password = ""; //$_POST['password'];
    $type1 = $_POST['type1']; $field1 = $_POST['field1']; $operator1 = $_POST['operator1']; $value1 = $_POST['value1']; $criteria1 = $_POST['criteria1'];
    $type2 = $_POST['type2']; $field2 = $_POST['field2']; $operator2 = $_POST['operator2']; $value2 = $_POST['value2']; $criteria2 = $_POST['criteria2'];
    $type3 = $_POST['type3']; $field3 = $_POST['field3']; $operator3 = $_POST['operator3']; $value3 = $_POST['value3']; $criteria3 = $_POST['criteria3'];
    $type4 = $_POST['type4']; $field4 = $_POST['field4']; $operator4 = $_POST['operator4']; $value4 = $_POST['value4']; $criteria4 = $_POST['criteria4'];
    $type5 = $_POST['type5']; $field5 = $_POST['field5']; $operator5 = $_POST['operator5']; $value5 = $_POST['value5']; $criteria5 = $_POST['criteria5'];
    $type6 = $_POST['type6']; $field6 = $_POST['field6']; $operator6 = $_POST['operator6']; $value6 = $_POST['value6']; $criteria6 = $_POST['criteria6'];
    $type7 = $_POST['type7']; $field7 = $_POST['field7']; $operator7 = $_POST['operator7']; $value7 = $_POST['value7']; $criteria7 = $_POST['criteria7'];
    $type8 = $_POST['type8']; $field8 = $_POST['field8']; $operator8 = $_POST['operator8']; $value8 = $_POST['value8']; $criteria8 = $_POST['criteria8'];
    $type9 = $_POST['type9']; $field9 = $_POST['field9']; $operator9 = $_POST['operator9']; $value9 = $_POST['value9']; $criteria9 = $_POST['criteria9'];
    $type10 = $_POST['type10']; $field10 = $_POST['field10']; $operator10 = $_POST['operator10']; $value10 = $_POST['value10'];  $criteria10 = $_POST['criteria10'];

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
	$currenttime = date('Y-m-d H:i:s');
    $fascreenquery = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomfascreen] WHERE userid = '".$userid."' AND name = '".$name."' "  ; 
    $fascreens = sqlsrv_query( $conn, $fascreenquery );
    if( $fascreens === false )  
        die( FormatErrors( sqlsrv_errors() ) );  
    if(sqlsrv_has_rows($fascreens)) {
        $returnValue = [
            "status" => false,
            "message" => 2
        ];
        echo json_encode($returnValue);
		return;
    }

    $insertquery = "INSERT INTO [csvdatasminichart].[dbo].[usercustomfascreen] (userid, name, type1,field1,operator1,value1,criteria1,type2,field2,operator2,value2,criteria2,type3,field3,operator3,value3,criteria3,type4,field4,operator4,value4,criteria4,type5,field5,operator5,value5,criteria5,type6,field6,operator6,value6,criteria6,type7,field7,operator7,value7,criteria7,type8,field8,operator8,value8,criteria8,type9,field9,operator9,value9,criteria9,type10,field10,operator10,value10,criteria10, encrypt, password, datetime) VALUES('".$userid."', '".$name."', '";
    $insertquery .= $type1 . "', '" . $field1 . "', '" . $operator1 . "', '" . $value1 . "', '" . $criteria1 . "', '";
    $insertquery .= $type2 . "', '" . $field2 . "', '" . $operator2 . "', '" . $value2 . "', '" . $criteria2 . "', '";
    $insertquery .= $type3 . "', '" . $field3 . "', '" . $operator3 . "', '" . $value3 . "', '" . $criteria3 . "', '";
    $insertquery .= $type4 . "', '" . $field4 . "', '" . $operator4 . "', '" . $value4 . "', '" . $criteria4 . "', '";
    $insertquery .= $type5 . "', '" . $field5 . "', '" . $operator5 . "', '" . $value5 . "', '" . $criteria5 . "', '";
    $insertquery .= $type6 . "', '" . $field6 . "', '" . $operator6 . "', '" . $value6 . "', '" . $criteria6 . "', '";
    $insertquery .= $type7 . "', '" . $field7 . "', '" . $operator7 . "', '" . $value7 . "', '" . $criteria7 . "', '";
    $insertquery .= $type8 . "', '" . $field8 . "', '" . $operator8 . "', '" . $value8 . "', '" . $criteria8 . "', '";
    $insertquery .= $type9 . "', '" . $field9 . "', '" . $operator9 . "', '" . $value9 . "', '" . $criteria9 . "', '";
    $insertquery .= $type10 . "', '" . $field10 . "', '" . $operator10 . "', '" . $value10  . "', '" . $criteria10;
    $insertquery .= "', ".$encrypt.", '".$password."', '".$currenttime."')";
    $insertfascreens = sqlsrv_query($conn, $insertquery);
    if($insertfascreens === false) {
        $returnValue = [
            "status" => false,
            "message" => 3
        ];
        echo json_encode($insertquery);
		return;
    }
    $query = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomfascreen] WHERE userid = '".$userid."' AND name = '".$name."'";
    $result = sqlsrv_query( $conn, $query );  
    $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC );
    $Id = $obj['id'];
    $returnSuccessValue = [
        "status" => true,
        "id" => $Id
    ];
    echo json_encode($returnSuccessValue);
    return;
?>