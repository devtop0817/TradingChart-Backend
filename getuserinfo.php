<?php
 
	
	//connect mssql

	$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";  
	$connectionOptions = array("Database"=>"csvdatasminichart", "UID"=>"sa", "PWD"=>"Atiks11", "CharacterSet" => "UTF-8");  
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if($conn == false)  
		die(FormatErrors(sqlsrv_errors()));  		
	
	$userquery = "SELECT * FROM [minichart].[dbo].[Users] WHERE useremail = '".$_SESSION['user']."'" ; 

	$user = sqlsrv_query( $conn, $userquery );  
	if( $user === false )  
	  die( FormatErrors( sqlsrv_errors() ) );  
	if(sqlsrv_has_rows($user))  
	{
		if(sqlsrv_fetch( $user )){
          $s_info = sqlsrv_get_field( $user, 2 ); 
        }
		
	}else{ 
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