<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15.11.2018
 * Time: 10:59
 */

require_once __DIR__ . "/../domain/sitemap/Sitemap.php";
require_once __DIR__ . "/../domain/sitemap/SitemapUrl.php";
require_once __DIR__ . "/../domain/sitemap/Hreflang.php";
require_once __DIR__ . "/../crawler/WebCrawler.php";
require_once __DIR__ . "/../domain/Rules.php";
require_once __DIR__ . "/../domain/sitemap/Sitemap.php";
require_once __DIR__ . "/../serialization/SitemapSerializer.php";

$hreflang1 = new Hreflang("ru", "http://ru.com");
$hreflang2 = new Hreflang("eu", "http://eu.com");

$sitemapUrl = new SitemapUrl("loc","1203-12-12", "daily", 1.0, [$hreflang1, $hreflang2]);

//var_export($sitemapUrl . "\n");

//var_export(json_encode($sitemapUrl, JSON_PRETTY_PRINT) . "\n");

//var_export($sitemapUrl->xmlSerialize());

// Real example test
$url = "https://beteastsports.com/";
$lw = WebCrawler::startCrawling($url, new Rules([], []));
$sitemap = new Sitemap($lw, new Rules([], []));

//var_export($sitemap . "\n");
//echo json_encode($sitemap, JSON_PRETTY_PRINT) . "\n";

//$chunks = $sitemap->createSitemapChunks();
//var_export($chunks);

// Auto chunks test
$ch1 = SitemapSerializer::serialize($lw, new Rules([], []), "http://domain.com/sitemap.xml", 1);
$ch2 = SitemapSerializer::serialize($lw, new Rules([], []), "http://domain.com/sitemap.xml", 10,
    0.00035); // 350 KB

var_export($ch1);
var_export($ch2);