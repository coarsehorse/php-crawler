<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.10.2018
 * Time: 12:18
 */

require_once __DIR__ . "/../domain/crawling/Level.php";
require_once __DIR__ . "/../domain/crawling/LevelsWrapper.php";

$l1 = ["qwe", "asd", "zxc"];
$l2 = ["1qwe", "2asd", "3zxc"];
$lw = new LevelsWrapper();
$lw->addNewLevel($l1);
$lw->addNewLevel($l2);

echo json_encode($lw, JSON_PRETTY_PRINT);