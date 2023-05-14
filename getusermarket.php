<?php
    require __DIR__ . '/vendor/autoload.php';
	use \Firebase\JWT\JWT;

    header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 

    $serverName = "DESKTOP-D48NNQG\SQLEXPRESS";
	$connectionOptions = array("Database"=>"MiniAdmin", "UID"=>"sa", "PWD"=>"Atiks11");
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
    $Id = '';
    $secret_key = "stockchart_valentin123!@#";
    try {
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
        
        $userid = $decoded->Email;
        $Id = $decoded->ID;
	}
	catch (UnexpectedValueException $e) {
		$returnValue = [
            "status" => false,
            "message" => 1
		];
		echo json_encode($returnValue);
		exit();
    }
    
    if($userid == '' || $Id == '') {
        $returnValue = [
            "status" => false,
            "message" => 1
		];
		echo json_encode($returnValue);
		exit();
    }
    $marketquery = "SELECT * FROM [MiniAdmin].[dbo].[UserMarkets] WHERE UserId = '".$Id."'"  ; 
    $result = sqlsrv_query( $conn, $marketquery );  
    $markets = [];
    while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
        $marketname = $obj['Market'];
        array_push($markets, $marketname);
    }

    $adminscans = [];
    $ScannerAdd = 0;
    if($userid != 'p8@dealing.sg') {

        $scanquery = "SELECT usercustomscan.* FROM [csvdatasminichart].[dbo].[usercustomscan]  INNER JOIN [MiniAdmin].[dbo].[UserScanners] ON UserScanners.Scanner=usercustomscan.Name+'.scn' WHERE usercustomscan.userid='462AA229-C722-44BD-BA6E-CA546DD8A885' AND UserScanners.UserID='".$Id."' ORDER BY usercustomscan.name ASC"  ; 
        $result = sqlsrv_query( $conn, $scanquery );  
        
        while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
            $name = $obj['name'];
            $id = $obj['id'];
            $script = $obj['script'];
            $encrypt = $obj['encrypt'];
            $password = $obj['password'];
            $ele = [
                'id' => $id,
                'name' => $name,
                'script' => $script,
                'encrypt' => $encrypt,
                'password' => $password
            ];
            array_push($adminscans, $ele);
        }

        $userquery = "SELECT * FROM [MiniAdmin].[dbo].[User] WHERE Id = '".$Id."'"  ; 
            $result = sqlsrv_query( $conn, $userquery );  
            $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC );
            $ScannerAdd = $obj['Scanner_Add'];

    } else {
    	$ScannerAdd = 1;
    }

    $scanquery = "SELECT * FROM [csvdatasminichart].[dbo].[userscan]";
    $scanresult = sqlsrv_query( $conn, $scanquery );  
    $predefinedscan = [];
    while( $obj = sqlsrv_fetch_array( $scanresult, SQLSRV_FETCH_ASSOC )) {
        $name = $obj['name'];
        $id = $obj['id'];
        $ele = [
            'id' => $id,
            'name' => $name,
        ];
        array_push($predefinedscan, $ele);
    }

    $customsimplescanquery = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomsimplescan] WHERE userid='".$Id."'";
    $customsimplescanresult = sqlsrv_query( $conn, $customsimplescanquery );  
    $customsimplescan = [];
    while( $obj = sqlsrv_fetch_array( $customsimplescanresult, SQLSRV_FETCH_ASSOC )) {
        $name = $obj['name'];
        $id = $obj['id'];
        $encrypt = $obj['encrypt'];
        $password = $obj['password'];
        $ele = [
            'id' => $id,
            'name' => $name,
            'encrypt' => $encrypt,
            'password' => $password
        ];
        array_push($customsimplescan, $ele);
    }

    $customscanquery = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomscan] WHERE userid='".$Id."'";
    $customscanresult = sqlsrv_query( $conn, $customscanquery );  
    $customscan = [];
    while( $obj = sqlsrv_fetch_array( $customscanresult, SQLSRV_FETCH_ASSOC )) {
        $name = $obj['name'];
        $id = $obj['id'];
        $script = $obj['script'];
        $encrypt = $obj['encrypt'];
        $password = $obj['password'];
        $ele = [
            'id' => $id,
            'name' => $name,
            'script' => $script,
            'encrypt' => $encrypt,
            'password' => $password
        ];
        array_push($customscan, $ele);
    }

    $customscanformulaquery = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomsimplescanformula] WHERE userid='".$Id."'";
    $customscanformularesult = sqlsrv_query( $conn, $customscanformulaquery);
    $customscanformula = [];
    while( $obj = sqlsrv_fetch_array( $customscanformularesult, SQLSRV_FETCH_ASSOC )) {
        $formulaid = $obj['formulaid'];
        $scanid = $obj['scanid'];
        $operator = $obj['operator'];
        
        $showcolumn = $obj['showcolumn'];
        $columnname = $obj['columnname'];
        $ele = [
            'condid' => $obj['condid'],
            'formulaid' => $formulaid,
            'formula' => $obj['formula'],
            'formulatype' => $obj['formulatype'],
            'operator' => $operator,
            'combineid' => $obj['combineid'],
            'combinetype' => $obj['combinetype'],
            'combineformula' => $obj['combineformula'],
            'showcolumn' => $showcolumn,
            'columnname' => $columnname,
            'scanid' => $scanid,
        ];
        array_push($customscanformula, $ele);
    }

    $scanformulaquery = "SELECT * FROM [csvdatasminichart].[dbo].[scanformula]";
    $scanformularesult = sqlsrv_query( $conn, $scanformulaquery );
    $scanformula = [];
    while( $obj = sqlsrv_fetch_array( $scanformularesult, SQLSRV_FETCH_ASSOC )) {
        $formulaid = $obj['formulaid'];
        $id = $obj['id'];
        $subcategory = $obj['subcategory'];
        $formula = $obj['formula'];

        $ele = [
            'id' => $id,
            'formulaid' => $formulaid,
            'subcategory' => $subcategory,
            'formula' => $formula,
            'editable' => $obj['editable']
        ];
        array_push($scanformula, $ele);
    }


    $scanformulacategoryquery = "SELECT * FROM [csvdatasminichart].[dbo].[scanformulacategory]";
    $scanformulacategoryresult = sqlsrv_query( $conn, $scanformulacategoryquery );
    $scanformulacatogory = [];
    while( $obj = sqlsrv_fetch_array( $scanformulacategoryresult, SQLSRV_FETCH_ASSOC )) {
        $category = $obj['category'];
        $id = $obj['id'];
        $ele = [
            'id' => $id,
            'category' => $category,
        ];
        array_push($scanformulacatogory, $ele);
    }

	$userquery = "SELECT * FROM [MiniAdmin].[dbo].[User] WHERE Id = '".$Id."'"  ; 
    $result = sqlsrv_query( $conn, $userquery );  
    $objuser = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC );
    $FAScreenAdd = $objuser['FAScreen_Add'];
    $FAScreen = $objuser['FAScreen'];
    $Multichart_View = $objuser['Multichart_View'];
    $Indicator_Write = $objuser['Indicator_Write'];
    $Trendscanner = $objuser['Trendscanner'];
    $Volumeprofile = $objuser['Volumeprofile'];
    $Simplecreate = $objuser['Simplecreate'];
	
    $adminfascreens = [];
    //$FAScreenAdd = 0;
    if($userid != 'p8@dealing.sg') {

        $fascreenquery = "SELECT usercustomfascreen.* FROM [csvdatasminichart].[dbo].[usercustomfascreen] WHERE userid='462AA229-C722-44BD-BA6E-CA546DD8A885' ORDER BY usercustomfascreen.name ASC"  ; 
        $result = sqlsrv_query( $conn, $fascreenquery );  
        
        while( ($obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) && $FAScreen == 1) {
            $name = $obj['name'];
            $id = $obj['id'];
            // $script = $obj['script'];
            $encrypt = $obj['encrypt'];
            $password = $obj['password'];
            $ele = [
                'id' => $id,
                'name' => $name,
                // 'script' => $script,
                'type1' => $obj['type1'], 'field1' => $obj['field1'], 'operator1' => $obj['operator1'], 'value1' => $obj['value1'], 'criteria1' => $obj['criteria1'],
                'type2' => $obj['type2'], 'field2' => $obj['field2'], 'operator2' => $obj['operator2'], 'value2' => $obj['value2'], 'criteria2' => $obj['criteria2'],
                'type3' => $obj['type3'], 'field3' => $obj['field3'], 'operator3' => $obj['operator3'], 'value3' => $obj['value3'], 'criteria3' => $obj['criteria3'],
                'type4' => $obj['type4'], 'field4' => $obj['field4'], 'operator4' => $obj['operator4'], 'value4' => $obj['value4'], 'criteria4' => $obj['criteria4'],
                'type5' => $obj['type5'], 'field5' => $obj['field5'], 'operator5' => $obj['operator5'], 'value5' => $obj['value5'], 'criteria5' => $obj['criteria5'],
                'type6' => $obj['type6'], 'field6' => $obj['field6'], 'operator6' => $obj['operator6'], 'value6' => $obj['value6'], 'criteria6' => $obj['criteria6'],
                'type7' => $obj['type7'], 'field7' => $obj['field7'], 'operator7' => $obj['operator7'], 'value7' => $obj['value7'], 'criteria7' => $obj['criteria7'],
                'type8' => $obj['type8'], 'field8' => $obj['field8'], 'operator8' => $obj['operator8'], 'value8' => $obj['value8'], 'criteria8' => $obj['criteria8'],
                'type9' => $obj['type9'], 'field9' => $obj['field9'], 'operator9' => $obj['operator9'], 'value9' => $obj['value9'], 'criteria9' => $obj['criteria9'],
                'type10' => $obj['type10'], 'field10' => $obj['field10'], 'operator10' => $obj['operator10'], 'value10' => $obj['value10'], 'criteria10' => $obj['criteria10'],
                'encrypt' => $encrypt,
                'password' => $password
            ];
            array_push($adminfascreens, $ele);
        }

        /*$userquery = "SELECT * FROM [RafflesChart].[dbo].[User] WHERE Id = '".$Id."'"  ; 
            $result = sqlsrv_query( $conn, $userquery );  
            $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC );
            $FAScreenAdd = $obj['FAScreen_Add'];*/

    } else {
    	$FAScreenAdd = 1;
    }

    // $fascreenquery = "SELECT * FROM [csvdatasminichart].[dbo].[userscan]";
    // $fascreenresult = sqlsrv_query( $conn, $fascreenquery );  
    // $predefinedfascreen = [];
    // while( $obj = sqlsrv_fetch_array( $fascreenresult, SQLSRV_FETCH_ASSOC )) {
    //     $name = $obj['name'];
    //     $id = $obj['id'];
    //     $ele = [
    //         'id' => $id,
    //         'name' => $name,
    //     ];
    //     array_push($predefinedfascreen, $ele);
    // }

    $customfascreenquery = "SELECT * FROM [csvdatasminichart].[dbo].[usercustomfascreen] WHERE userid='".$Id."' ORDER BY usercustomfascreen.name ASC";
    $customfascreenresult = sqlsrv_query( $conn, $customfascreenquery );  
    $customfascreen = [];
    while( ($obj = sqlsrv_fetch_array( $customfascreenresult, SQLSRV_FETCH_ASSOC )) && $FAScreen == 1) {

        $name = $obj['name'];
        $id = $obj['id'];
        // $script = $obj['script'];
        $encrypt = $obj['encrypt'];
        $password = $obj['password'];
        $ele = [
            'id' => $id,
            'name' => $name,
            // 'script' => $script,
            'type1' => $obj['type1'], 'field1' => $obj['field1'], 'operator1' => $obj['operator1'], 'value1' => $obj['value1'], 'criteria1' => $obj['criteria1'],
            'type2' => $obj['type2'], 'field2' => $obj['field2'], 'operator2' => $obj['operator2'], 'value2' => $obj['value2'], 'criteria2' => $obj['criteria2'],
            'type3' => $obj['type3'], 'field3' => $obj['field3'], 'operator3' => $obj['operator3'], 'value3' => $obj['value3'], 'criteria3' => $obj['criteria3'],
            'type4' => $obj['type4'], 'field4' => $obj['field4'], 'operator4' => $obj['operator4'], 'value4' => $obj['value4'], 'criteria4' => $obj['criteria4'],
            'type5' => $obj['type5'], 'field5' => $obj['field5'], 'operator5' => $obj['operator5'], 'value5' => $obj['value5'], 'criteria5' => $obj['criteria5'],
            'type6' => $obj['type6'], 'field6' => $obj['field6'], 'operator6' => $obj['operator6'], 'value6' => $obj['value6'], 'criteria6' => $obj['criteria6'],
            'type7' => $obj['type7'], 'field7' => $obj['field7'], 'operator7' => $obj['operator7'], 'value7' => $obj['value7'], 'criteria7' => $obj['criteria7'],
            'type8' => $obj['type8'], 'field8' => $obj['field8'], 'operator8' => $obj['operator8'], 'value8' => $obj['value8'], 'criteria8' => $obj['criteria8'],
            'type9' => $obj['type9'], 'field9' => $obj['field9'], 'operator9' => $obj['operator9'], 'value9' => $obj['value9'], 'criteria9' => $obj['criteria9'],
            'type10' => $obj['type10'], 'field10' => $obj['field10'], 'operator10' => $obj['operator10'], 'value10' => $obj['value10'], 'criteria10' => $obj['criteria10'],
            'encrypt' => $encrypt,
            'password' => $password
        ];
        array_push($customfascreen, $ele);
    }

    $returnVal = [
        'status' => true,
        'data' => $markets,
        'scan' => $predefinedscan,
        'fascreen' => $predefinedscan,
        'ma' => [],
        'oscillator' => [],
        'customscan' => $customscan,
        'customsimplescan' => $customsimplescan,
        'customscanformula' => $customscanformula,
        'scanformula' => $scanformula,
        'formulacategory' => $scanformulacatogory,
        'adminscan' => $adminscans,
        'scanstatus' => $ScannerAdd,
        'customfascreen' => $customfascreen,
        'adminfascreen' => $adminfascreens,
	    'fascreenstatus' => $FAScreenAdd,
        'fascreencanuse' => $FAScreen,
        'multichartcanuse' => $Multichart_View,
        'indicatorcanwrite' => $Indicator_Write,
        'trendscannercanuse' => $Trendscanner,
        'simplecreatecanuse' => $Simplecreate,
        'volumeprofilecanuse' => $Volumeprofile
    ];
    echo json_encode($returnVal);
?>