<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.10.2018
 * Time: 18:40
 */

require_once __DIR__ . "/../domain/crawling/Level.php";
require_once __DIR__ . "/../domain/crawling/LevelsWrapper.php";
require_once __DIR__ . "/../domain/Page.php";

$p1 = [new Page("qwer1", "h1", "title", []),
    new Page("asdf1", "h1", "title", []),
    new Page("zxcv2", "h1", "title", [])
];
$p2 = [new Page("qwer2", "h1", "title", []),
    new Page("asdf2", "h1", "title", []),
    new Page("zxcv2", "h1", "title", [])
];
$lvls = new LevelsWrapper();

$lvls->addNewLevel($p1);
//var_export($lvls);

//echo "\n\n\n";

$lvls->addNewLevel($p2);
//var_export($lvls);

echo "\n\n\n";
$lvls->removePage(new Page("asdf2", "h1", "title", []));
var_export($lvls);
