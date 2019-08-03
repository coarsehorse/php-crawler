<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12.11.2018
 * Time: 14:46
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

// Failed because canonical fails:
$url1 = "https://ru.aliexpress.com/item/Bear-Leader-Girls-Dress-2017-New-Summer-Mesh-Girls-Clothes-Pink-Applique-Princess-Dress-Children-Summer/32780054522.html?spm=a2g01.11212660.layer-ehwhme.3.570827b8SimK4X&pvid=c82fd09a-d5c6-4888-9448-d3a98484daff&gps-id=5784789&scm=1007.17919.110176.0&scm-url=1007.17919.110176.0&scm_id=1007.17919.110176.0";
// 2 hreflangs:
$url2 = "https://www.oncrawl.com/oncrawl-seo-thoughts/hreflang-and-seo-5-mistakes-to-avoid/";
// No hreflang, but alternate present:
$url3 = "https://rozetka.com.ua/xiaomi_redmi_note5_4_64gb_blue_eu/p48565486/#tab=characteristics";

$page = WebCrawler::parsePage($url2, []);

echo json_encode($page, JSON_PRETTY_PRINT);
//var_export($page);