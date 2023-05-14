<?php
    require_once('dbconnect.php');
    require_once('global.php');

    date_default_timezone_set("Asia/Seoul");
    
    $sql = "SELECT * FROM $_TableName WHERE proc_status=1 AND b_endtime > ".strtotime("-1 days")." ORDER BY b_endtime DESC";
    $stmt = sqlsrv_query( $conn, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }

    // echo $stmt;
    $history_array = [];
    while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
        array_push($history_array, $row);
    }
    
    sqlsrv_free_stmt( $stmt);

    echo json_encode(array("history"=>$history_array, "time"=>date("Y-m-d h:i:s a")));
?>