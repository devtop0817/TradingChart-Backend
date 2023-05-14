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
    // if( !isset( $_POST['token'] ) || !isset( $_POST['name'] ) || !isset( $_POST['script'] ) || !isset( $_POST['id'] ) || !isset( $_POST['encrypt'] ) || !isset( $_POST['password'] )) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		exit();
    }
    
    $name = $_POST['name'];
	$token = $_POST['token'];
    // $script =  $_POST['script'];
    $id = $_POST['id'];
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
	$currenttime = date('Y-m-d H:i:s');
    $scanquery = "UPDATE [csvdatasminichart].[dbo].[usercustomfascreen] SET name='".$name."', encrypt='".$encrypt."', password='".$password."', datetime='".$currenttime . "', ";
    $scanquery .= "type1='" . $type1 . "', field1='" . $field1 . "', operator1='" . $operator1 . "', value1='" . $value1 . "', criteria1='" . $criteria1 . "', ";
    $scanquery .= "type2='" . $type2 . "', field2='" . $field2 . "', operator2='" . $operator2 . "', value2='" . $value2 . "', criteria2='" . $criteria2 . "', ";
    $scanquery .= "type3='" . $type3 . "', field3='" . $field3 . "', operator3='" . $operator3 . "', value3='" . $value3 . "', criteria3='" . $criteria3 . "', ";
    $scanquery .= "type4='" . $type4 . "', field4='" . $field4 . "', operator4='" . $operator4 . "', value4='" . $value4 . "', criteria4='" . $criteria4 . "', ";
    $scanquery .= "type5='" . $type5 . "', field5='" . $field5 . "', operator5='" . $operator5 . "', value5='" . $value5 . "', criteria5='" . $criteria5 . "', ";
    $scanquery .= "type6='" . $type6 . "', field6='" . $field6 . "', operator6='" . $operator6 . "', value6='" . $value6 . "', criteria6='" . $criteria6 . "', ";
    $scanquery .= "type7='" . $type7 . "', field7='" . $field7 . "', operator7='" . $operator7 . "', value7='" . $value7 . "', criteria7='" . $criteria7 . "', ";
    $scanquery .= "type8='" . $type8 . "', field8='" . $field8 . "', operator8='" . $operator8 . "', value8='" . $value8 . "', criteria8='" . $criteria8 . "', ";
    $scanquery .= "type9='" . $type9 . "', field9='" . $field9 . "', operator9='" . $operator9 . "', value9='" . $value9 . "', criteria9='" . $criteria9 . "', ";
    $scanquery .= "type10='" . $type10 . "', field10='" . $field10 . "', operator10='" . $operator10 . "', value10='" . $value10 . "', criteria10='" . $criteria10;
    $scanquery .= "' WHERE id = ".$id;
    $res = sqlsrv_query( $conn, $scanquery );
    if($res == false) {
        $returnValue = [
            "status" => false,
            "message" => 1
		];
		echo json_encode($returnValue);
		exit();
    } else {
        $returnVal = [
            'status' => true
        ];
        echo json_encode($returnVal);
    }
    // $returnVal = [
    //     'status' => true
    // ];
    // echo json_encode($returnVal);
?>