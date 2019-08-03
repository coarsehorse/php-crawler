<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09.11.2018
 * Time: 19:12
 */

require_once __DIR__ . "/../crawler/WebCrawler.php";

$fake_domain = "https://domain.com";

$fake_urls = [
    "https://domain.com/aaaaa/cv/bbbb",
    "https://domain.com/cv/",

    "https://domain.com/about/us",
    "https://domain.com/qwer/about/us",

    "https://domain.com/cv.pdf/aaaa/",
    "https://domain.com/some.pdf"
];

$fake_rules = ["/cv/", "*/about", ".pdf$"];


//$fake_rules = WebCrawler::robotsDisallowReader("https://hotline.ua/");

$robotsDisallowRules = [];
foreach ($fake_rules as $r)
    $robotsDisallowRules[] = WebCrawler::constructRegexFromRobotsRule($r, $fake_domain);

$remaining = array_values(array_filter($fake_urls, function (string $link) use ($robotsDisallowRules) {
    foreach ($robotsDisallowRules as $rule) {
        if (preg_match($rule, $link))
            return false;
    }
    return true;
}));

var_export($remaining);