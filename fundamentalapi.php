<?php
	require __DIR__ . '/vendor/autoload.php';
	use \Firebase\JWT\JWT;

	header('Access-Control-Allow-Origin: *');  
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: *");
	header("Access-Control-Max-Age: 3600");
    	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";
	$connectionOptions = array("Database"=>"FinicalDB", "UID"=>"sa", "PWD"=>"Atiks11");
	$conn = sqlsrv_connect($serverName, $connectionOptions);  
	if( $conn === false ) {
		 die( print_r( sqlsrv_errors(), true));
	}
	
	if( !isset( $_POST['token'] ) || !isset( $_POST['timeinterval'] ) || !isset( $_POST['timeunit'] ) ) {
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);
		return;
	}

	$symbolname = "D05";
	if (isset( $_POST['symbolname'] )) {
		$symbolname = $_POST['symbolname'];
	}
	$marketname = "SGX";
	if (isset( $_POST['marketname'] ))  {
		$marketname = $_POST['marketname'];
	}
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
	
	$market1 = $marketname;
	$symbol1 = $symbolname;
	if ($marketname == "SGX") {
		$market1 = "SGX";
		$symbol1 = $symbolname;
	} else if ($marketname == "HKEX") {
		$market1 = "HKSE";
		$symbol1 = "0" . substr($symbolname, 0, 4);
	} else if ($marketname == "NASD") {
		$market1 = "NAS";
		$symbol1 = $symbolname;
	} else if ($marketname == "NYSE") {
		$market1 = "NYSE";
		$symbol1 = $symbolname;
	} else if ($marketname == "SZSE") {
		$market1 = "SZSE";
		$symbol1 = "0" . substr($symbolname, 0, 5);
	} else if ($marketname == "SSE") {
		$market1 = "SHSE";
		$symbol1 = "0" . substr($symbolname, 0, 6);
	}

	if(($result = sqlsrv_query($conn,"SELECT * FROM Market WHERE symbol='".$market1."_".$symbol1."' ORDER BY id ASC")) != false){
		$returnrow = ''; 
		while( $obj = sqlsrv_fetch_array( $result, SQLSRV_FETCH_ASSOC )) {
			$eachrow = $obj['Revenue_Share'].','.
				$obj['Earnings_Share'].','.
				$obj['Cash_Share'].','.
				$obj['Dividends_Share'].','.
				$obj['Book_Share'].','.
				$obj['Total_Debt_Share'].','.
				$obj['Dividend_Ratio'].','.
				$obj['EPS'].','.
				$obj['Total_Assets'].','.
				$obj['Total_Liabilities'].','.
				$obj['PE_Ratio'].','.
				$obj['PB_Ratio'].','.
				$obj['Price_Flow'].','.
				$obj['Shiller_Ratio'].','.
				$obj['Dividend_Yield'].','.
				$obj['Net_Share'].','.
				$obj['Graham_Number'].','.
				$obj['Earnings_Power_Value'].','.
				$obj['Beneish_Score'].','.
				$obj['Beta'].','.
				$obj['fiscal_period'].','.
				$obj['symbol'].','.
				$obj['industry1'].','.
				$obj['industry2'].','.
				$obj['stock_title'].','.
				$obj['market_cap'].','.
				$obj['shares_out'].','.
				$obj['ps_ratio'].','.
				$obj['Revenue_Growth_5_percent'].','.
				$obj['Revenue_Growth_10_percent'].','.
				$obj['Revenue_Growth_12_percent'].','.
				$obj['EBITDA_Growth_5_percent'].','.
				$obj['EBITDA_Growth_10_percent'].','.
				$obj['EBITDA_Growth_12_percent'].','.
				$obj['Free_Cash_Flow_growth_5_percent'].','.
				$obj['Free_Cash_Flow_growth_10_percent'].','.
				$obj['Free_Cash_Flow_growth_12_percent'].','.
				$obj['Book_Value_Growth_5_percent'].','.
				$obj['Book_Value_Growth_10_percent'].','.
				$obj['Book_Value_Growth_12_percent'].','.
				$obj['ROE_percent'].','.
				$obj['ROE_percent_adjusted'].','.
				$obj['ROA_percent'].','.
				$obj['Debt_to_equity'].','.
				$obj['Equity_to_Asset'].','.
				$obj['Debt_to_asset'].','.
				$obj['Asset_turnover'].','.
				$obj['Total_Revenue'].','.
				$obj['Pre_Tax_income'].','.
				$obj['Net_Income'].','.
				$obj['Preferred_Dividends'].','.
				$obj['EPS_Basic'].','.
				$obj['Cash_Equivalent'].','.
				$obj['Total_equity'].','.
				$obj['Stock_issuance'].','.
				$obj['Stock_repurchase'].','.
				$obj['Cash_Flow_Div'].','.
				$obj['Price_Tangible_book'].','.
				$obj['Price_OP_Flow'].','.
				$obj['Enterprise_Value'].','.
				$obj['Intrinsic_Value_projected'].','.
				$obj['Median_PS_Value'].','.
				$obj['Peter_Lynch_FV'].','.
				$obj['Altman_Z_Score'].','.
				$obj['Piotroski_F_Score'].','.
				$obj['Sloan_Ratio'].','.
				$obj['Shares_BuyBack_Ratio'].','.
				$obj['Fiscal_year'];
			
			if($returnrow == '' ){
				$returnrow = $eachrow;
			}else{
				$returnrow = $returnrow."&&".$eachrow;
			}
		}	

		$returnarray = [ 
			'data' => $returnrow,
			'userinfo' => $decoded
		];
		echo json_encode($returnarray);  
	} else{ 
		$returnValue = [
			"status" => false
		];
		echo json_encode($returnValue);  
	}
?>