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
        } else {
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

        while($line = fgets($file)) {
            if(!is_array($stock)) {
                $stockarray[$stock]=trim($line);
            } else {
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
    static function retrieveEarningsDate($ticker) {
        $url = 'http://finance.yahoo.com/q?s='.$ticker;
        $file = fopen($url,'r');
        $string = stream_get_contents($file);
        fclose($file);
        $index = strpos($string,'Next Earnings Date:</th><td class=');
        $line = substr($string,$index+52,20);
//echo $line;
        if(preg_match('/(\d+-\w+-\d+)/',$line,$matches) == false) {
//echo "!regexerror!";
//echo $line;
            return 'N/A';
            return false;
        }
        return $matches[1];
    }

    public static function toAbbrev($str) {
        switch($str) {
        case 'AfterHoursChange':
            return 'c8';
        case 'AnnualizedGain':
            return 'g3';
        case 'Ask':
            return 'b2';
        case 'Size':
            return 'a5';
        case 'AverageDailyVolume':
            return 'a2';
        case 'Bid':
            return 'b3';
        case 'BidSize':
            return 'b6';
        case 'BookValuePerShare':
            return 'b4';
        case 'Change':
            return 'c1';
        case 'ChangeInPercent':
            return 'c0';
        case 'ChangeFromFiftyDayMovingAverage':
            return 'm7';
        case 'ChangeFromTwoHundredDayMovingAverage':
            return 'm5';
        case 'ChangeFromYearHigh':
            return 'k4';
        case 'ChangeFromYearLow':
            return 'j5';
        case 'ChangeInPercent':
            return 'k2';
        case 'Change':
            return 'c6';
        case 'Commission':
            return'c3';
        case 'Currency':
            return 'c4';
        case 'DayHigh':
            return 'h0';
        case 'DayLow':
            return 'g0';
        case 'DayRange':
            return'm2';
        case 'DayValueChange':
            return 'w4';
        case 'DividendPayDate':
            return 'r1';
        case 'TrailingAnnualDividendYield':
            return'd0';
        case 'TrailingAnnualDividendInPercent':
            return'y0';
        case 'DilutedEPS':
            return'e0';
        case 'EBITDA':
            return'j4';
        case 'EPSEstimatedCurrentYear':
            return 'e7';
        case 'EPSEstimateNextQuarter':
            return 'e9';
        case 'EPSEstimateNextYear':
            return'e8';
        case 'ExDividendDate':
            return'q0';
        case 'FiftyDayMovingAverage':
            return'm3';
        case 'SharesFloat':
            return'f6';
        case 'HighLimit':
            return'l2';
        case 'HoldingsGainPercent':
            return 'g5';
        case 'HoldingsGain':
            return 'g6';
        case 'HoldingsValue':
            return 'v7';
        case 'LastTradeDate':
            return 'd1';
        case 'LastTradePriceOnly':
            return 'l1';
        case 'LastTradeWithTime':
            return 'k1';
        case 'LastTradeSize':
            return 'k3';
        case 'LastTradeTime':
            return 't1';
        case 'LastTradeWithTime':
            return 'l0';
        case 'LowLimit':
            return 'l3';
        case 'MarketCap':
            return 'j3';
        case 'MoreInfo':
            return 'i0';
        case 'Name':
            return 'n0';
        case 'Notes':
            return 'n4';
        case 'OneYearTargetPrice':
            return 't8';
        case 'Open':
            return 'o0';
        case 'OrderBook':
            return 'i5';
        case 'PEGRatio':
            return 'r5';
        case 'PERatio':
            return 'r2';
        case 'PercentChangeFromFiftydayMoving':
            return 'm8';
        case 'PercentChangeFromTwoHundreddayMoving':
            return 'm6';
        case 'ChangeInPercentFromYear':
            return 'k5';
        case 'PercentChangeFromYear':
            return 'j6';
        case 'PreviousClose':
            return 'p0';
        case 'Price':
            return 'p6';
        case 'PriceEPSEstimateCurrent':
            return 'r6';
        case 'PriceEPSEstimateNext':
            return 'r7';
        case 'PricePaid':
            return 'p1';
        case 'PriceSales':
            return 'p5';
        case 'Revenue':
            return 's6';
        case 'SharesOwned':
            return 's1';
        case 'Shares':
            return 'j2';
        case 'ShortRatio':
            return 's7';
        case 'StockExchange':
            return 'x0';
        case 'Symbol':
            return 's0';
        case 'TickerTrend':
            return 't7';
        case 'TradeDate':
            return 'd2';
        case 'TradeLinks':
            return 't6';
        case 'TradeLinksAdditional':
            return 'f0';
        case 'TwoHundredDayMovingAverage':
            return 'm4';
        case 'Volume':
            return 'v0';
        case 'YearHigh':
            return 'k0';
        case 'YearLow':
            return 'j0';
        case 'YearRange':
            return 'w0';
        default:
            return false;
        }
    }

}


?>