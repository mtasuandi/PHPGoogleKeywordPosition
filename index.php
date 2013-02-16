<?php
include("RankChecker.class.php");
$RankChecker = new RankChecker(1,5);

for($res = 10; $res<=40; $res += 10)
{
	set_time_limit(90);
	$result = $RankChecker->find("com", $res, "imlaunchr.com","viral coupon popup");
	if ($result !== false)
	{
		echo "Page: ".$result["page"].".";
		echo "<br />Link: <a href='".$result["url"]."'>".$result["url"]."</a>";
		echo "<br />Position: ".$result["position"];
		echo "<hr />";
	}
	else
	{
		echo "Not found!";
	}
}

for($res = 50; $res<=100; $res += 50)
{
	set_time_limit(90);
	$result = $RankChecker->find("com", 100, "imlaunchr.com","viral coupon popup");
	if ($result !== false)
	{
		echo "Page: ".$result["page"].".";
		echo "<br />Link: <a href='".$result["url"]."'>".$result["url"]."</a>";
		echo "<br />Position: ".$result["position"];
		echo "<hr />";
	}
	else
	{
		echo "Not found!";
	}
}
?>
