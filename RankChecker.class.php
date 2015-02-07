<?php
if ( @ini_set( 'max_execution_time', 1200 ) !== FALSE )
	@ini_set( 'max_execution_time', 1200 );

if ( !class_exists( 'GoogleRankChecker' ) ) {
	class GoogleRankChecker {
		public $start;
		public $end;
		
		public function __construct( $start = 1, $end = 2 ) {
			$this->start = $start;
			$this->end = $end;
		}
		
		public function find( $keyword, $useproxie, $proxies ) {
			$results = array();

			for ( $start = ( $this->start-1 ) * 10; $start <= $this->end * 10; $start += 10 ) {
				$ua	= array(
					0 	=> 'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
					10 	=> 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
					20 	=> 'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0;  rv:11.0) like Gecko'
				);
	
				if ( $useproxie ) {
					$host = $proxies['host'];
					$port = $proxies['port'];
					$username	= $proxies["username"];
					$password = $proxies["password"];
					
					if ( !empty( $username ) ) {
						$auth = base64_encode( $username . ":" . $password );
						$useauth = "Proxy-Authorization: Basic $auth";
					} else {
						$useauth = "";
					}
					
					$options = array(
						"http" => array(
							"method" => "GET",
							"header" => "Accept-language: en\r\n" .
								"Cookie: SEO Zen\r\n" .
								"User-Agent: " . $ua[ $start ] . "\r\n".
								$useauth,
							"proxy" => "tcp://" . $host . ":" . $port,
							"request_fulluri" => true
						)
					);
				} else {
					$options = array(
						"http" => array(
							"method" => "GET",
							"header" => "Accept-language: en\r\n" .
								"Cookie: SEO Zen\r\n" . 
								"User-Agent: " . $ua[ $start ] )
					);
				}
				
				if ( $useproxie ) {
					if ( !empty( $username ) ) {
						$auth = base64_encode( $username . ":" . $password );
		
						$arrayproxies	= array(
							CURLOPT_PROXY => $host,
							CURLOPT_PROXYPORT	=> $port,
							CURLOPT_PROXYUSERPWD => $auth
						);
					}	else {
						$arrayproxies	= array(
							CURLOPT_PROXY => $host,
							CURLOPT_PROXYPORT	=> $port
						);
					}
				}	else {
					$arrayproxies	= array();
				}
				
				$keyword = str_replace( ' ', '+', trim( $keyword ) );
				$url = 'https://www.google.com/search?ie=UTF-8&q=' . $keyword . '&start=' . $start . '&num=30';
				$context = stream_context_create( $options );

				if ( $this->_isCurl() ) {
					$data = $this->_curl( $url, $useproxie, $arrayproxies );
				} else {
					$data	= @file_get_contents( $url, false, $context );
				}
				
				if ( is_array( $data ) ) {
					$errmsg = $data['errmsg'];
					$results = array( 'rank' => 'zerox', 'url' => $errmsg );
				} else {
					if ( strpos( $data, 'To continue, please type the characters below' ) !== FALSE || $data == FALSE || strpos( $data, "We're sorry" ) !== FALSE ) {
						$results = array( 'rank' => 'zero', 'url' => '' );
					} else {
						$j = -1;
						$i = 1;
						
						while( ( $j = stripos( $data, '<cite class="_Rm">', $j+1 ) ) !== false ) {
							$k = stripos( $data, '</cite>', $j );
							$link = strip_tags( substr( $data, $j, $k-$j ) );
							$rank	= $i++;
							$results[] = array( 'rank' => $rank, 'url' => $link );
						}
					}
				}
				
				$sleep = rand( 20, 25 );
				sleep( $sleep );
			}
			
			return $results;
		}

		public function getPageRank( $url ) {
			$pageRank = 0;

			try {
				$options = array( 
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_HEADER         => false,
	        CURLOPT_FOLLOWLOCATION => true, 
	        CURLOPT_USERAGENT      => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0",
	        CURLOPT_AUTOREFERER    => true, 
	        CURLOPT_CONNECTTIMEOUT => 120, 
	        CURLOPT_TIMEOUT        => 120, 
	        CURLOPT_MAXREDIRS      => 10, 
		    );

		    $newurl = 'http://www.prapi.net/pr.php?url=' . $url . '&f=json';
		    $ch = curl_init( $newurl );
		    curl_setopt_array( $ch, $options );
		    set_time_limit( 240 );
		    $content = curl_exec( $ch );
		    curl_errno( $ch );
		    curl_error( $ch );
		    curl_getinfo( $ch );
		    curl_close( $ch );
		    $objContent = json_decode( $content );
		    $pageRank = $objContent->pagerank;
			} catch ( Exception $e ) {
				$pageRank = 0;
			}
	    return $pageRank; 
		}

		private function _isCurl() {
		  return function_exists( 'curl_version' );
		}

		private function _curl( $url, $useproxie, $arrayproxies ) {
			try {
				$ch = curl_init( $url );

				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_HEADER, false );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
				curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:34.0) Gecko/20100101 Firefox/34.0' );
				curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
				curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 120 );
				curl_setopt( $ch, CURLOPT_TIMEOUT, 120 );
				curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch, CURLOPT_SSLVERSION, 3 );

				if ( $useproxie ) {
					if ( !empty( $arrayproxies ) ) {
						foreach( $arrayproxies as $param => $val ) {
							curl_setopt( $ch, $param, $val );
						}
					}
				}

				$content = curl_exec( $ch );
				$errno = curl_errno( $ch );
				$error = curl_error( $ch );
			  curl_close( $ch );

			  if ( !$errno ) {
					return $content;
				} else {
					return array( 'errno' => $errno, 'errmsg' => $error );
				}
			} catch ( Exception $e ) {
				return array( 'errno' => $e->getCode(), 'errmsg' => $e->getMessage() );
			}
		}
	}
}
?>
