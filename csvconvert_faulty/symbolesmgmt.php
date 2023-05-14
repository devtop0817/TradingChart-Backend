<?php
	include('config.php');
	$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";  
	
	$connectionOptions = array("Database"=>"csvdatas",  "Uid"=>"sa", "PWD"=>"Atiks11");  
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if( $conn === false ) {
		 die( print_r( sqlsrv_errors(), true));
	}   
	
  
	$date = new DateTime( date('Y-m-d')); 
	$year = $date->format("Y");
		
	 
	if(($marketresult = sqlsrv_query($conn,"SELECT * FROM recordhistory where dyear = ".$year." ")) != false){
	
		while( $obj = sqlsrv_fetch_array( $marketresult, SQLSRV_FETCH_ASSOC )) {
			
			$market = $obj['rsymbolname'];
			// remove old data
			$checkdelsql = "Delete from symboles where market='".$market."' "; 
			if( sqlsrv_query($conn, $checkdelsql) === false ){
				FormatErrors(sqlsrv_errors()); 
				exit;
			}else{
				echo 'Checked Old history -';
			}	
			
			// get txt files contents			
			$filepath = $symbolesmain_path."/".$market."/Stocks/StockCode.txt";			
			$filecontents = file_get_contents( $filepath, true);
			
			// make symboles fomart
			$contentsrows = explode("\n", $filecontents);
			$insert_query = "INSERT INTO symboles (symbole, market, company ) VALUES ";
			$value_query = '';
			
			$firstnode = 1;
			foreach( $contentsrows as $ckey => $cval ){
				if( str_replace(' ','', $cval) != ''){
					$fomartsarray = preg_split( '/[\t]/', $cval );
					
					if( isset($fomartsarray[0]) && isset($fomartsarray[1]) ){
						$symbole = $fomartsarray[0];
						$company = $fomartsarray[1];
						
						$company = str_replace("'", "''", $company);
						
						if( $symbole != 'symbol' ){
							if( $firstnode != 1){
								$value_query .= ",";
							}						
							
							$value_query .= "('".$symbole."', '".$market."', '".$company."')";		
							
							if( $firstnode % 999 == 0 ){
								// insert query
								if( sqlsrv_query($conn, $insert_query.$value_query) === false ){
									FormatErrors(sqlsrv_errors()); 
									exit;
								}else{					 
									echo "Symboles inserted(999) with ".$market."\n";
								}
								// fomart values
								$value_query = '';
								$firstnode = 0;
							}
							
							$firstnode++;
						}
					}
				}				
			}
			
			if( sqlsrv_query($conn, $insert_query.$value_query) === false ){
				FormatErrors(sqlsrv_errors()); 
				exit;
			}else{					 
				echo "Symboles inserted with ".$market."\n";
			}	
		}	  
	}else{ 
	}
	
	sqlsrv_close($conn);
	
	
	function getStartAndEndDate($week, $year) {
	  $dto = new DateTime();
	  $dto->setISODate($year, $week);
	  $ret['week_start'] = $dto->format('Y-m-d');
	  $dto->modify('+6 days');
	  $ret['week_end'] = $dto->format('Y-m-d');
	  return $ret;
	}
	
	function FormatErrors( $errors )  
	{   
		echo "Error information: \n";  
	  
		foreach ( $errors as $error )  
		{
			echo "Message: ".$error['message']."\n";  
		}  
	}  
 
?>		