<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12.11.2018
 * Time: 19:12
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

// Initial data
$absLocat = "https://domain.com/one/two/three";
echo 'Initial data' . "\n";
echo "AbsoluteLocation: $absLocat\n\n";

// path/path1 case
$rel = "path/path1";
$res = WebCrawler::convertRelativeURLtoAbsolute($rel, $absLocat);
echo 'path/path1 case' . "\n";
var_export($res);
echo "\n\n";

// ../../path case
$rel = "../../path";
$res = WebCrawler::convertRelativeURLtoAbsolute($rel, $absLocat);
echo '../../path case' . "\n";
var_export($res);
echo "\n\n";

// ../path1/../path2 case test
/*$rel = "../new_rel_path/../another_one";
$absLocat = "https://domain.com/one/two/three";
$res = WebCrawler::convertRelativeURLtoAbsolute($rel, $absLocat);
var_export($res);*/

// ./path/path1 case
$rel = "./path/path1";
$res = WebCrawler::convertRelativeURLtoAbsolute($rel, $absLocat);
echo './path/path1 case' . "\n";
var_export($res);
echo "\n\n";

// /path/path1 case
$rel = "/path/path1";
$res = WebCrawler::convertRelativeURLtoAbsolute($rel, $absLocat);
echo '/path/path1 case' . "\n";
var_export($res);
echo "\n\n";

// //path/path1 case
$rel = "//path/path1";
$res = WebCrawler::convertRelativeURLtoAbsolute($rel, $absLocat);
echo '//path/path1 case' . "\n";
var_export($res);
echo "\n\n";

// ../.././path extra case
$rel = "../.././path";
$res = WebCrawler::convertRelativeURLtoAbsolute($rel, $absLocat);
echo '../.././path case' . "\n";
var_export($res);
echo "\n\n";

// http://domain.com/path case
$rel = "http://domain.com/path";
$res = WebCrawler::convertRelativeURLtoAbsolute($rel, $absLocat);
echo 'http://domain.com/path case' . "\n";
var_export($res);
echo "\n\n";