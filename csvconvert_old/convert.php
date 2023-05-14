<?php
include('config.php');
$serverName = "WIN-4HNQPHJG3FG\SQLEXPRESS";  
$connectionOptions = array("Database"=>"csvdatas", "Uid"=>"", "PWD"=>"");  
$conn = sqlsrv_connect($serverName, $connectionOptions);  
if($conn == false)  
    die(FormatErrors(sqlsrv_errors()));  

foreach( $directory as $rootsymbolkey => $dirval ){
	
	$keyarray = explode('_', $rootsymbolkey );
	$rootsymbolkey = isset($keyarray[0]) ? $keyarray[0] : $rootsymbolkey;
	
  $tablename = $rootsymbolkey;
  $isexisttablequery = "SELECT COUNT(table_name) FROM information_schema.tables WHERE table_name = '".$tablename."'";
  $isexist = sqlsrv_query($conn, $isexisttablequery);  
  if( $isexist !== false ) {    
    if(sqlsrv_fetch( $isexist )){
          $count_number = sqlsrv_get_field( $isexist, 0 ); 
      if( $count_number == 0 ){
        echo 'Creating New Table...<br />';
        $createtablequery = "CREATE TABLE ".$tablename." (
          cid int PRIMARY KEY IDENTITY,
          csymbol varchar(255),
          cdate date,
          copen float,
          chigh float,
          clow float,
          cclose float,
          cvolume float,
          market varchar(255),
          cfilename varchar(255),  
        );";
        
        if( sqlsrv_query($conn, $createtablequery) === false ){
          die(FormatErrors(sqlsrv_errors())); 
        }      
      }else{
      echo 'Table exist already';
      }
    }
  }else{
    die( FormatErrors( sqlsrv_errors() ) );
  }
  
  $files = array_diff(scandir($dirval), array('.', '..'));
  foreach($files as $filekeys => $filevalues ){
	  
	  echo $filevalues;
	  echo "\n";
	  
  $directoryyear = substr( $dirval, -4 );
    
    $lastupdateddate = $default_last_updated_date;
    $lastupdatehisquery = "SELECT * FROM recordhistory WHERE rsymbolname = '".$rootsymbolkey."' AND dyear = '".$directoryyear."' "  ; 
    
    $getlastupdatehistory = sqlsrv_query( $conn, $lastupdatehisquery );  
    if( $getlastupdatehistory === false )  
      die( FormatErrors( sqlsrv_errors() ) );  
    if(sqlsrv_has_rows($getlastupdatehistory))  
    {
      if(sqlsrv_fetch( $getlastupdatehistory )){
        $lastupdateddate = sqlsrv_get_field( $getlastupdatehistory, 3 );
 
		
        $lastupdateddate = date_format($lastupdateddate, 'Y-m-d');
		 
      } 
    }
	
    $csvdate = getDateByFilename($filevalues); 
    if( strtotime($csvdate) >= strtotime($lastupdateddate)){
      // read csv file and insert
      echo 'New File : '.$filevalues;
      echo ' Converting ...'."\n";
      $row = 1;
      if (($handle = fopen( $dirval.'/'.$filevalues, "r")) !== FALSE) {
		$checkflag = 0;
		echo 'Started with'.$filevalues."\n";
        while (($data = fgetcsv($handle)) !== FALSE) {

          if( ($data[0] == 'symbol') || ($data[1] == 'date') ){

          }else{ 
            // insert data
            $csymbol = (isset($data[0])) ? $data[0] : 'unknow';
            $cdate = (isset($data[1])) ? $data[1] : date('Y-m-d');
            $copen = (isset($data[2])) ? $data[2] : 0;
            $chigh = (isset($data[3])) ? $data[3] : 0;
            $clow = (isset($data[4])) ? $data[4] : 0;
            $cclose = (isset($data[5])) ? $data[5] : 0;
            $cvolume = (isset($data[6])) ? $data[6] : 0;
            
            $market = $rootsymbolkey;
            $cfilename = $filevalues; 

            /* $newquery = "INSERT INTO ".$tablename." (csymbol, cdate, copen, chigh, clow, cclose, cvolume, market, cfilename) 
            VALUES ('".$csymbol."', '".$cdate."', '".$copen."', '".$chigh."', '".$clow."', '".$cclose."', '".$cvolume."','".$market."', '".$cfilename."')
            ON DUPLICATE KEY UPDATE csymbol = '".$csymbol."', cdate = '".$cdate."', copen = '".$copen."', chigh = '".$chigh."', clow = '".$clow."', cclose = '".$cclose."', cvolume = '".$cvolume."', cfilename = '".$cfilename."' ";
			 */
			
			
			if( strtotime($cdate) > strtotime($lastupdateddate)){
				
				if( $checkflag == 0 ){
					$checkdelsql = "Delete from ".$tablename." WHERE csymbol = '".$csymbol."' AND cdate = '".$cdate."'  ";
					
					if( sqlsrv_query($conn, $checkdelsql) === false ){
				
					}else{
						$checkflag = 1;
						echo  " CHEKCED TABLE .\n";
					}
					
				}
			/* 	$newquery = "INSERT INTO ".$tablename." (csymbol, cdate, copen, chigh, clow, cclose, cvolume, market, cfilename) VALUES ('".$csymbol."', '".$cdate."', '".$copen."', '".$chigh."', '".$clow."', '".$cclose."', '".$cvolume."','".$market."', '".$cfilename."')"; */
			}else{
/* 				echo 'UPORIN...'.$cfilename."\n";
				$newquery = " 
				IF EXISTS (SELECT * FROM ".$tablename." WHERE csymbol = '".$csymbol."' AND cdate = '".$cdate."' )
					UPDATE ".$tablename." SET copen = '".$copen."', chigh = '".$chigh."', clow = '".$clow."', cclose = '".$cclose."' , cvolume = '".$cvolume."' WHERE csymbol = '".$csymbol."' AND cdate = '".$cdate."' 
				ELSE
					INSERT INTO ".$tablename." (csymbol, cdate, copen, chigh, clow, cclose, cvolume, market, cfilename) VALUES ('".$csymbol."', '".$cdate."', '".$copen."', '".$chigh."', '".$clow."', '".$cclose."', '".$cvolume."','".$market."', '".$cfilename."')"; */

			}
			
			
			$newquery = "INSERT INTO ".$tablename." (csymbol, cdate, copen, chigh, clow, cclose, cvolume, market, cfilename) VALUES ('".$csymbol."', '".$cdate."', '".$copen."', '".$chigh."', '".$clow."', '".$cclose."', '".$cvolume."','".$market."', '".$cfilename."')";
			
			if( sqlsrv_query($conn, $newquery) === false ){
				
			}else{
			}
			
            $row ++;
       
          }
        }
        // update or insert the recordhistroy table
        if($lastupdateddate == $default_last_updated_date){
          $recordhistoryquery = "INSERT INTO recordhistory (rsymbolname, lastfilename, lastdate, recorddate, dyear) VALUES ('".$rootsymbolkey."', '".$filevalues."', '".$csvdate."', '".date('Y-m-d H:i:s')."', '".$directoryyear."')";
        }else{
          $recordhistoryquery = "UPDATE recordhistory SET lastfilename = '".$filevalues."', lastdate = '".$csvdate."', recorddate = '".date('Y-m-d H:i:s')."' WHERE rsymbolname = '".$rootsymbolkey."' AND dyear = '".$directoryyear."' ";
        }

        sqlsrv_query($conn, $recordhistoryquery);
        echo 'Successfully Done!'.$filevalues."\n";
        fclose($handle);
      }
    }else{

    }
  } 
}

echo 'Successfully Finished!';

sqlsrv_close($conn);
 
function getDateByFilename($filename){
  $datestring = explode('_', $filename);
  if(isset($datestring[1])){
    return substr($datestring[1], 0, 4).'-'.substr($datestring[1], 4, 2).'-'.substr($datestring[1], 6, 2);
  }else{
    return '0000-00-00';
  }
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