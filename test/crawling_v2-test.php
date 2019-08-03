<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30.10.2018
 * Time: 14:26
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";
require_once __DIR__ . "/../serialization/SitemapSerializer.php";
require_once __DIR__ . "/../serialization/SiteTreeSerializer.php";

// Break limitations
ini_set('memory_limit','8192M');
set_time_limit(PHP_INT_MAX);

$url = "https://beteastsports.com/";
//$url = "https://www.moroccanviews.com/";
$time_start = microtime(true);

$levels = WebCrawler::startCrawling($url);

$time_end = microtime(true);
$execution_time = ($time_end - $time_start);

//var_export($levels);
$domainFile = __DIR__ . "/../results2/" . explode('/', $url)[2];
$sitemapFile = $domainFile . "-sitemap.xml";
$treeFile = $domainFile . "-tree.html";

$converted = SiteTreeSerializer::convert($levels);
$tree = SiteTreeSerializer::serialize($converted);
file_put_contents($treeFile, $tree);
echo "Resulting tree view: \"" . realpath($treeFile) . "\"\n";

$serialized = SitemapSerializer::serialize($levels);
file_put_contents($sitemapFile, $serialized);
echo "Resulting sitemap: \"" . realpath($sitemapFile) . "\"\n";

echo "\nDone by " . $execution_time . " sec(" . $execution_time / 60 . " min)\n";