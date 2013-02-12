<?php
include("RankChecker.class.php");
$RankChecker = new RankChecker(1,5);
$result = $RankChecker->find("dotcomcodex.com","viral coupon popup");

if ($result !== false)
{
	echo "Your website is found at page number  ".$result["page"].".";
	echo "<br />Visit here: <a href='".$result["url"]."'>".$result["url"]."</a>";
	echo "<br />Your website rank is ".$result["position"];
}
else
{
	echo "Not found!";
}
?>