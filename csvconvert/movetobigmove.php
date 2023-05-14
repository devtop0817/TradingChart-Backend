<?php

$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";

$connectionOptions = array("Database" => "csvdatas", "Uid" => "sa", "PWD" => "Atiks11");
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

$date = new DateTime(date('Y-m-d'));
$year = $date->format("Y");

if (($marketresult = sqlsrv_query($conn, "SELECT * FROM recordhistory where dyear = " . $year . " ")) != false) {

    while ($obj = sqlsrv_fetch_array($marketresult, SQLSRV_FETCH_ASSOC)) {
        $market = $obj['rsymbolname'];
        $latestmovedtobmtable = $obj['movedbmdate'];
        $marketlastdate = $obj['lastdate'];

        $marketlastdate = date_format($marketlastdate, 'Y-m-d');
        $latestmovedtobmtable = date_format($latestmovedtobmtable, 'Y-m-d');

        $latestmoveTimestamp = strtotime($latestmovedtobmtable);
        $marketlatestTimestamp = strtotime($marketlastdate);

        // if ($latestmoveTimestamp >= $marketlatestTimestamp) {
            // check and remove duplicated date

            $checkdelsql = "Delete from BigMove where datadate >= '" . $marketlastdate . "' and market='" . $market . "' ";

            if (sqlsrv_query($conn, $checkdelsql) === false) {
                FormatErrors(sqlsrv_errors());
                exit;
            } else {
                echo 'Checked Old history -';
            }

            // Get data && insert new data
            $getandinsertquery = "INSERT INTO BigMove (market,symbol,VolumeSpike,timeunit, datetime, datadate, company )
										SELECT '" . $market . "' market, csymbol as symbol,(cclose * cvolume) as VolumeSpike, 'd' as timeunit,
										'" . $marketlastdate . "' as datetime, '" . $marketlastdate . "' datadate, symboles.company as company
										FROM " . $market . " as marketable
										LEFT OUTER JOIN symboles on symboles.symbole = marketable.csymbol and symboles.market = marketable.market
										where marketable.cdate = '" . $marketlastdate . "'";

            if (sqlsrv_query($conn, $getandinsertquery) === false) {
                FormatErrors(sqlsrv_errors());
                exit;
            } else {
                // update recordhistory table
                $recordhistoryquery = "UPDATE recordhistory SET movedbmdate = '" . $marketlastdate . "' WHERE rsymbolname = '" . $market . "' AND dyear = '" . $year . "' ";
                if (sqlsrv_query($conn, $recordhistoryquery) === false) {
                    FormatErrors(sqlsrv_errors());
                    exit;
                } else {
                    // update recordhistory table
                    echo 'Updated Record history';
                }

            }
        // } else {
        //     echo 'There is no any new data - ';
        // }

        echo 'finished -' . $market . "\n";
    }
} else {
}

sqlsrv_close($conn);

function getStartAndEndDate($week, $year)
{
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $ret['week_start'] = $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $ret['week_end'] = $dto->format('Y-m-d');
    return $ret;
}

function FormatErrors($errors)
{
    echo "Error information: \n";

    foreach ($errors as $error) {
        echo "Message: " . $error['message'] . "\n";
    }
}
