<?php
    require_once('dbconnect.php');
    require_once('global.php');
    date_default_timezone_set("Asia/Seoul");
    $round = $_POST['round'];
    $sql = "SELECT * FROM $_TableName WHERE b_round=$round AND b_endtime > ".strtotime("-1 hour");
    $stmt = sqlsrv_query( $conn, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }

    // echo $stmt;
    $_RoundResult = "";
    while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
        $_RoundResult = $row['b_start'].$row['b_line'].$row['b_end'];
        break;
    }
    echo $_RoundResult;
    sqlsrv_free_stmt( $stmt);
    file_put_contents("e:/New_2020/6June/miniGames/powerladder/one/backend/log.txt", $sql."\n",FILE_APPEND | LOCK_EX);
    file_put_contents("e:/New_2020/6June/miniGames/powerladder/one/backend/log.txt", "  =>  $round 회차결과 $_RoundResult 를 읽었습니다."." --> ".date("Y/m/d H:i:s")."\n",FILE_APPEND | LOCK_EX);
?>