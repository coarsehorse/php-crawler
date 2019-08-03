<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.10.2018
 * Time: 19:20
 */

require_once __DIR__ . "/../domain/crawling/Level.php";
require_once __DIR__ . "/../domain/Page.php";

$lvl = new Level(1, [new Page("qwer", "h1", "title", []),
    new Page("asdf", "h1", "title", []),
    new Page("zxcv", "h1", "title", [])
]);

//var_export($lvl);
//echo "\n\n";
//var_export($lvl->getLevelPages()[0]->getValue());


//var_dump($lvl->isPageExists(new Page("qwer1", "h1", "title", [])));
//var_dump($lvl->isPageExists(new Page("qwer", "h1", "title", [])));

$lvl->incrementPageCounter(new Page("zxcv", "", "", []));
var_export($lvl);

$lvl->removePage(new Page("asdf", "", "", []));
//$lvl->removePage(new Page("qwer", "", "", []));
//var_export($lvl);

echo "\n\n" . json_encode($lvl, JSON_PRETTY_PRINT) . "\n\n";