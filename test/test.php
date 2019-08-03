<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.11.2018
 * Time: 16:07
 */

//$lvlNum = 11;
//
//$priority = 0.9;
//
//if ($lvlNum < 3)
//    $priority = 1.0;
//elseif ($lvlNum < 5)
//    $priority = 0.9;
//else
//    for ($i = 0; $i < $lvlNum - 4; $i++) { // - 4 because 1-4 lvls are reserved
//        $priority = round($priority - 0.1, 1);
//        if ($priority <= 0.5)
//            break;
//    }
//
//var_export($priority);

$a = [1, 2, 3, 4, 5, 6, 7];

//var_export(array_chunk($a, 9));

//var_export(date_timestamp_get(date_create()));
//echo "\n";
//usleep(1000000);
//var_export(date_timestamp_get(date_create()));

//var_export(microtime(true));
//echo "\n";
//usleep(1);
//var_export(microtime(true));

//require_once __DIR__ . "/../util/Utils.php";
//
//echo Utils::measureGzSize("asdasdasdsaasdasdasdsd") . "\n";
//echo Utils::measureGzSize("asdasdasdsaasdasdasds") . "\n";
//echo Utils::measureGzSize("asdasdasdsaasdasdasdsd") . "\n";

//echo 10 / 100;
//echo "\n";
//echo 0 ?? 1;

$a = ["a", "b", "c"];
$b = ["d", "e", "f"];
//$c = array_merge($a, $b);

//var_export($c);

$res = @mkdir("/path1");
echo "\"$res\"\n";