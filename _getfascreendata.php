<?php
    require __DIR__ . '/vendor/autoload.php';
	use \Firebase\JWT\JWT;

    header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); 

    $serverName = "DESKTOP-D48NNQG\SQLEXPRESS";
	$connectionOptions = array("Database"=>"csvdatasminichart", "UID"=>"sa", "PWD"=>"Atiks11");
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if( $conn === false ) {
		die( print_r( sqlsrv_errors(), true));
    }
    if( !isset( $_POST['token'] ) || !isset( $_POST['market']) || !isset( $_POST['sector'] )) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		exit();
    }
    $token = $_POST['token'];
    $market = $_POST["market"];
    $sector = $_POST["sector"];
    $rs_min = null; $rs_max = null;
    if (isset($_POST['rs_min'])) {
        $rs_min = $_POST['rs_min'];
    }
    if (isset($_POST['rs_max'])) {
        $rs_max = $_POST['rs_max'];
    }
    $es_min = null; $es_max = null;
    if (isset($_POST['es_min'])) {
        $es_min = $_POST['es_min'];
    }
    if (isset($_POST['es_max'])) {
        $es_max = $_POST['es_max'];
    }
    $cs_min = null; $cs_max = null;
    if (isset($_POST['cs_min'])) {
        $cs_min = $_POST['cs_min'];
    }
    if (isset($_POST['cs_max'])) {
        $cs_max = $_POST['cs_max'];
    }
    $ds_min = null; $ds_max = null;
    if (isset($_POST['ds_min'])) {
        $ds_min = $_POST['ds_min'];
    }
    if (isset($_POST['ds_max'])) {
        $ds_max = $_POST['ds_max'];
    }
    $bps_min = null; $bps_max = null;
    if (isset($_POST['bps_min'])) {
        $bps_min = $_POST['bps_min'];
    }
    if (isset($_POST['bps_max'])) {
        $bps_max = $_POST['bps_max'];
    }
    $tds_min = null; $tds_max = null;
    if (isset($_POST['tds_min'])) {
        $tds_min = $_POST['tds_min'];
    }
    if (isset($_POST['tds_max'])) {
        $tds_max = $_POST['tds_max'];
    }
    $dr_min = null; $dr_max = null;
    if (isset($_POST['dr_min'])) {
        $dr_min = $_POST['dr_min'];
    }
    if (isset($_POST['dr_max'])) {
        $dr_max = $_POST['dr_max'];
    }
    $eps_min = null; $eps_max = null;
    if (isset($_POST['eps_min'])) {
        $eps_min = $_POST['eps_min'];
    }
    if (isset($_POST['eps_max'])) {
        $eps_max = $_POST['eps_max'];
    }
    $ta_min = null; $ta_max = null;
    if (isset($_POST['ta_min'])) {
        $ta_min = $_POST['ta_min'];
    }
    if (isset($_POST['ta_max'])) {
        $ta_max = $_POST['ta_max'];
    }
    $tl_min = null; $tl_max = null;
    if (isset($_POST['tl_min'])) {
        $tl_min = $_POST['tl_min'];
    }
    if (isset($_POST['tl_max'])) {
        $tl_max = $_POST['tl_max'];
    }
    $pe_min = null; $pe_max = null;
    if (isset($_POST['pe_min'])) {
        $pe_min = $_POST['pe_min'];
    }
    if (isset($_POST['pe_max'])) {
        $pe_max = $_POST['pe_max'];
    }
    $pb_min = null; $pb_max = null;
    if (isset($_POST['pb_min'])) {
        $pb_min = $_POST['pb_min'];
    }
    if (isset($_POST['pb_max'])) {
        $pb_max = $_POST['pb_max'];
    }
    $pf_min = null; $pf_max = null;
    if (isset($_POST['pf_min'])) {
        $pf_min = $_POST['pf_min'];
    }
    if (isset($_POST['pf_max'])) {
        $pf_max = $_POST['pf_max'];
    }
    $sr_min = null; $sr_max = null;
    if (isset($_POST['sr_min'])) {
        $sr_min = $_POST['sr_min'];
    }
    if (isset($_POST['sr_max'])) {
        $sr_max = $_POST['sr_max'];
    }
    $dy_min = null; $dy_max = null;
    if (isset($_POST['dy_min'])) {
        $dy_min = $_POST['dy_min'];
    }
    if (isset($_POST['dy_max'])) {
        $dy_max = $_POST['dy_max'];
    }
    $ns_min = null; $ns_max = null;
    if (isset($_POST['ns_min'])) {
        $ns_min = $_POST['ns_min'];
    }
    if (isset($_POST['ns_max'])) {
        $ns_max = $_POST['ns_max'];
    }
    $gn_min = null; $gn_max = null;
    if (isset($_POST['gn_min'])) {
        $gn_min = $_POST['gn_min'];
    }
    if (isset($_POST['gn_max'])) {
        $gn_max = $_POST['gn_max'];
    }
    $epv_min = null; $epv_max = null;
    if (isset($_POST['epv_min'])) {
        $epv_min = $_POST['epv_min'];
    }
    if (isset($_POST['epv_max'])) {
        $epv_max = $_POST['epv_max'];
    }
    $bs_min = null; $bs_max = null;
    if (isset($_POST['bs_min'])) {
        $bs_min = $_POST['bs_min'];
    }
    if (isset($_POST['bs_max'])) {
        $bs_max = $_POST['bs_max'];
    }
    $beta_min = null; $beta_max = null;
    if (isset($_POST['beta_min'])) {
        $beta_min = $_POST['beta_min'];
    }
    if (isset($_POST['beta_max'])) {
        $beta_max = $_POST['beta_max'];
    }
    $userid = '';
    $Id = '';
    $secret_key = "stockchart_valentin123!@#";
    try {
        $decoded = JWT::decode($token, $secret_key, array('HS256'));
        $userid = $decoded->Email;
        $Id = $decoded->ID;
	}
	catch (UnexpectedValueException $e) {
		$returnValue = [
            "status" => false,
            "message" => 1
		];
		echo json_encode($returnValue);
		exit();
    }

    if($userid == '') {
        $returnValue = [
            "status" => false,
            "message" => 1
		];
		echo json_encode($returnValue);
		exit();
    }

    $userquery = "SELECT * FROM [MiniAdmin].[dbo].[User] WHERE Id = '".$Id."'"  ; 
    $resultuser = sqlsrv_query( $conn, $userquery );  
    $objuser = sqlsrv_fetch_array( $resultuser, SQLSRV_FETCH_ASSOC );
    $FAScreenAdd = $objuser['FAScreen_Add'];
    $FAScreen = $objuser['FAScreen'];

    $market1 = $market;
    if ($market == "SGX") {
		$market1 = "SGX";
    } else if ($market == "HKEX") {
		$market1 = "HKSE";
    } else if ($market == "NASD") {
		$market1 = "NAS";
    } else if ($market == "NYSE") {
		$market1 = "NYSE";
    } else if ($market == "SZSE") {
		$market1 = "SZSE";
    } else if ($market == "SSE") {
        $market1 = "SHSE";
    }
    if ($sector == "All")
        $indicatorquery = "SELECT * FROM [FinicalDB].[dbo].[Market] WHERE symbol like '" . $market1 . "%' AND fiscal_period='TTM/current'";
    else
        $indicatorquery = "SELECT * FROM [FinicalDB].[dbo].[Market] WHERE symbol like '" . $market1 . "%' AND industry1='" . $sector . "' AND fiscal_period='TTM/current'";
    
    if ($rs_min !== null && $rs_max !== null)
        $indicatorquery .= " AND CAST(Revenue_Share as float)>=" . $rs_min . " AND CAST(Revenue_Share as float)<=" . $rs_max;
    if ($es_min !== null && $es_max !== null)
        $indicatorquery .= " AND CAST(Earnings_Share as float)>=" . $es_min . " AND CAST(Earnings_Share as float)<=" . $es_max;
    if ($cs_min !== null && $cs_max !== null)
        $indicatorquery .= " AND CAST(Cash_Share as float)>=" . $cs_min . " AND CAST(Cash_Share as float)<=" . $cs_max;
    if ($ds_min !== null && $ds_max !== null)
        $indicatorquery .= " AND CAST(Dividends_Share as float)>=" . $ds_min . " AND CAST(Dividends_Share as float)<=" . $ds_max;
    if ($bps_min !== null && $bps_max !== null)
        $indicatorquery .= " AND CAST(Book_Share as float)>=" . $bps_min . " AND CAST(Book_Share as float)<=" . $bps_max;
    if ($tds_min !== null && $tds_max !== null)
        $indicatorquery .= " AND CAST(Total_Debt_Share as float)>=" .$tds_min . " AND CAST(Total_Debt_Share as float)<=" . $tds_max;
    if ($dr_min !== null && $dr_max !== null)
        $indicatorquery .= " AND CAST(Dividend_Ratio as float)>=" . $dr_min . " AND CAST(Dividend_Ratio as float)<=" . $dr_max;
    if ($eps_min !== null && $eps_max !== null)
        $indicatorquery .= " AND CAST(EPS as float)>=" . $eps_min . " AND CAST(EPS as float)<=" . $eps_max;
    if ($ta_min !== null && $ta_max !== null)
        $indicatorquery .= " AND CAST(Total_Assets as float)>=" . $ta_min . " AND CAST(Total_Assets as float)<=" . $ta_max;
    if ($tl_min !== null && $tl_max !== null)
        $indicatorquery .= " AND CAST(Total_Liabilities as float)>=" . $tl_min . " AND CAST(Total_Liabilities as float)<=" . $tl_max;
    if ($pe_min !== null && $pe_max !== null)
        $indicatorquery .= " AND CAST(PE_Ratio as float)>=" . $pe_min . " AND CAST(PE_Ratio as float)<=" . $pe_max;
    if ($pb_min !== null && $pb_max !== null)
        $indicatorquery .= " AND CAST(PB_Ratio as float)>=" . $pb_min . " AND CAST(PB_Ratio as float)<=" . $pb_max;
    if ($pf_min !== null && $pf_max !== null)
        $indicatorquery .= " AND CAST(Price_Flow as float)>=" . $pf_min . " AND CAST(Price_Flow as float)<=" . $pf_max;
    if ($sr_min !== null && $sr_max !== null)
        $indicatorquery .= " AND CAST(Shiller_Ratio as float)>=" . $sr_min . " AND CAST(Shiller_Ratio as float)<=" . $sr_max; 
    if ($dy_min !== null && $dy_max !== null)
        $indicatorquery .= " AND CAST(Dividend_Yield as float)>=" . $dy_min . " AND CAST(Dividend_Yield as float)<=" . $dy_max;
    if ($ns_min !== null && $ns_max !== null)
        $indicatorquery .= " AND CAST(Net_Share as float)>=" . $ns_min . " AND CAST(Net_Share as float)<=" . $ns_max;
    if ($gn_min !== null && $gn_max !== null)
        $indicatorquery .= " AND CAST(Graham_Number as float)>=" . $gn_min . " AND CAST(Graham_Number as float)<=" . $gn_max;
    if ($epv_min !== null && $epv_max !== null)
        $indicatorquery .= " AND CAST(Earnings_Power_Value as float)>=" . $epv_min . " AND CAST(Earnings_Power_Value as float)<=" . $epv_max;
    if ($bs_min !== null && $bs_max !== null)
        $indicatorquery .= " AND CAST(Beneish_Score as float)>=" . $bs_min . " AND CAST(Beneish_Score as float)<=" . $bs_max;
    if ($beta_min !== null && $beta_max !== null)
        $indicatorquery .= " AND CAST(Beta as float)>=" . $beta_min . " AND CAST(Beta as float)<=" . $beta_max;
    $result = sqlsrv_query($conn, $indicatorquery);
    if($result == false) {
        echo $indicatorquery;
        exit;
    }
    
    $indicators = [];
    while(($obj = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) && $FAScreen == 1) {
        $underline_pos = strpos($obj['symbol'], '_');
        if ($underline_pos !== false) {
            $market = substr($obj['symbol'], 0, $underline_pos);
            $symbol = substr($obj['symbol'], $underline_pos + 1);
            $company = $obj['industry1'];
            $ele = [
                'exchange' => $market,
                'company' => $company,
                'symbol' => $symbol,
                'Revenue_Share' => $obj['Revenue_Share'],
                'Earnings_Share' => $obj['Earnings_Share'],
                'Cash_Share' => $obj['Cash_Share'],
                'Dividends_Share' => $obj['Dividends_Share'],
                'Book_Share' => $obj['Book_Share'],
                'Total_Debt_Share' => $obj['Total_Debt_Share'],
                'Dividend_Ratio' => $obj['Dividend_Ratio'],
                'EPS' => $obj['EPS'],
                'Total_Assets' => $obj['Total_Assets'],
                'Total_Liabilities' => $obj['Total_Liabilities'],
                'PE_Ratio' => $obj['PE_Ratio'],
                'PB_Ratio' => $obj['PB_Ratio'],
                'Price_Flow' => $obj['Price_Flow'],
                'Shiller_Ratio' => $obj['Shiller_Ratio'],
                'Dividend_Yield' => $obj['Dividend_Yield'],
                'Net_Share' => $obj['Net_Share'],
                'Graham_Number' => $obj['Graham_Number'],
                'Earnings_Power_Value' => $obj['Earnings_Power_Value'],
                'Beneish_Score' => $obj['Beneish_Score'],
                'Beta' => $obj['Beta'],
                'fiscal_period' => $obj['fiscal_period'],
                'industry2' => $obj['industry2']
            ];
            array_push($indicators, $ele);
        }
    }
    $returnVal = [
        'status' => true,
        'data' => $indicators
    ];
    echo json_encode($returnVal);
?>