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
if(@ini_set('max_execution_time', 1200) !== FALSE)
	@ini_set('max_execution_time', 1200);

if(!class_exists('GoogleRankChecker'))
{
	class GoogleRankChecker
	{
		public $start;
		public $end;
		
		public function __construct($start=1, $end=2)
		{
			$this->start	= $start;
			$this->end		= $end;
		}
		
		public function find($keyword, $useproxie, $proxies)
		{	
			for($start = ($this->start-1)*10; $start <= $this->end*10; $start += 10)
			{
				$ua	= array(
					0 	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36',
					10 	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0',
					20 	=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/536.30.1 (KHTML, like Gecko) Version/6.0.5 Safari/536.30.1'
				);
	
				if($useproxie)
				{				
					$host 		= $proxies["host"];
					$port		= $proxies["port"];
					$username	= $proxies["username"];
					$password 	= $proxies["password"];
					
					if(!empty($username))
					{
						$auth 		= base64_encode($username.":".$password);
						$useauth 	= "Proxy-Authorization: Basic $auth";
					}
					else
					{
						$useauth = "";
					}
					
					$options	= array(
						"http"		=> array(
							"method"			=> 	"GET",
							"header"			=> 	"Accept-language: en\r\n".
													"Cookie: biztech=indonesia\r\n". 
													"User-Agent: ".$ua[$start]."\r\n".
													$useauth,
							"proxy" 			=> 	"tcp://".$host.":".$port,
							"request_fulluri" 	=> 	true
						)
					);
				}
				else
				{
					$options = array(
						"http"		=> array(
							"method"	=> "GET",
							"header"	=> "Accept-language: en\r\n" .
								"Cookie: biztech=indonesia\r\n" . 
								"User-Agent: ".$ua[$start])
					);
				}
				
				if($useproxie)
				{
					if(!empty($username))
					{
						$auth 	= base64_encode($username.":".$password);
		
						$arrayproxies	= array(
							CURLOPT_PROXY 			=> $host,
							CURLOPT_PROXYPORT		=> $port,
							CURLOPT_PROXYUSERPWD 	=> $auth
						);
					}
					else
					{
						$arrayproxies	= array(
							CURLOPT_PROXY 		=> $host,
							CURLOPT_PROXYPORT	=> $port
						);
					}
				}
				else
				{
					$arrayproxies	= array();
				}
				
				$keyword		= str_replace(" ", "+", trim($keyword));
				$url			= "https://www.google.com/search?ie=UTF-8&q=$keyword&start=$start&num=30";
				$context 		= stream_context_create($options);

				if($this->_isCurl())
				{
					$data 	= $this->_curl($url, $useproxie, $arrayproxies);
				}
				else
				{
					$data	= @file_get_contents($url, false, $context);
				}
				
				if(is_array($data))
				{
					$errmsg 	= $data['errmsg'];
					$results 	= array("rank" => "zerox", "url" => $errmsg);
				}
				else
				{
					if(strpos($data, "To continue, please type the characters below") !== FALSE || $data == FALSE || strpos($data, "We're sorry") !== FALSE)
					{
						$results 	= array("rank" => "zero", "url" => "");
					}
					else
					{
						$flag	= false;
						$j		= -1;
						$i 		= 1;
						
						while( ($j = stripos($data,'<cite class="vurls">',$j+1)) !== false )
						{
							$k 			= stripos($data,"</cite>",$j);
							$link 		= strip_tags(substr($data,$j,$k-$j));
							$rank		= $i++;
							$results[]	= array("rank" => 1, "url" => $link);
		
							if($this->_isCurl() === false)
							{
								$flag 	= true;
							}
						}
						
						if ($flag) {
							break;
						}
					}
				}
				
				$sleep = rand(20,25);
				sleep($sleep);
				
				if($this->_isCurl() === false)
				{
					return $results;
				}
			}
			
			if($this->_isCurl())
			{
				return $results;
			}
		}

		private function _isCurl()
		{
		    return function_exists("curl_version");
		}

		private function _curl($url, $useproxie, $arrayproxies)
		{			
			$ch = curl_init($url); 
		    
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/29.0.1547.66 Safari/537.36");
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			if($useproxie)
			{
				if(!empty($arrayproxies))
				{
					foreach($arrayproxies as $param => $val)
					{	
						curl_setopt($ch, $param, $val);
					}
				}
			}
		    $content 	= curl_exec($ch);
			$errno 		= curl_errno($ch);
			$error 		= curl_error($ch);
		    curl_close($ch);
			if($errno == 0)
			{
				return $content;
			}
			else
			{
				return array("errno" => $errno, "errmsg" => $error);
			}
		    
		}
	}
}
?>
