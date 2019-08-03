<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 19.10.2018
 * Time: 17:16
 */

require_once __DIR__ . "./crawler/WebCrawler.php";
require_once __DIR__ . "/serialization/SitemapSerializer.php";
require_once __DIR__ . "/serialization/ImagesSitemapSerializer.php";
require_once __DIR__ . "/serialization/SiteTreeSerializer.php";
require_once __DIR__ . "/domain/Rules.php";
require_once __DIR__ . "/logger/Logger.php";

// Increase limits
ini_set('memory_limit', '16384M');
ini_set('default_socket_timeout', 1200); // 20 min
set_time_limit(PHP_INT_MAX);

$url = "https://beteastsports.com/";
//$url = "https://www.northerngolfshop.com.au/events";
// $url = "https://ampmlimo.ca/";
//$url = "https://www.moroccanviews.com/";
//$url = "https://www.rockbottomvapes.com/";
//$url = "https://mediglobus.com/";

echo "\n::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::\n";
echo "::::  Start crawling " . $url . "\n";
echo "::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::\n\n";
Logger::log("Start crawling $url");

$time_start = microtime(true);

$url = WebCrawler::fixUrl($url);

// Crawl $url
$lw = WebCrawler::startCrawling($url, new Rules());

// Prepare filesystem to save crawling results
$domain = explode('/', $url)[2];
$domain_dir = __DIR__ . "/results3/" . str_replace(".", "-", $domain);
@mkdir($domain_dir); // create domain dir
array_map('unlink', glob($domain_dir . "/*")); // wipe dir

// Save crawled levels, just in case
$json = json_encode($lw, JSON_PRETTY_PRINT);
if (!file_put_contents(WebCrawler::fixUrl($domain_dir) . "crawled-levels-bkp.json", $json))
    file_put_contents("crawled-levels-bkp.json", $json); // if smth wrong with dir, try to save it again

// Construct sitemap
$sitemaps = SitemapSerializer::serialize($lw, new Rules(), $url . 'sitemap.xml', 10);

// Write sitemap or index sitemap
file_put_contents(WebCrawler::fixUrl($domain_dir) . "sitemap.xml", $sitemaps[0]);

// Compress and write sitemap parts
if (count($sitemaps) > 1) {
    for ($i = 1; $i < count($sitemaps); $i++) {
        $sitemapIndex = $i - 1;
        Utils::compressToGz(WebCrawler::fixUrl($domain_dir) . "sitemap{$sitemapIndex}.xml.gz", $sitemaps[$i]);
    }
}

// Construct images sitemap
$imagesSitemap = ImagesSitemapSerializer::serialize($lw, new Rules(['/.*/'], ['/.*' . $domain . '.*/']));
file_put_contents(WebCrawler::fixUrl($domain_dir) . "images_sitemap.xml", $imagesSitemap);

// Construct site tree
$siteTree = SiteTreeSerializer::serialize(SiteTreeSerializer::convert($lw), "https://");
file_put_contents(WebCrawler::fixUrl($domain_dir) . "sitetree.html", $siteTree);

$time_end = microtime(true);
$execution_time = ($time_end - $time_start);

echo "Done by " . $execution_time . " sec(" . $execution_time / 60 . " min)\n";
