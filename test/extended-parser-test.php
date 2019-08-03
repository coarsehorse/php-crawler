<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09.11.2018
 * Time: 12:15
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

$url = "https://hotline.ua/";
//$url = "https://allo.ua";
//$url = "https://www.facebook.com";
//$url = "https://www.google.com";
//$url = "https://moz.com/";

// Robots test
$disallows = WebCrawler::robotsDisallowReader($url);
foreach ($disallows as $d)
    echo "$d === " . WebCrawler::constructRegexFromRobotsRule($d, $url) . "\n";

