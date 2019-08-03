<?php

require_once __DIR__ . "/../domain/crawling/Level.php";

$l = new Level(12, ["qwe", "asd", "zxc"]);

echo json_encode($l, JSON_PRETTY_PRINT);