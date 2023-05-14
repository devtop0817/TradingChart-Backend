<?php
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	
	
	require __DIR__ . '/vendor/autoload.php';
	use \Firebase\JWT\JWT;
	$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";  
	$connectionOptions = array("Database"=>"MiniAdmin", "UID"=>"sa", "PWD"=>"Atiks11");  
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	
	if( ( isset( $_POST['email'] ) && ($_POST['type'] == 'social' ) ) || ( isset( $_POST['email'] ) && isset( $_POST['pass'] )  ) ){
		
		$email = $_POST['email']; 
		$pass = $_POST['pass'];
		$type = $_POST['type'];
		
		if($conn == false)  
			die(FormatErrors(sqlsrv_errors()));  		
		
		if( $type == 'manual' ){
			$userquery = "SELECT ut.Id as Id,ut.Login as Login, ut.Password as Password, anr.Name as rtype FROM [MiniAdmin].[dbo].[User] AS ut";
			$userquery .= " LEFT JOIN [MiniAdmin].[dbo].[AspNetUserRoles] as anur ON ut.Id = anur.UserId";
			$userquery .= " LEFT JOIN [MiniAdmin].[dbo].[AspNetRoles] as anr ON anr.Id = anur.RoleId";
			$userquery .= " WHERE ut.Login = '".$email."' AND ut.Password = '".$pass."' "  ;
			
		}else{
			$userquery = "SELECT ut.Id as Id,ut.Login as Login, ut.Password as Password, anr.Name as rtype FROM [MiniAdmin].[dbo].[User] AS ut";
			$userquery .= " LEFT JOIN [MiniAdmin].[dbo].[AspNetUserRoles] as anur ON ut.Id = anur.UserId";
			$userquery .= " LEFT JOIN [MiniAdmin].[dbo].[AspNetRoles] as anr ON anr.Id = anur.RoleId";
			$userquery .= " WHERE Login = '".$email."'"  ; 
		}
		
		
    
		$user = sqlsrv_query( $conn, $userquery );  
		if( $user === false )  
		  die( FormatErrors( sqlsrv_errors() ) );  
		if(sqlsrv_has_rows($user))  
		{
			$userinfo = sqlsrv_fetch_array($user);
			$email = $userinfo["Login"];
			$pass = $userinfo["Password"];
			$id = $userinfo["Id"];
			session_set_cookie_params(7200,"/");
			session_start();

		   	$_SESSION['user'] = $email;
		   	$siteUrl = 'https://minichart.info';
			$secret_key = "stockchart_valentin123!@#";
			//print_r( $_SESSION );
			
			$isspecialmember = 0;
			if( isset($userinfo['rtype']) && ( $userinfo['rtype'] == 'SpecialMember' ) ){
				$isspecialmember = 1;
			}
			$token = getToken(10);
			$userinfo = [
				"Email" => $email,
				"Password" => $pass,
				"ID" => $id,
				"token" => $token
			];
			$jwt = JWT::encode($userinfo, $secret_key);
			$returnVal = [
				"status" => true,
				"token" => $jwt,
				"isspecial" => $isspecialmember
			];
			$_SESSION['token'] = $jwt;
			// delete previous login 
			$insertQuery = "UPDATE [MiniAdmin].[dbo].[UserLogins] SET [token]=NULL WHERE email='".$email."'";
			$userhistory = sqlsrv_query( $conn, $insertQuery );
			
			// insert Login history
			$insertQuery = "INSERT INTO [MiniAdmin].[dbo].[UserLogins] ([email],[logindate],[token]) VALUES ('".$email."' ,'".date('Y-m-d H:i:s')."', '" . $token . "' ) ";
		    $userhistory = sqlsrv_query( $conn, $insertQuery );
		 
			
			echo json_encode($returnVal);
		} else {
			$returnValue = [
				"status" => false,
				"message" => "Login Fail"
			];
			echo json_encode($returnValue);
		}
	} else {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
	}

	
	function FormatErrors( $errors )  
	{  
		/* Display errors. */  
		echo "Error information: <br/>";  
	
		foreach ( $errors as $error )  
		{  
			// echo "SQLSTATE: ".$error['SQLSTATE']."<br/>";  
			// echo "Code: ".$error['code']."<br/>";  
			echo "Message: ".$error['message']."<br/>";  
		}  
	}  

	// Generate token
	function getToken($length){
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet); // edited
	
		for ($i=0; $i < $length; $i++) {
		$token .= $codeAlphabet[random_int(0, $max-1)];
		}
	
		return $token;
  	}
?>
