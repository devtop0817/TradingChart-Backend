<?php
	require __DIR__ . '/vendor/autoload.php';
	use \Firebase\JWT\JWT;

	header('Access-Control-Allow-Origin: *');  
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: *");
	header("Access-Control-Max-Age: 3600");
    	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";
	$connectionOptions = array("Database"=>"csvdatas", "UID"=>"sa", "PWD"=>"Atiks11");
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if( $conn === false ) {
		 die( print_r( sqlsrv_errors(), true));
	}   
	
	if( !isset( $_POST['token'] ) || !isset( $_POST['symbolname'] ) || !isset( $_POST['marketname'] )  || !isset( $_POST['timeinterval'] ) || !isset( $_POST['timeunit'] ) ) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		return;
	}
	$symbolname = $_POST['symbolname'];
	$marketname = $_POST['marketname'];
	$token = $_POST['token'];
	$timeinterval = $_POST['timeinterval'];
	$timeunit = $_POST['timeunit'];
	$secret_key = "stockchart_valentin123!@#";
	try {
		$decoded = JWT::decode($token, $secret_key, array('HS256'));
	}
	catch (UnexpectedValueException $e) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		return;
	}
	
	//echo "SELECT * FROM ".$marketname." WHERE SGX.csymbol='".$symbolname."' ORDER BY SGX.cdate ASC ";
	
	if( $timeunit == 'd' ){
		if(($result = sqlsrv_query($conn,"SELECT top(300) cdate, max(copen) as copen, max(chigh) as chigh, max(clow) as clow, max(cclose) as cclose, max(cvolume) as cvolume FROM ".$marketname." WHERE csymbol='".$symbolname."' AND cvolume != 0 GROUP BY cdate ORDER BY cdate DESC ")) != false){
			$returnrow = ''; 
			while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
				$date = date_format($obj['cdate'],"d-M-y");
				$eachrow = $date.','.$obj['copen'].','.$obj['chigh'].','.$obj['clow'].','.$obj['cclose'].','.$obj['cvolume'];
				
				if($returnrow == '' ){
					$returnrow = $eachrow;
				}else{
					$returnrow = $returnrow."&&<br />".$eachrow;
				}
			}	
			
		 
			
			$returnarray = [ 
				'data' => $returnrow,
				'userinfo' => $decoded
			];
			echo json_encode($returnarray);  
		}else{ 
			$returnValue = [
				"status" => false
			];
			echo json_encode($returnValue);  
		}
	}
	
	else if( $timeunit == 'm' ){
		
		$monthsql = "SELECT *, 
			(SELECT max(copen) FROM ".$marketname." WHERE cdate = newtable.cdate and csymbol='".$symbolname."' )  as copen,
			(SELECT max(cclose) FROM ".$marketname." WHERE cdate = newtable.closedate and csymbol='".$symbolname."' )  as cclose
			FROM(
				SELECT min(cdate) as cdate, max(cdate) as closedate, min(clow) as clow, max(chigh) as chigh, sum(cvolume) as cvolume
				FROM ".$marketname."
				WHERE csymbol='".$symbolname."' AND cvolume != 0
				GROUP BY datepart(yyyy, cdate ), datepart(mm, cdate )
			) AS newtable

			order by cdate asc";
		if(($result = sqlsrv_query($conn, $monthsql)) != false){
			$returnrow = ''; 
			//$returnrow = '27-Jan-16,96.04,96.63,93.34,93.42,133369674'; 
			while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
				$date = date_format($obj['cdate'],"d-M-y");
				
				
				$eachrow = $date.','.$obj['copen'].','.$obj['chigh'].','.$obj['clow'].','.$obj['cclose'].','.$obj['cvolume'];
				
				if($returnrow == '' ){
					$returnrow = $eachrow;
				}else{
					$returnrow = $returnrow."&&<br />".$eachrow;
				} 
			}	 
			
			$returnarray = [ 
				'data' => $returnrow,
				'userinfo' => $decoded
			];
			 
			echo json_encode($returnarray);  
		}else{ 
			$returnValue = [
				"status" => false
			];
			echo json_encode($returnValue);  
		}
	}
	
	else if( $timeunit == 'y' ){
		// if selected  year on toolbar ?? 
		
		$yearsql = "SELECT *, 
		(SELECT max(copen) FROM ".$marketname." WHERE cdate = newtable.cdate and csymbol='".$symbolname."' )  as copen,
		(SELECT max(cclose) FROM ".$marketname." WHERE cdate = newtable.closedate and csymbol='".$symbolname."' )  as cclose
		FROM(
			SELECT min(cdate) as cdate, max(cdate) as closedate, min(clow) as clow, max(chigh) as chigh, sum(cvolume) as cvolume
			FROM ".$marketname." 
			WHERE csymbol='".$symbolname."' AND cvolume != 0
			GROUP BY datepart(yyyy, cdate )
		) AS newtable

		order by cdate asc";
		
		if(($result = sqlsrv_query($conn, $yearsql)) != false){
			
			$returnrow = ''; 
			while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
				$date = date_format($obj['cdate'],"d-M-y");
				
				
				$eachrow = $date.','.$obj['copen'].','.$obj['chigh'].','.$obj['clow'].','.$obj['cclose'].','.$obj['cvolume'];
				
				if($returnrow == '' ){
					$returnrow = $eachrow;
				}else{
					$returnrow = $returnrow."&&<br />".$eachrow;
				} 
			}	 
			
			$returnarray = [ 
				'data' => $returnrow,
				'userinfo' => $decoded
			];
			 
			echo json_encode($returnarray);  
		}else{ 
			$returnValue = [
				"status" => false
			];
			echo json_encode($returnValue);  
		}
	}
	
	else if( $timeunit == 'w' ){
		$weeksql = "SELECT *, 
		(SELECT max(copen) FROM ".$marketname." WHERE cdate = newtable.cdate and csymbol='".$symbolname."' )  as copen,
		(SELECT max(cclose) FROM ".$marketname." WHERE cdate = newtable.closedate and csymbol='".$symbolname."' )  as cclose
		FROM(
			SELECT min(cdate) as cdate, max(cdate) as closedate, min(clow) as clow, max(chigh) as chigh, sum(cvolume) as cvolume
			FROM ".$marketname."
			WHERE csymbol='".$symbolname."' AND cvolume != 0
			GROUP BY datepart(yyyy, cdate ), datepart(mm, cdate ), datepart(wk, cdate)
		) AS newtable

		order by cdate asc";

		if(($result = sqlsrv_query($conn, $weeksql)) != false){
			
			$returnrow = ''; 
			while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
				$date = date_format($obj['cdate'],"d-M-y");
				
				
				$eachrow = $date.','.$obj['copen'].','.$obj['chigh'].','.$obj['clow'].','.$obj['cclose'].','.$obj['cvolume'];
				
				if($returnrow == '' ){
					$returnrow = $eachrow;
				}else{
					$returnrow = $returnrow."&&<br />".$eachrow;
				} 
			}	 
			
			$returnarray = [
				'data' => $returnrow,
				'userinfo' => $decoded
			];
			 
			echo json_encode($returnarray);  
		}else{ 
			$returnValue = [
				"status" => false
			];
			echo json_encode($returnValue);
		}
	}
	
?>