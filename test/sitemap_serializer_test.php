<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.11.2018
 * Time: 15:30
 */

require_once __DIR__ . "/../serialization/SitemapSerializer.php";
require_once __DIR__ . "/../crawler/WebCrawler.php";
require_once __DIR__ . "/../domain/Rules.php";

$url = "https://beteastsports.com/";
$lw = WebCrawler::startCrawling($url, new Rules([], []));
$res = SitemapSerializer::serialize($lw, new Rules([], []), "https://example.com/sitema.xml");

var_export($res);