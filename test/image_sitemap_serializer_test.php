<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.11.2018
 * Time: 10:56
 */

require_once __DIR__ . "/../serialization/ImagesSitemapSerializer.php";
require_once __DIR__ . "/../crawler/WebCrawler.php";
require_once __DIR__ . "/../domain/Rules.php";

$url = "https://beteastsports.com/";
$lw = WebCrawler::startCrawling($url, new Rules([], []));
//$lw = WebCrawler::startCrawling($url, new Rules(['/^.*beteastsports\.com\/.*$/'], []));
$res = ImagesSitemapSerializer::serialize($lw, new Rules([], []));

file_put_contents(__DIR__ . "/../results3/image-sitemap-" . explode("/", $url)[2] . ".xml", $res);

// Filter domain images test
//$url = "http://domain.com/my-article/";
//$imgs = ["http://domain.com/img1.png", "http://example.com/me.jpg", "http://domain.com/he.jpg"];
//$res = ImageSitemapSerializer::filterDomainImages($imgs, $url);
//var_export($res);

// Sitemap rules tests
// Exceptions test
$res = ImagesSitemapSerializer::serialize($lw, new Rules(['/^.*beteastsports\.com.*$/'], []));
var_export($res);
// Approvals test
$res = ImagesSitemapSerializer::serialize($lw, new Rules(['/^.*beteastsports\.com.*$/'], ['/https:\/\/beteastsports.com\/wp-content\/themes\/swd-theme\/assets\/img\/header_button\.png/']));
var_export($res);