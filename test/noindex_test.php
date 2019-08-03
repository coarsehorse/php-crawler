<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12.11.2018
 * Time: 11:07
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

$url = "https://hotline.ua/login/";
//$url = "https://hotline.ua";

$page = WebCrawler::parsePage($url);

var_export($page);