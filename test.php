<?
require_once('YahooFinance.php');

$arr = array("STEM","COOL","RENN","DRYS","KCG","GOOG","NVDA","RAD","T","A",'AA','LUV','SWHC','C','F','SINA','BAC');

//abbreviation change speedtest
$iterations = 250000;
$start = microtime(true);
for($i = 0; $i != $iterations; $i++)
    YahooFinance::toAbbrev('ChangeFromFiftyDayMovingAverage');
$end = microtime(true);
echo "\n".'Time to execute '.$iterations.' abbreviation calls: '.($end-$start).' seconds'."\n\t".'('.(1000*($end-$start))/$iterations.' milliseconds per call)'."\n";

//current price retrieval
$iterations = 5;
$mtime = 0;
for($i = 0; $i != $iterations; $i++){
$start = microtime(true);
YahooFinance::retrieveCurrentPrice($arr);
$end = microtime(true);
echo "\n".'Time to retrieve '.count($arr).' stock prices : '.($end-$start).' seconds'."\n\t".'('.(1000*($end-$start))/count($arr).' milliseconds per stock)';
$mtime += $end-$start;
}
echo "\n\n".'Time to execute '.$iterations.' stock price retrieval pulls : '.$mtime.' seconds'."\n\t".'('.$mtime/$iterations.' seconds per pull)'."\n";

$start = microtime(true);
for($i = 0; $i != 5; $i++)
YahooFinance::retrieveEarningsDate('rad');
$end = microtime(true);
echo "\n".'Time to retrieve 5 earnings dates: '.($end-$start).' seconds ('.(($end-$start)/5).' seconds per pull'."\n";
?>