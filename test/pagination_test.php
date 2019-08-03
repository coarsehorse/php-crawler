<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.11.2018
 * Time: 17:47
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

$url1 = "https://domain.com/doctors/page/111/";
$url2 = "https://domain.com/doctors/?page=1/";
$url3 = "https://domain.com/doctors?page=2/";
$url4 = "https://domain.com/doctors/?p=10/";
$url5 = "https://domain.com/doctors/p/112/";

$url = "https://thewings.com.ua/shop/muzhskie/page/2/";

$res = WebCrawler::parsePage($url, []);

var_export($res);