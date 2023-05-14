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
    if( !isset( $_POST['token'] ) || !isset( $_POST['name'] ) || !isset( $_POST['script'] ) || !isset( $_POST['id'] ) || !isset( $_POST['encrypt'] ) || !isset( $_POST['password'] )) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		exit();
    }
    
    $name = $_POST['name'];
	$token = $_POST['token'];
    $script =  $_POST['script'];
    $id = $_POST['id'];
    $simpleflag = $_POST['simpleflag'];

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
    if ($simpleflag)
        $scanquery = "UPDATE [csvdatasminichart].[dbo].[usercustomsimplescan] SET name='".$name."', encrypt='".$encrypt."', password='".$password."', datetime='".$currenttime."' WHERE id = ".$id; 
    else
        $scanquery = "UPDATE [csvdatasminichart].[dbo].[usercustomscan] SET name='".$name."', script='".$script."', encrypt='".$encrypt."', password='".$password."', datetime='".$currenttime."' WHERE id = ".$id; 
    $res = sqlsrv_query( $conn, $scanquery );
    if($res == false) {
        $returnValue = [
            "status" => false,
            "message" => 1
        ];
		echo json_encode($returnValue);
		exit();
    } else {
        if ($simpleflag) {
            $deletequery = "DELETE FROM [csvdatasminichart].[dbo].[usercustomsimplescanformula] WHERE scanid=".$id;
            $deletescans = sqlsrv_query($conn, $deletequery);

            $insertquery = "INSERT INTO [csvdatasminichart].[dbo].[usercustomsimplescanformula] (userid, scanid, condid, formulaid, formula, formulatype, operator, combineid, combinetype, combineformula, showcolumn, columnname) VALUES ";
            for ($i = 0; $i < count($script); $i++) {
                if ($i == 0)
                    $insertquery .= "('".$userid."', ".$id.", ".$script[$i]['condid'].", ".$script[$i]['formulaid'].", '".$script[$i]['formula']."', ".$script[$i]['formulatype'].", ".$script[$i]['operator'].", ".$script[$i]['combineid'].", ".$script[$i]['combinetype'].", '".$script[$i]['combineformula']."', ".$script[$i]['showcolumn'].", '".$script[$i]['columnname']."') ";    
                else
                    $insertquery .= ", ('".$userid."', ".$id.", ".$script[$i]['condid'].", ".$script[$i]['formulaid'].", '".$script[$i]['formula']."', ".$script[$i]['formulatype'].", ".$script[$i]['operator'].", ".$script[$i]['combineid'].", ".$script[$i]['combinetype'].", '".$script[$i]['combineformula']."', ".$script[$i]['showcolumn'].", '".$script[$i]['columnname']."')";
            }
            $insertscans = sqlsrv_query($conn, $insertquery);
        }

        $returnVal = [
            'status' => true
        ];
        echo json_encode($returnVal);
    }
?>