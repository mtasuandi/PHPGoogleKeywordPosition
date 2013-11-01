<?php
/**
 * Format array proxies:
 * array('host' => 'HOST', 'port' => 'PORT', 'username' => 'USERNAME', 'password' => 'PASSWORD');
 */
include("RankChecker.class.php");
$newGoogleRankChecker 	= new GoogleRankChecker();
$newquery 		= 'YOUR_KEYWORD';
$useproxies 		= 'TRUE_OR_FALSE';
$arrayproxies 		= 'ARRAY_PROXIES';
$googledata 		= $newGoogleRankChecker->find($newquery, $useproxies, $arrayproxies);
var_dump($googledata);
?>
