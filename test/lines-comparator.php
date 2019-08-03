<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 25.10.2018
 * Time: 10:12
 */

$my = explode(PHP_EOL, file_get_contents("my.txt"));
$frog = explode(PHP_EOL, file_get_contents("frog.txt"));

//var_dump($my);
//var_dump($frog);

if (count($my) === count($frog))
    echo "Number of lines in both files are equal\n";
else
    echo "Numbers of lines in both files are NOT equal\n";

$my_absent = [];
foreach ($frog as $f)
    if (!in_array($f, $my))
        array_push($my_absent, $f);

$frog_absent = [];
foreach ($my as $m)
    if (!in_array($m, $frog))
        array_push($frog_absent, $m);

file_put_contents("my_absent.txt", join("\n", $my_absent));
file_put_contents("frog_absent.txt", join("\n", $frog_absent));

echo "Done\n";
