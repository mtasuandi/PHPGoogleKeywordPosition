<?php
/**
 * Format array proxies:
 * array('host' => 'HOST', 'port' => 'PORT', 'username' => 'USERNAME', 'password' => 'PASSWORD');
 */
include('RankChecker.class.php');

$newGoogleRankChecker   = new GoogleRankChecker();
$newquery               = 'cat';
$useproxies             = false;
$domain                 = 'www.mywebsite.it';
$arrayproxies           = [];
$googledata             = $newGoogleRankChecker->find($newquery, $domain, $useproxies, $arrayproxies);

echo '<pre>'; var_dump($googledata);
