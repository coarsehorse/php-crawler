<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 29.10.2018
 * Time: 19:16
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";
require_once __DIR__ . "/../serialization/SiteTreeSerializer.php";
require_once __DIR__ . "/../domain/Page.php";

$time_start = microtime(true);
$lw = WebCrawler::startCrawling("https://beteastsports.com/");
//$lw = WebCrawler::startCrawling("https://www.moroccanviews.com/");
$time_end = microtime(true);

//var_export($lw . "\n");
var_export(json_encode($lw, JSON_PRETTY_PRINT));
//$conv = SiteTreeSerializer::convert($lw);
//var_export($conv . "\n");
//echo "\n\n\n";
//var_export(SiteTreeSerializer::recoursiveViewBuilder($conv, "https://") . "\n");
//echo "\n\n\n";
//var_export(SiteTreeSerializer::serialize($conv) . "\n");

$execution_time = ($time_end - $time_start);
echo "Execution time: " . $execution_time . " sec\n";