<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12.11.2018
 * Time: 13:23
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

// Case 1 - canonical is different
//$url = "https://hotline.ua/mobile-mobilnye-telefony-i-smartfony/google-pixel-3-4128gb-clearly-white/prices/#cond_new";
//$res = WebCrawler::parsePage($url, []);
//var_export($res);

// Case 2 - canonicals are same
//$url = "https://hotline.ua/mobile-mobilnye-telefony-i-smartfony/google-pixel-3-4128gb-clearly-white/";
//$res = WebCrawler::parsePage($url, []);
//var_export($res);

// Case 3 - canonical is different but already crawled
//$url = "https://hotline.ua/mobile-mobilnye-telefony-i-smartfony/google-pixel-3-4128gb-clearly-white/prices/#cond_new";
//$can = "https://hotline.ua/mobile-mobilnye-telefony-i-smartfony/google-pixel-3-4128gb-clearly-white/";
//$res = WebCrawler::parsePage($url, [$can]);
//var_export($res);

// Crawler code piece test
$link = "https://hotline.ua/mobile-mobilnye-telefony-i-smartfony/google-pixel-3-4128gb-clearly-white/prices/#cond_new";
$page = WebCrawler::parsePage($link, []);
if ($page->getUrl() !== $link) // parsed canonical url instead of original $link
    $crawledLinks[] = $page->getUrl(); // take into account canonical url
$crawledLinks[] = $link;

var_export($crawledLinks);
