<?php
    require_once('dbconnect.php');
    require_once('global.php');

    date_default_timezone_set("Asia/Seoul");
    
    $sql = "SELECT * FROM TB_GameList WHERE name='monkeyladder' AND time_cycle=1";
    $stmt = sqlsrv_query( $conn, $sql );
    if( $stmt === false) {
        die( print_r( sqlsrv_errors(), true) );
    }

    $gamePlayingStatus = 0;
    while( $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ) {
        $gamePlayingStatus = $row['playing_status'];
        break;
    }
    
    sqlsrv_free_stmt( $stmt);

    echo $gamePlayingStatus;
?>