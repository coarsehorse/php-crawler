<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 031 31.10.18
 * Time: 11:29 PM
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

$res = WebCrawler::parsePage("https://www.moroccanviews.com/login/");
var_export($res);