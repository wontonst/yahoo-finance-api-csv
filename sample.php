<?
require_once('YahooFinance.php');

//sample usage of this script

//list of stocks we'll be working with
$arr = array("STEM","COOL","RENN","DRYS","KCG","GOOG","NVDA","RAD");
//grab price for all stocks in array
$arr = YahooFinance::retrieveCurrentPrice($arr);

foreach($arr as $k => $v) {
    if($k == 'filesize') {
        echo "File size: $v\n";
        continue;
    } else
        echo $k.': $'.$v."\n";
}
echo 'Earnings date for RAD (Rite Aid) : '.YahooFinance::retrieveEarningsDate('rad')."\n";

echo 'Retrieving historical data for GOOG...'."\n";
$historical = (YahooFinance::retrieveHistorical('GOOG','20150101','20150108','daily'));
foreach($historical as $val){
echo 'Date: '.$val['date'].' Open: '.$val['open'].' Close: '.$val['close']."\n";
}

?>