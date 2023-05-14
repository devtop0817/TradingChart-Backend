<?php
 
	
	//change user info
	 
	if( ( isset($_GET['user']) ) && ( isset($_GET['info'])) ){

		$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";  
		$connectionOptions = array("Database"=>"minichart", "UID"=>"sa", "PWD"=>"Atiks11");  
		$conn = sqlsrv_connect($serverName, $connectionOptions);  
		if($conn == false)  
			die(FormatErrors(sqlsrv_errors()));  		
		
		$userquery = "SELECT * FROM [minichart].[dbo].[Users] WHERE useremail = '".$_GET['user']."'" ; 

		$user = sqlsrv_query( $conn, $userquery );  
		if( $user === false )  
		  die( FormatErrors( sqlsrv_errors() ) );  
		if(sqlsrv_has_rows($user))  
		{
			// update
			$targetquery = "UPDATE Users SET s_info = '".$_GET['info']."' WHERE useremail = '".$_GET['user']."' ";
		}else{ 
		
			// insert
			$targetquery = "INSERT INTO Users (useremail, s_info ) VALUES ('".$_GET['user']."', '".$_GET['info']."' )";
		}
		
		echo $targetquery;
		sqlsrv_query($conn, $targetquery); 
		
		sqlsrv_close($conn);
	}else{
		echo 'userinfowrong';
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


?> 