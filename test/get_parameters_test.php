<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.11.2018
 * Time: 10:25
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

$url = "https://thewings.com.ua/shop?filter=1&f_pa_leather_type=kajzer";

$res = WebCrawler::parsePage($url, []);

var_export($res);