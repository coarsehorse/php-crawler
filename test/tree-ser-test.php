<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.10.2018
 * Time: 17:39
 */

include_once __DIR__ . "/../serialization/SiteTreeSerializer.php";
require_once __DIR__ . "/../domain/crawling/Level.php";
require_once __DIR__ . "/../domain/crawling/LevelsWrapper.php";

$l1 = ["http://domain/", "http://domain/about/v1/article/", "http://domain/about/v2/article/", "http://domain/about/us/"];
$l2 = ["http://domain/tours/level2/some_shit/", "http://domain/tours/africa-tour/", "http://domain/another_shit/"];
$lw = new LevelsWrapper();
$lw->addNewLevel($l1);
$lw->addNewLevel($l2);

$anoth = SiteTreeSerializer::convert($lw);
//var_export($anoth);
$html = SiteTreeSerializer::serialize($anoth);
file_put_contents('tree-view.html', $html);
echo "Done\n";