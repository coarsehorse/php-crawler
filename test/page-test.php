<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 29.10.2018
 * Time: 18:57
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";
require_once __DIR__ . "/../domain/Page.php";

//$url = "https://beteastsports.com/hubungi-kami/";
//$url = "https://www.moroccanviews.com/homepage-3/";
//$url = "https://hotline.ua/computer-planshety/apple-ipad-pro-129-2018-wi-fi-plus-cellular-1tb-space-gray/";
$url = "https://www.oncrawl.com/oncrawl-seo-thoughts/hreflang-and-seo-5-mistakes-to-avoid/";

$time_start = microtime(true);
$p = WebCrawler::parsePage($url, []);
$time_end = microtime(true);

$execution_time = ($time_end - $time_start);

var_export($p);

echo "Execution time: " . $execution_time . " sec\n";

