<?php

	$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";  
	
	$connectionOptions = array("Database"=>"csvdatas",  "Uid"=>"sa", "PWD"=>"Atiks11");  
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if( $conn === false ) {
		 die( print_r( sqlsrv_errors(), true));
	}   
	
  
	$date = new DateTime( date('Y-m-d'));
	$cweek = $date->format('W');
	$cyear = $date->format("Y");
		
	
	// market. symbole, weeknumber, year, othervalues....	
	// get marketarray from recordhistory
	for( $yi=2015; $yi<= $cyear; $yi++){
		
		$finalweek =53;
		if( $yi == $cyear ){
			$finalweek = $cweek;
		}
		
		$year = $yi;
		
		for( $wi = 1; $wi<=$finalweek; $wi++){
			
			$week = $wi;
			
			
			if(($marketresult = sqlsrv_query($conn,"SELECT * FROM recordhistory where dyear = ".$year." ")) != false){
			
				while( $obj = sqlsrv_fetch_array( $marketresult, SQLSRV_FETCH_ASSOC )) {
					$market = $obj['rsymbolname'];
					
				
					$checkdelsql = "Delete from weeklydata where year = ".$year." and weeknumber = ".$week." and market='".$market."' ";
				 
					if( sqlsrv_query($conn, $checkdelsql) === false ){
						
					}else{
					//	 echo 'check old history on '.$market.'<br/>';
					}
					
					 
				 
					
					$recordgetquery = "select csymbol, chigh, clow, cvolume, startdate, enddate,
					(select top 1 copen from ".$market." where csymbol = maint.csymbol and cdate = startdate ) as copen,
					(select top 1 cclose from ".$market." where csymbol = maint.csymbol and cdate = enddate ) as cclose
					from (
					select csymbol, max(chigh) as chigh, min(clow) as clow, sum(cvolume) as cvolume, min(cdate) startdate, max(cdate) enddate from
					".$market." where datepart(year, cdate) = ".$year." and datepart(wk, cdate) = ".$week." and cvolume != 0
					group by csymbol) maint";
					  
					//	echo $recordgetquery;
					$symboleindex = 1;
					if(($recordhistorybymarket = sqlsrv_query($conn, $recordgetquery )) != false){
						while( $obj1 = sqlsrv_fetch_array( $recordhistorybymarket, SQLSRV_FETCH_ASSOC )) {
							//insert each symbole weekly result
							$symbol = isset($obj1['csymbol']) ? $obj1['csymbol'] : '' ;					
							$high = isset($obj1['chigh']) ? $obj1['chigh'] : '' ;
							$low = isset($obj1['clow']) ? $obj1['clow'] : '' ;					
							$volume = isset($obj1['cvolume']) ? $obj1['cvolume'] : '' ;
							$open = isset($obj1['copen']) ? $obj1['copen'] : '' ;	
							$close = isset($obj1['cclose']) ? $obj1['cclose'] : '' ;	 
							
							$insertquery = "INSERT INTO weeklydata (symbol, market, weeknumber, year, wopen, whigh, wlow, wclose, wvolume, addeddate) 
							values ('".$symbol."', '".$market."', ".$week.", ".$year.", '".$open."' , '".$high."', '".$low."', '".$close."', '".$volume."', '".date('Y-m-d H:i:s')."')";
							 
							if( sqlsrv_query($conn, $insertquery) === false ){
								FormatErrors(sqlsrv_errors()); 
								exit;
							}else{
								echo $symboleindex.' : '.$market." :: ".$symbol.'...inserting...'."\n";
								$symboleindex ++;
							}
							 
						}
					}

					echo 'finished'.$market." : ".$year." : ".$week."\n";
				}	  
			}else{ 
			}
	
		}
	}
	
	echo "finished all!";
	
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