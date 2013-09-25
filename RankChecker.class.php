<?php
/*
************************************************************************************************************************
The GoogleRankChecker class can be used to find your website rank for a specific keyword in google search.

Please note that google search results is sensitive to IP, Country, Language, Either you're logged in or not and etc.

Written by: Hamed Afshar
Company: Golha (http://www.golha.net)

Modified by: M Teguh A Suandi
Company: Biztech Indonesia (http://manfredekblad.net)

Change Log:
	Version 1.0 (2011/08/02)
		First release.
	Version 1.1 (2013/13/02)
		Second release, add keyword position result.
************************************************************************************************************************
*/
ini_set('max_execution_time', 1200);
if(!class_exists('GoogleRankChecker'))
{
	class GoogleRankChecker
	{
		public $start;
		public $end;
		
		public function __construct($start=1, $end=10)
		{
			$this->start	= $start;
			$this->end		= $end;
		}
		
		public function find($keyword)
		{
			for($start = ($this->start-1)*10; $start < $this->end*10; $start += 10)
			{
				$ua	= array(
					0 	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36',
					10 	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0',
					20 	=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/536.30.1 (KHTML, like Gecko) Version/6.0.5 Safari/536.30.1',
					30  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.65 Safari/537.36',
					40  => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.57 Safari/537.36',
					50  => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.62 Safari/537.36',
					60  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:23.0) Gecko/20100101 Firefox/23.0',
					70  => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.57 Safari/537.36',
					80  => 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36',
					90  => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36'
				);
	
				$options = array(
				  	'http'		=> array(
				    	'method'	=> "GET",
				    	'header'	=> "Accept-language: en\r\n" .
				            "Cookie: foo=bar\r\n" . 
				            "User-Agent: " . $ua[$start]
				    )
				);
				
				$keyword		= str_replace(" ", "+", trim($keyword));
				$url			= "https://www.google.com/search?ie=UTF-8&q=$keyword&start=$start&num=100";
				sleep(10);
				$context 		= stream_context_create($options);

				if($this->_isCurl())
				{
					$data 	= $this->_curl($url);
				}
				else
				{
					$data	= file_get_contents($url, false, $context);
				}
				
				$flag	= false;
				$j		= -1;
				$i 		= 1;
				
				while( ($j = stripos($data,"<cite>",$j+1)) !== false )
				{
					$k 			= stripos($data,"</cite>",$j);
					$link 		= strip_tags(substr($data,$j,$k-$j));
					$rank		= $i++;
					$results[]	= array("rank" => $rank, "url" => $link);

					if($this->_isCurl() == false)
					{
						$flag 	= true;
					}
				}
				
				if ($flag) {
					break;
				}
			}
			return $results;
		}

		public function get_web_page($url)
		{
			$res = array();
		    $options = array( 
		        CURLOPT_RETURNTRANSFER => true,
		        CURLOPT_HEADER         => false,
		        CURLOPT_FOLLOWLOCATION => true, 
		        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/536.30.1 (KHTML, like Gecko) Version/6.0.5 Safari/536.30.1',
		        CURLOPT_AUTOREFERER    => true, 
		        CURLOPT_CONNECTTIMEOUT => 120, 
		        CURLOPT_TIMEOUT        => 120, 
		        CURLOPT_MAXREDIRS      => 10, 
		    ); 

		    $newurl	 = 'http://www.checkpagerankapi.com/pr?url='.$url; 
		    $ch      = curl_init( $newurl ); 
		    curl_setopt_array( $ch, $options );
		    set_time_limit(240); 
		    $content = curl_exec( $ch ); 
		    $err     = curl_errno( $ch ); 
		    $errmsg  = curl_error( $ch ); 
		    $header  = curl_getinfo( $ch ); 
		    curl_close( $ch ); 
   
		    $res['url'] 	= $header['url'];
		    return $res; 
		}

		private function _isCurl()
		{
		    return function_exists('curl_version');
		}

		private function _curl($url)
		{
			$curloptions = array( 
		        CURLOPT_RETURNTRANSFER => true,
		        CURLOPT_HEADER         => false,
		        CURLOPT_FOLLOWLOCATION => true, 
		        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36',
		        CURLOPT_AUTOREFERER    => true, 
		        CURLOPT_CONNECTTIMEOUT => 120, 
		        CURLOPT_TIMEOUT        => 120, 
		        CURLOPT_MAXREDIRS      => 10,
		        CURLOPT_SSL_VERIFYPEER => false 
		    );

			$ch      = curl_init($url); 
		    curl_setopt_array($ch, $curloptions);
		    set_time_limit(240); 
		    $content = curl_exec($ch);
		    curl_close($ch);
		    return $content;
		}
	}
}
?>
