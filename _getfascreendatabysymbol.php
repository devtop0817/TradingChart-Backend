<?php
require __DIR__ . '/vendor/autoload.php';

use \Firebase\JWT\JWT;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$serverName = "DESKTOP-D48NNQG\SQLEXPRESS";
$connectionOptions = array("Database" => "FinicalDB", "UID" => "sa", "PWD" => "Atiks11", "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
if (!isset($_POST['token']) || !isset($_POST['symbol'])) {
    $returnValue = [
        "status" => false
    ];
    echo json_encode($returnValue);
    exit();
}
$token = $_POST['token'];
$symbol = $_POST["symbol"];

$userid = '';
$secret_key = "stockchart_valentin123!@#";
try {
    $decoded = JWT::decode($token, $secret_key, array('HS256'));
    // $userid = $decoded->Email;
    $userid = $decoded->ID;
} catch (UnexpectedValueException $e) {
    $returnValue = [
        "status" => false,
        "message" => 1
    ];
    echo json_encode($returnValue);
    exit();
}

if ($userid == '') {
    $returnValue = [
        "status" => false,
        "message" => 1
    ];
    echo json_encode($returnValue);
    exit();
}


$allquery = "SELECT * FROM [FinicalDB].[dbo].[Market] WHERE stock_title like '%$symbol%' AND Fiscal_year='/current'";

if ($allquery == "") {
    $returnValue = [
        "status" => false,
        "message" => 1
    ];
    echo json_encode($returnValue);
    exit();
}

$result = sqlsrv_query($conn, $allquery);
if ($result == false) {
    echo $allquery;
    exit;
}

$indicators = [];
while ($obj = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $underline_pos = strpos($obj['symbol'], '_');
    if ($underline_pos !== false) {
        $market = substr($obj['symbol'], 0, $underline_pos);
        $symbol = substr($obj['symbol'], $underline_pos + 1);
        $company = $obj['industry1'];
        $ele = [
            'exchange' => $market,
            'company' => $company,
            'symbol' => $symbol,
            'stock_title' => $obj['stock_title'],
            'market_cap' => $obj['market_cap'],
            'PE_Ratio' => $obj['PE_Ratio'],
            'PB_Ratio' => $obj['PB_Ratio'],
            'Dividend_Yield' => $obj['Dividend_Yield'],
            'Altman_Z_Score' => $obj['Altman_Z_Score'],
            'Asset_turnover' => $obj['Asset_turnover'],
            'Beneish_Score' => $obj['Beneish_Score'],
            'Beta' => $obj['Beta'],
            'Book_Value_Growth_10_percent' => $obj['Book_Value_Growth_10_percent'],
            'Book_Value_Growth_12_percent' => $obj['Book_Value_Growth_12_percent'],
            'Book_Value_Growth_5_percent' => $obj['Book_Value_Growth_5_percent'],
            'Book_Share' => $obj['Book_Share'],
            'Cash_Equivalent' => $obj['Cash_Equivalent'],
            'Cash_Flow_Div' => $obj['Cash_Flow_Div'],
            'Debt_to_asset' => $obj['Debt_to_asset'],
            'Debt_to_equity' => $obj['Debt_to_equity'],
            'Dividend_Ratio' => $obj['Dividend_Ratio'],
            'Dividends_Share' => $obj['Dividends_Share'],
            'Earnings_Share' => $obj['Earnings_Share'],
            'Earnings_Power_Value' => $obj['Earnings_Power_Value'],
            'EBITDA_Growth_10_percent' => $obj['EBITDA_Growth_10_percent'],
            'EBITDA_Growth_12_percent' => $obj['EBITDA_Growth_12_percent'],
            'EBITDA_Growth_5_percent' => $obj['EBITDA_Growth_5_percent'],
            'Enterprise_Value' => $obj['Enterprise_Value'],
            'EPS_Basic' => $obj['EPS_Basic'],
            'EPS' => $obj['EPS'],
            'Equity_to_Asset' => $obj['Equity_to_Asset'],
            'Free_Cash_Flow_growth_10_percent' => $obj['Free_Cash_Flow_growth_10_percent'],
            'Free_Cash_Flow_growth_12_percent' => $obj['Free_Cash_Flow_growth_12_percent'],
            'Free_Cash_Flow_growth_5_percent' => $obj['Free_Cash_Flow_growth_5_percent'],
            'Cash_Share' => $obj['Cash_Share'],
            'Graham_Number' => $obj['Graham_Number'],
            'Intrinsic_Value_projected' => $obj['Intrinsic_Value_projected'],
            'Stock_issuance' => $obj['Stock_issuance'],
            'Median_PS_Value' => $obj['Median_PS_Value'],
            'Net_Share' => $obj['Net_Share'],
            'Net_Income' => $obj['Net_Income'],
            'Peter_Lynch_FV' => $obj['Peter_Lynch_FV'],
            'Piotroski_F_Score' => $obj['Piotroski_F_Score'],
            'Preferred_Dividends' => $obj['Preferred_Dividends'],
            'Pre_Tax_income' => $obj['Pre_Tax_income'],
            'Price_Flow' => $obj['Price_Flow'],
            'Price_OP_Flow' => $obj['Price_OP_Flow'],
            'Price_Tangible_book' => $obj['Price_Tangible_book'],
            'ps_ratio' => $obj['ps_ratio'],
            'Stock_repurchase' => $obj['Stock_repurchase'],
            'Total_Revenue' => $obj['Total_Revenue'],
            'Revenue_Growth_10_percent' => $obj['Revenue_Growth_10_percent'],
            'Revenue_Growth_12_percent' => $obj['Revenue_Growth_12_percent'],
            'Revenue_Growth_5_percent' => $obj['Revenue_Growth_5_percent'],
            'Revenue_Share' => $obj['Revenue_Share'],
            'ROA_percent' => $obj['ROA_percent'],
            'ROE_percent' => $obj['ROE_percent'],
            'ROE_percent_adjusted' => $obj['ROE_percent_adjusted'],
            'Shares_BuyBack_Ratio' => $obj['Shares_BuyBack_Ratio'],
            'shares_out' => $obj['shares_out'],
            'Shiller_Ratio' => $obj['Shiller_Ratio'],
            'Sloan_Ratio' => $obj['Sloan_Ratio'],
            'Total_Assets' => $obj['Total_Assets'],
            'Total_Debt_Share' => $obj['Total_Debt_Share'],
            'Total_equity' => $obj['Total_equity'],
            'Total_Liabilities' => $obj['Total_Liabilities'],
            'fiscal_period' => $obj['fiscal_period'],
            'industry2' => $obj['industry2'],
            'Currency' => $obj['Currency']
        ];
        array_push($indicators, $ele);
    }
}

$returnValue = [
    'status' => true,
    'data' => $indicators
];
echo json_encode($returnValue);
