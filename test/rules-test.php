<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.11.2018
 * Time: 12:02
 */

require_once __DIR__ . "/../domain/Rules.php";

$rules = new Rules(["bad1", "bad2"], ["ok1", "ok2"]);
var_export($rules . "\n");
var_export(json_encode($rules, JSON_PRETTY_PRINT));

