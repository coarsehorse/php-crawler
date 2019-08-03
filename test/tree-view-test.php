<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 29.10.2018
 * Time: 15:39
 */

require_once __DIR__ . "/../serialization/SiteTreeSerializer.php";
require_once __DIR__ . "/../domain/LevelsWrapper.php";
require_once __DIR__ . "/../domain/SiteTree.php";

$links = explode(PHP_EOL, file_get_contents("moroccanviews.com-links.txt"));

$siteTree = new SiteTree();

foreach ($links as $link) {
    $linkLevelNames = array_slice(explode('/', $link), 2, -1);
    //$siteTree = SiteTreeSerializer::siteTreeBuilder($siteTree, $linkLevelNames, $link);
}

$html = SiteTreeSerializer::serialize($siteTree);
file_put_contents('moroccanviews.com-view.html', $html);