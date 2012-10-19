<?php
define('BASE_URL_', 'http://download.finance.yahoo.com/d/quotes.csv?s=');
define('HISTORICAL_URL_', 'http://ichart.yahoo.com/table.csv?s=');
define('STATIC_END_', '&e=.csv');
define('STATIC_HISTORICAL_', '&ignore=.csv');

class YahooFinance {

    /**
     * This function will retrieve the specific data for the specified stock ticker.
     * The array is keyed as such: $array['TICKER'] = PRICE;
     * array['filesize'] returns the size of the file(s) retrieved
     *  @param $options the data to retrieve
     * @param stock the stock's ticker
     * @brief retrieves data for a stock
     */
    /*
      After Hours Change (Realtime)	c8
      Annualized Gain               	g3
      Ask                           	a0
      Ask (Realtime)                	b2
      Ask Size                      	a5
      Average Daily Volume          	a2
      Bid                           	b0
      Bid (Realtime)                	b3
      Bid Size                      	b6
      Book Value Per Share          	b4
      Change                        	c1
      Change Change In Percent		c0
      Change From Fiftyday Moving Average               m7
      Change From Two Hundreddsay Moving Average	m5
      Change From Year High                 k4
      Change From Year Low                  j5
      Change In Percent                     p2
      Change In Percent (Realtime)          k2
      Change (Realtime)                     c6
      Commission                            c3
      Currency                              c4
      Days High                             h0
      Days Low                              g0
      Days Range                            m0
      Days Range (Realtime)                 m2
      Days Value Change                     w1
      Days Value Change (Realtime)          w4
      Dividend Pay Date                     r1
      Trailing Annual Dividend Yield	    d0
      Trailing Annual Dividend Yield In Percent	y0
      Diluted E P S                         e0
      E B I T D A                           j4
      E P S Estimate Current Year           e7
      E P S Estimate Next Quarter           e9
      E P S Estimate Next Year              e8
      Ex Dividend Date                      q0
      Fiftyday Moving Average               m3
      Shares Float                          f6
      High Limit                            l2
      Holdings Gain                         g4
      Holdings Gain Percent                 g1
      Holdings Gain Percent (Realtime)	    g5
      Holdings Gain (Realtime)              g6
      Holdings Value                        v1
      Holdings Value (Realtime)             v7
      Last Trade Date                       d1
      Last Trade Price Only                 l1
      Last Trade (Realtime) With Time	    k1
      Last Trade Size                       k3
      Last Trade Time                       t1
      Last Trade With Time                  l0
      Low Limit                             l3
      Market Capitalization                 j1
      Market Cap (Realtime)                 j3
      More Info                             i0
      Name                                  n0
      Notes                                 n4
      Oneyr Target Price                    t8
      Open                                  o0
      Order Book (Realtime)                 i5
      P E G Ratio                           r5
      P E Ratio                             r0
      P E Ratio (Realtime)                  r2
      Percent Change From Fiftyday Moving Average	m8
      Percent Change From Two Hundredday Moving Average	m6
      Change In Percent From Year High	k5
      Percent Change From Year Low	j6
      Previous Close	       		p0
      Price Book			p6
      Price E P S Estimate Current Year	r6
      Price E P S Estimate Next Year	r7
      Price Paid	   		p1
      Price Sales	   		p5
      Revenue		   		s6
      Shares Owned	   		s1
      Shares Outstanding		j2
      Short Ratio			s7
      Stock Exchange			x0
      Symbol				s0
      Ticker Trend			t7
      Trade Date			d2
      Trade Links			t6
      Trade Links Additional		f0
      Two Hundredday Moving Average	m4
      Volume	     	    		v0
      Year High		    		k0
      Year Low		    		j0
      Year Range	    		w0

     */
    static function retrieveCurrentPrice($stock) {
        $url = BASE_URL_;
        if (is_array($stock)) {
            $stockquery = implode(',', $stock);
            for ($i = count($stock) - 1; $i != -1; $i--) {
                $stocklist[] = 0;
            }
            $stockarray = array_combine($stock, $stocklist);
            $url = $url.$stockquery.'&f=l1'.STATIC_END_;
        }
        else{
//		die('YAHOO FINANCE RETRIEVECURRENTPRICE IS NOT EQUIPPED TO HANDLE NONARRAY PARAMETER');
            $url = BASE_URL_.$stock.'&f=l1'.STATIC_END_;
        }
        if(!$file=fopen($url,'r'))
            die('ERROR RETRIEVING STOCK PRICE URL '.$url);        
                
        //echo 'File size (line 125): '.sizeof($file);
        //if(!is_array($stock))
          //  return fread($file,sizeof($file));
        
$i = 0;
$stockarray['filesize'] = 0;

        while($line = fgets($file))
        {
if(!is_array($stock))
{
$stockarray[$stock]=trim($line);
}
else
{
            $stockarray[$stock[$i]]=trim($line);
}
$i++;
$stockarray['filesize'] += strlen($line);
        }
        return $stockarray;
    }

    /**
     * This function will retrieve the historical prices in a certain time period by a specified interval.
     * @param $stock stock ticker
     * @param $fromdate start date [format:YYYYMMDD]
     * @param $todate end date [format:YYYYMMDD]
     * @param $type interval to get data from ['daily','weekly','monthly'] 

      Return format:
      array[0] = most recent entry (
      array[0]['line'] = useless parsed line (string)
      array[0]['date'] = date (date)
      array[0]['open'] = opening price (float)
      array[0]['high'] = day high price (float_
      array[0]['low'] = day low price (float)
      array[0]['close'] = closing price (float)
      array[0]['volume'] = volume (int)
      array[0]['adj'] = adj close (float)
     * 
     * array['filesize'] = file retrieved size
      )
     */
    static function retrieveHistorical($stock, $fromdate, $todate, $type) {
        if (!preg_match("/\d{8}/", $fromdate) || !preg_match("/\d{8}/", $todate))
            return false;
        $month = $fromdate[4] . $fromdate[5];
        $day = $fromdate[6] . $fromdate[7];
        $year = $fromdate[0] . $fromdate[1] . $fromdate[2] . $fromdate[3];
        if ($day < 1 || $day > 30)
            return false;
        if ($month < 1 || $month > 12)
            return false;
        if (($year . '-' . $month . '-' . $day) < '1-1-1975')
            return false;

        $month = $month - 1;
        $url = HISTORICAL_URL_ . $stock . '&a=' . $month . '&b=' . $fromdate[6] . $fromdate[7] . '&c=' . $fromdate[0] . $fromdate[1] . $fromdate[2] . $fromdate[3];

        $month = $todate[4] . $todate[5];
        $day = $todate[6] . $todate[7];
        $year = $todate[0] . $todate[1] . $todate[2] . $todate[3];
        if ($day < 1 || $day > 30)
            return false;
        if ($month < 1 || $month > 12)
            return false;
        if (($year . '-' . $month . '-' . $day) > date('Y-m-d')) {
            return false;
        }

        $month = $month - 1;
        $url = $url . '&d=' . $month . '&e=' . $todate[6] . $todate[7] . '&f=' . $todate[0] . $todate[1] . $todate[2] . $todate[3];
        switch ($type) {
            case('daily'):
                $url = $url . '&g=d';
                break;
            case('monthly'):
                $url = $url . '&g=m';
                break;
            case('yearly'):
                $url = $url . '&g=y';
                break;
            default:
                return false;
        }
        $url = $url . STATIC_HISTORICAL_;
        $handle = fopen($url, "r");
$arr['filesize'] = sizeof($handle);
        $keys = array('line', 'date', 'open', 'high', 'low', 'close', 'volume', 'adj');
        //new keys       
        $arr = array();
        $buffer = fgets($handle, 5120);
        if ($buffer == "")
            return false;

//parse each line and place into array $one
        while (($buffer = fgets($handle, 5120)) !== false) {
            if (!preg_match('/([0-9\-]{10})[\,]{1}([0-9\.]+)[\,]{1}([0-9\.]+)[\,]{1}([0-9\.]+)[\,]{1}([0-9\.]+)[\,]{1}([0-9\.]+)[\,]{1}([0-9\.]+)\s?/', $buffer, $one))
                return false;
            $one = array_combine($keys, array_values($one)); //rekeying
            $arr[] = $one;
        }
        if (!feof($handle)) {
            echo "Error: unexpected fgets() fail\n";
            return false;
        }
        fclose($handle);
        return $arr;
    }
static function retrieveEarningsDate($ticker)
{
$url = 'http://finance.yahoo.com/q?s='.$ticker;
$file = fopen($url,'r');
$string = stream_get_contents($file);

$index = strpos($string,'Next Earnings Date:</th><td class=');
$line = substr($string,$index+52,20);
//echo $line;
if(preg_match('/(\d+-\w+-\d+)/',$line,$matches) == false)
echo "!regexerror!";
return $matches[1];
}
}


/*
$arr = array("STEM","COOL","RENN","DRYS","KCG","GOOG","NVDA","RAD");
$arr = YahooFinance::retrieveCurrentPrice($arr);
foreach($arr as $k => $v)
{
if($k == 'filesize')echo "File size: $v";else
echo $k.': $'.$v.'  '.YahooFinance::retrieveEarningsDate($k)."\n";
}
echo YahooFinance::retrieveEarningsDate('rad');*/
?>