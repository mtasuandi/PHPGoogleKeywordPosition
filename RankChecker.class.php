<?php
/*
************************************************************************************************************************
The RankChecker class can be used to find your website rank for a specific keyword in google search.

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
class RankChecker
{
	public $start;
	public $end;
	
	public function __construct($start=1,$end=1)
	{
		$this->start	= $start;
		$this->end		= $end;
	}
	
	public function find($country, $resultperpage, $domainName, $keyword)
	{
		$url	= "";
		$page	= 0;
		
		for($start = ($this->start-1)*10; $start < $this->end*10; $start += 10)
		{
			$page++;
			$keyword	= str_replace(" ","+",$keyword);
			$url		= "http://www.google.$country/search?ie=UTF-8&q=$keyword&amp;num=$resultperpage&start=$start";
			
			$request	= $url;
			$ch 		= curl_init();
			curl_setopt($ch, CURLOPT_URL,$request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'].'-'.$start);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$data = curl_exec($ch);
			curl_close($ch);
			
			$flag	= false;
			$j		= -1;
			$i 		= 1;
			
			while( ($j = stripos($data,"<cite>",$j+1)) !== false )
			{
				$k = stripos($data,"</cite>",$j);
				
				$link = strip_tags(substr($data,$j,$k-$j));
				
				if (strpos($link, $domainName)!== false){
					$flag 		= true;
					
					if(true == $page)
					{
						$position 	= $i+$start;
					}
					else
					{
						$position 	= $i;
					}
					
					break;
				}
				else { ++$i; }
				
			}
			
			if ($flag) {
				break;
			}
		}
		
		if ($flag) {
			return array("url"=>$url, "page"=>$page, "position"=>$position);
		}else{
			return false;
		}
	}
}
?>
