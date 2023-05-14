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
    
    if( !isset( $_POST['token'] ) || !isset( $_POST['name'] ) || !isset( $_POST['script'] ) || !isset( $_POST['encrypt'] ) || !isset( $_POST['password'] )  ) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		return;
    }
    $name = $_POST['name'];
    $simpleflag = $_POST['simpleflag'];
	$token = $_POST['token'];
    $script = $_POST['script'];
	$encrypt = $_POST['encrypt'];
	$password = $_POST['password'];
	
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

    $scanquery = "";
    if ($simpleflag)
        $scanquery = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomsimplescan] WHERE userid = '".$userid."' AND name = '".$name."' "  ; 
    else
        $scanquery = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomscan] WHERE userid = '".$userid."' AND name = '".$name."' "  ;

    $scans = sqlsrv_query( $conn, $scanquery );
    if( $scans === false )  
        die( FormatErrors( sqlsrv_errors() ) );  
    if(sqlsrv_has_rows($scans)) {
        $returnValue = [
            "status" => false,
            "message" => 2
        ];
        echo json_encode($returnValue);
		return;
    }

    if ($simpleflag)
        $insertquery = "INSERT INTO [csvdatasminichart].[dbo].[usercustomsimplescan] (userid, name, encrypt, password, datetime) VALUES('".$userid."', '".$name."', ".$encrypt.", '".$password."', '".$currenttime."')";
    else 
        $insertquery = "INSERT INTO [csvdatasminichart].[dbo].[usercustomscan] (userid, name, script, encrypt, password, datetime) VALUES('".$userid."', '".$name."', '".$script."', ".$encrypt.", '".$password."', '".$currenttime."')";
    $insertscans = sqlsrv_query($conn, $insertquery);

    if($insertscans === false) {
        
        $returnValue = [
            "status" => false,
            "message" => 3
        ];
        echo json_encode($returnValue);
		return;
    }

    if($simpleflag)
        $query = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomsimplescan] WHERE userid = '".$userid."' AND name = '".$name."'";
    else
        $query = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomscan] WHERE userid = '".$userid."' AND name = '".$name."'";

    $result = sqlsrv_query( $conn, $query );  
    $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC );
    $Id = $obj['id'];
    $returnSuccessValue = [
        "status" => true,
        "id" => $Id
    ];

    if ($simpleflag) {
        $insertquery = "INSERT INTO [csvdatasminichart].[dbo].[usercustomsimplescanformula] (userid, scanid, condid, formulaid, formula, formulatype, operator, combinetype, combineid, combineformula, showcolumn, columnname) VALUES ";
        for ($i = 0; $i < count($script); $i++) {
            if ($i == 0)
                $insertquery .= "('".$userid."', ".$Id.", ".$script[$i]['condid'].", ".$script[$i]['formulaid'].", '".$script[$i]['formula']."', ".$script[$i]['formulatype'].", ".$script[$i]['operator'].", ".$script[$i]['combinetype'].", ".$script[$i]['combineid'].", '".$script[$i]['combineformula']."', ".$script[$i]['showcolumn'].", '".$script[$i]['columnname']."')";
            else
                $insertquery .= ", ('".$userid."', ".$Id.", ".$script[$i]['condid'].", ".$script[$i]['formulaid'].", '".$script[$i]['formula']."', ".$script[$i]['formulatype'].", ".$script[$i]['operator'].", ".$script[$i]['combinetype'].", ".$script[$i]['combineid'].", '".$script[$i]['combineformula']."', ".$script[$i]['showcolumn'].", '".$script[$i]['columnname']."')";
        }

        $insertscans = sqlsrv_query($conn, $insertquery);
    }
    
    echo json_encode($returnSuccessValue);
    return;
?>