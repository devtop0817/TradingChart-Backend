<?php
    require __DIR__ . '/vendor/autoload.php';
	use \Firebase\JWT\JWT;

    header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 

    $serverName = "DESKTOP-D48NNQG\SQLEXPRESS";  
	$connectionOptions = array("Database"=>"csvdatasminichart", "UID"=>"sa", "PWD"=>"Atiks11", "CharacterSet" => "UTF-8");
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if( $conn === false ) {
		die( print_r( sqlsrv_errors(), true));
    }

    
    if( !isset( $_POST['token'] ) || !isset( $_POST['tablename'] ) || !isset( $_POST['timeunit'] ) || !isset( $_POST['ordercondition'] ) || !isset( $_POST['wherecondition'] ) || !isset( $_POST['market'] ) || !isset( $_POST['start'] )) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		exit();
    }
    $token = $_POST['token'];
    $tablename = $_POST['tablename'];
    $timeunit = $_POST['timeunit'];
    $ordercondition = $_POST['ordercondition'];
    $wherecondition = $_POST['wherecondition'];
    $start = $_POST['start'];
    //$market = json_decode($_POST['market']);
    $market = $_POST['market'];
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
    $marketcount = count($market);
    $marketcondition = " AND (";
    
    for($i = 0; $i < $marketcount - 1; $i++){
        $marketcondition = $marketcondition."market='".$market[$i]."' or ";
    }
    $marketcondition = $marketcondition."market='".$market[$marketcount - 1]."')";
    $indicatorquery = "SELECT * FROM (SELECT *, ROW_NUMBER() OVER ";
    if($ordercondition != '') {
        $indicatorquery = $indicatorquery.'('.$ordercondition.') as seq FROM '.$tablename." WHERE timeunit='".$timeunit."'";
    } else {
        $indicatorquery = $indicatorquery.'(ORDER BY market, symbol ASC) as seq FROM '.$tablename." WHERE timeunit='".$timeunit."'";
    }
    if($wherecondition != '') {
        $indicatorquery = $indicatorquery.' '.$wherecondition;
    }
    $date = date('Y-m-d');
    $days_ago = date('Y-m-d', strtotime('-1 days'));
    $date_where = " AND ([datetime] BETWEEN '".$days_ago."' AND '".$date."') ";
    $indicatorquery = $indicatorquery.$date_where;
    $end = $start + 50;
    $indicatorquery = $indicatorquery.$marketcondition.')t WHERE seq BETWEEN '.($start + 1).' AND '.$end;
    $result = sqlsrv_query( $conn, $indicatorquery );
    $scandata = [];
    $scanfield = [];
    $i = 0;
    foreach(sqlsrv_field_metadata($result) as $field){
        array_push($scanfield, $field['Name']);
    }
    $count = count($scanfield);
    while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_NUMERIC )) {
        $row = [];
	array_push($row, $obj[$count - 2]->format('Y-m-d H:i:s'));
        for($i = 2; $i < $count - 2; $i++) {
            $data = $obj[$i];
            array_push($row, $data);
        }
        array_push($scandata, $row);
    }
    array_splice($scanfield, $count - 3, 3);
    array_splice($scanfield, 0, 2);
    
    $returnVal = [
        'status' => true,
        'data' => $scandata,
        'field' => $scanfield
    ];
    echo json_encode($returnVal);
?>