<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 19.10.2018
 * Time: 17:23
 */

require_once __DIR__ . "/../domain/crawling/LevelsWrapper.php";
require_once __DIR__ . "/../domain/Page.php";
require_once __DIR__ . "/../logger/Logger.php";

/***
 * Class with the crawling functionality.
 */
class WebCrawler
{
    /**
     * Parses specified page and constructs Page object.
     *
     * @param $url string The url to parse.
     * @param $crawledLinks string[] The already crawled link.
     * @return Page object or null if the page http status != 200.
     */
    public static function parsePage($url, $crawledLinks)
    {
        // To be sure that following "/" is exists
        $url = self::fixUrl($url);

        // Getting the page xpath
        $headers = @get_headers($url);
        if(!$headers || substr($headers[0], 9, 3) !== "200") { // headers[0] should be "HTTP/1.1 200 OK"
            return null;
        }

        $contents = @file_get_contents($url);
        if (!$contents)
            return null;
        $dom = new DOMDocument();
        @$dom->loadHTML($contents);
        $xpath = new DOMXpath($dom);

        // Collecting links
        $linksQuery = $xpath
            ->query("//a/@href");
        if (self::isXpathQueryOk($linksQuery, "links", $url)) {
            $links = [];
            // Convert DOMNodeList element to array of links
            foreach ($linksQuery as $linkDOM)
                $links[] = trim($linkDOM->textContent);

            // Filter out fake links(#, mailto:, etc.)
            $links = array_values(array_filter($links, function ($link) {
                return (!(self::contains("#", $link))
                    and !(self::contains("javascript:void(0)", $link))
                    and !(self::contains("mailto:", $link))
                    and !(self::contains("tel:", $link))
                    and !(self::contains("javascript:;", $link))
                );
            }));

            // Add following "/" if not exists
            $links = array_map(function ($link) {
                return self::fixUrl($link);
            }, $links);

            // Prepend relative links
            $convertedLinks = [];
            foreach ($links as $lnk) {
                $absRel = self::convertRelativeURLtoAbsolute($lnk, $url);
                if (!is_null($absRel))
                    $convertedLinks[] = $absRel;
            }
            $links = $convertedLinks;

            // Removes duplicates
            $links = array_values(array_unique($links));

            // Filter out urls that not contain domain in the start
            $domain = explode('/', $url)[2];
            //  critical
            $links = array_values(array_filter($links, function ($link) use ($domain) {
                return preg_match("/^(http|https):\/\/" . $domain . ".*$/", $link) === 1;
            }));
        } else
            $links = [];

        // Collecting h1
        $h1Query = $xpath->query("//h1");
        if (self::isXpathQueryOk($h1Query, "h1", $url))
            $h1 = trim($h1Query->item(0)->textContent);
        else
            $h1 = "no_h1";

        // Collecting title
        $titleQuery = $xpath->query("//title");
        if (self::isXpathQueryOk($titleQuery, "title", $url))
            $title = $titleQuery->item(0)->textContent;
        else
            $title = "no_title";

        // Collecting hrefLangs
        $hreflangPairs = [];
        $hreflangsQuery = $xpath->query("//link[contains(@rel, 'alternate')]");
        if (self::isXpathQueryOk($hreflangsQuery, "hreflangs", $url)) {
            foreach ($hreflangsQuery as $hreflangDomNode) {
                $hreflang = $xpath->evaluate("@hreflang", $hreflangDomNode);
                $href = $xpath->evaluate("@href", $hreflangDomNode);

                if ($hreflang->length !== 0 and $href->length !== 0)
                    $hreflangPairs[] = new Pair($hreflang->item(0)->textContent, $href->item(0)->textContent);
            }
        }

        // Collecting imgs
        $imgsQuery = $xpath->query("//img/@src");
        if (self::isXpathQueryOk($imgsQuery, "imgs", $url)) {
            $imgs = [];
            // Convert DOMNodeList element to array of imgs
            foreach ($imgsQuery as $imgDOM)
                $imgs[] = trim($imgDOM->textContent);

            // Filter out imgs which contains encoded imgs as text
            $imgs = array_values(array_filter($imgs, function($img) use ($url) {
                if (preg_match('/^data:.*$/', $img))
                    return false;
                return true;
            }));

            // Resolve relative img links(convert to the absolute)
            $convertedImgs = [];
            foreach ($imgs as $img) {
                $absRel = self::convertRelativeURLtoAbsolute($img, $url);
                if (!is_null($absRel))
                    $convertedImgs[] = $absRel;
            }
            $imgs = $convertedImgs;

            // Get rid of trailing slash, these are images (.png instead of .png/)
            $imgs = array_map(function($img) {
                $chars = str_split($img);
                if (array_slice($chars, count($chars) - 1)[0] === "/")
                    return join('', array_slice($chars, 0, count($chars) - 1));
                else
                    return $img;
            }, $imgs);

            // Removes duplicates
            $imgs = array_values(array_unique($imgs));
        }
        else
            $imgs = [];

        // Checking pagination pattern
        $m = [];
        preg_match('/^((http|https):\/\/.*\/)(page|p)\/\d+\/$/', $url, $m);

        if (count($m) !== 0) { // pagination url detected, extract parent(url without /page/1)
            $links[] = self::fixUrl($m[1]);
        }

        // Checking get parameters pattern
        if (self::contains("?", $url)) {
            $beforeGet = self::fixUrl(explode("?", $url)[0]);
            $links[] = $beforeGet;
        }

        // Checking canonical
        $canonicalUrl = "";
        $canonicalQuery = $xpath->query("//link[contains(@rel, 'canonical')]/@href");
        if (self::isXpathQueryOk($canonicalQuery, "canonical", $url)) {
            $canonicalUrl = self::fixUrl($canonicalQuery->item(0)->textContent);
            if ($canonicalUrl !== $url) // if canonical forwards to another url
                $links[] = $canonicalUrl; // parse later
        }

        // Checking noindex
        $noIndex = false;
        $noIndexQuery = $xpath->query("//meta[contains(@content, 'noindex')]");
        if (self::isXpathQueryOk($noIndexQuery, "noIndex", $url))
            $noIndex = true;

        return new Page($url, $h1, $title, $links, $hreflangPairs, $imgs, $canonicalUrl, $noIndex);
    }


    /**
     * Starts the recursive crawling process.
     *
     * @param string $url The site root. Crawling will start from that url.
     * @param Rules $rules The url exceptions/approvals.
     * @return LevelsWrapper - the crawled site inside LevelsWrapper object.
     */
    public static function startCrawling(string $url, Rules $rules): LevelsWrapper
    {
        $robotsDisallowRules = self::robotsDisallowReader($url);
        $robotsDisallowRegexes = [];
        foreach ($robotsDisallowRules as $r)
            $robotsDisallowRegexes[] = WebCrawler::constructRegexFromRobotsRule($r, $url);
        $rules->appendExceptions($robotsDisallowRegexes);

        return self::crawl([self::fixUrl($url)], [], new LevelsWrapper(), [], $rules);
    }

    /**
     * Crawl the one level of the site. Recall itself if not crawled links was found.
     *
     * @param $toCrawl string[] The links to crawl(current level links).
     * @param $crawledLinks string[] The already crawled links(used in recursion).
     * @param $crawledLevels LevelsWrapper The already crawled levels. Represented in LevelsWrapper object.
     * @param $last_3_timeouts int[] The array with the last 3 unique timeouts.
     * Used to bypass site protection from the bots. This array updates after every crawled link.
     * @param $rules Rules The url exceptions/approvals.
     * @return LevelsWrapper - crawled site inside LevelsWrapper object.
     */
    private static function crawl($toCrawl, $crawledLinks, $crawledLevels,
                                  $last_3_timeouts, $rules) {
        // Crawl this level
        $crawledLinksNumber = count($crawledLinks);
        $nulls = 0;
        $crawledPages = [];
        foreach ($toCrawl as $link) {
            $timeout = self::getTimeoutNotInArray($last_3_timeouts, 1, 5);
            array_push($last_3_timeouts, $timeout);
            $last_3_timeouts = array_slice($last_3_timeouts, -3);
            //sleep($timeout);
            Logger::log("[$crawledLinksNumber] crawling $link ... ", false);
            $page = self::parsePage($link, $crawledLinks);
            if (is_null($page)) { // Http status != 200
                Logger::log("Got not 200 on $link");
                $nulls += 1;
            } else {
                $crawledPages[] = $page;
                if ($page->getUrl() !== $link) // parsed canonical url or something like this
                    $crawledLinks[] = $page->getUrl(); // take into account parsed url
            }
            $crawledLinks[] = $link;
            $crawledLinksNumber++;
        }
        echo "toCrawl: " . count($toCrawl) . " nulls: " . $nulls . "\n";
        Logger::log("toCrawl: " . count($toCrawl) . " nulls: " . $nulls);
        $crawledLevels->addNewLevel($crawledPages);

        // Remove crawled array duplicates
        $crawledLinks = array_values(array_unique($crawledLinks));


        // Get the next level links
        $nextLevelLinks = [];
        foreach ($crawledPages as $p) {
            $nextLevelLinks = array_merge($nextLevelLinks, $p->getDomainLinks());
        };

        // Find the unique next level links
        $uniqNextLvlLinks = array_values(array_unique($nextLevelLinks));

        // Get the links that are not already crawled
        $remaining = array_values(array_filter($uniqNextLvlLinks, function (string $link) use ($crawledLinks) {
            return !in_array($link, $crawledLinks);
        }));

        // Validate rules (robots.txt + others).
        $remaining = array_values(array_filter($remaining, function (string $link) use ($rules) {
            return $rules->validate($link);
        }));

        // Get the links from the next level that already crawled
        // and increase their counters in crawledLevels
        $toUpdate = array_values(array_filter($nextLevelLinks, function (string $link) use ($crawledLinks) {
            return in_array($link, $crawledLinks);
        }));
        foreach ($toUpdate as $updLink)
            $crawledLevels->incrementPageCounter(new Page($updLink, "", "",
                [], [], [], "", true));

        // Return the crawledLevels or crawl not crawled links
        if (count($remaining) === 0) {
            return $crawledLevels;
        } else {
            return self::crawl($remaining, $crawledLinks, $crawledLevels, $last_3_timeouts, $rules);
        }
    }

    // HELPER METHODS //

    /**
     * Generates random timeout in specified range($maxTimeout >= timeout >= $minTimeout).
     * Checks whether newly generated timeout not in $timeoutArray array.
     * If so call itself again(regenerate timeout).
     *
     * @param array $timeoutArray The timeouts that the new timeout will not be equal to.
     * @param int $minTimeout The min possible timeout value(inclusive)
     * @param int $maxTimeout The max possible timeout value(inclusive)
     * @return int newly generated timeout which value not in $timeoutArray.
     */
    private static function getTimeoutNotInArray(array $timeoutArray, int $minTimeout, int $maxTimeout): int
    {
        try {
            $delay = random_int($minTimeout, $maxTimeout);
        } catch (Exception $e) {
            die($e->getMessage());
        }

        if (in_array($delay, $timeoutArray))
            return self::getTimeoutNotInArray($timeoutArray, $minTimeout, $maxTimeout);
        else
            return $delay;
    }

    /**
     * Checks whether xPath query found something. Generate warning otherwise.
     *
     * @param DOMNodeList $xpathQuery The xPath to check.
     * @param string $queryName The query name, smth that describes given xPath. Used for warning.
     * @param string $link The link to the page where this xPath must working. Used for warning.
     * @param bool $printWarning If this flag is true, the warning message will be
     * printed(if xpath query found nothing). By default message will be printed.
     * @return bool - true if the given xPath found smth, false(and print warning) otherwise.
     */
    private static function isXpathQueryOk(DOMNodeList $xpathQuery, string $queryName, string $link, $printWarning = false)
    {
        if ($xpathQuery->length === 0) {
            if ($printWarning)
                Logger::log("\n[WARNING] Not found \"$queryName\" at $link");
            return false;
        }
        return true;
    }

    /**
     * Checks whether the $needle occurs in $haystack.
     *
     * @param string $needle The string to search.
     * @param string $haystack The string to search in.
     * @return bool true if occurs, false otherwise.
     */
    private static function contains(string $needle, string $haystack): bool
    {
        return strpos($haystack, $needle) !== false;
    }

    /**
     * Checks whether the specified url contains following slash.
     *
     * @param string $url The url to check.
     * @return string - original url if it contains the following slash, url with "/" otherwise.
     */
    public static function fixUrl(string $url): string
    {
        $lastChar = substr($url, -1);

        if ($lastChar !== "/")
            return $url . "/";
        else
            return $url;
    }

    /**
     * Reads robots.txt from $domainUrl + "/robots.txt".
     * Firstly checks headers, if file exists - downloads it,
     * autodetect line ending, split file line by line.
     * Than try to get Disallow rules for "User-Agent: Googlebot",
     * if no rules found, try to get rules for "User-Agent: *".
     *
     * @param string $domainUrl The domain url.
     * @return string[] - array of parsed Disallow rules.
     */
    public static function robotsDisallowReader(string $domainUrl): array
    {
        $url = self::fixUrl($domainUrl) . "robots.txt";

        // Check page accessibility
        $headers = @get_headers($url);
        if (!$headers || substr($headers[0], 9, 3) !== "200")
            return [];

        // Get file contents
        $robots = @file_get_contents($url);
        // Determine line ending
        $lineEnding = "";
        if (self::contains("\r", $robots)) {
            if (self::contains("\r\n", $robots))
                $lineEnding = "\r\n";
            else
                $lineEnding = "\r";
        } else
            $lineEnding = "\n";
        $robots = explode($lineEnding, $robots);

        $disallows = self::readUserAgentDisallowRules("User-Agent: Googlebot", $robots);
        if (count($disallows) === 0)
            $disallows = self::readUserAgentDisallowRules("User-Agent: *", $robots);

        return $disallows;
    }

    /**
     * Reads Disallow rules for the specified userAgent.
     *
     * @param $userAgent string The user agent name in format "User-Agent: _NAME_".
     * @param $robots string[] The robots.txt lines in an array.
     * @return array string[] - found rules.
     */
    public static function readUserAgentDisallowRules($userAgent, $robots): array
    {
        // Parse disallows
        $disallows = [];
        $flag = false; // userAgent flag
        foreach ($robots as $rob) {
            // Parse only userAgent rules
            if ($flag === false && self::contains(strtolower($userAgent), // start of userAgent section
                    strtolower($rob))) {
                $flag = true;
                continue;
            }
            elseif ($flag === true && self::contains(strtolower("User-agent"), // end of any agent section
                    strtolower($rob)))
                break;
            if ($flag) {
                $m = [];
                preg_match('/^Disallow: (.*)$/', $rob, $m);
                if (count($m) !== 0)
                    $disallows[] = $m[1];
            }
        }
        $disallows = array_values(array_unique($disallows));
        // Remove empty "rules"
        $disallows = array_values(array_filter($disallows, function (string $dis) {
            if ($dis === "")
                return false;
            return true;
        }));

        return $disallows;
    }

    /**
     * Robots rules are simple: * - any character in any quantity, $ - end of the url.
     * So it's easy to convert it into valid regex: * to .* and $ to $.
     * Also this method escapes any reserved regex symbol in the robots rule.
     * Reserved symbols: [\^/.|?+(){}]
     * * Regex is "Partial" because the rules in robots.txt intend that they will be applied
     * to the site urls with trailing domain. Example:
     * \/travels\/ and https:\/\/holidays.com\/travels\/ are not the same regexes.
     *
     * @param string $robotsRule The rule from the robots.txt
     * @return string - valid(but partial) regex from the rule.
     */
    public static function convertRobotsRuleToPartialRegex(string $robotsRule): string
    {
        $reservedCharacters = "[\^/.|?+(){}]"; // No $, no * - their have special meanings in robots rules
        $searchArray = [];
        $replaceArray = [];
        for ($i = 0; $i < strlen($reservedCharacters); $i++) {
            $searchArray[] = $reservedCharacters[$i];
            $replaceArray[] = "\\" . $reservedCharacters[$i];
        }
        $robotsRule = str_replace($searchArray, $replaceArray, $robotsRule);
        $robotsRule = str_replace("*", ".*", $robotsRule);
        // If "$" present but rule not starts from "/"
        if ($robotsRule[-1] === "$" and ($robotsRule[0] . $robotsRule[1]) !== "\/")
            $robotsRule = ".*" . $robotsRule;

        return $robotsRule;
    }

    /**
     * Constructs the valid php regex that can be used to validate site URLs.
     *
     * @param $robotsRule string The rule from the robots.txt.
     * @param $siteDomain string The site domain URL, like "https://example.com".
     * @return string - valid regex based on the site domain and robots.txt rule of that site.
     */
    public static function constructRegexFromRobotsRule($robotsRule, $siteDomain): string
    {
        // Get clear domain
        $domain = array_slice(explode('/', $siteDomain), 2, 1)[0];
        // Extend domain with any protocol
        $protocolDomain = "(http|https)://" . $domain;
        // Prepare $protocolDomain to be regex
        $protocolDomain = str_replace("/", "\/", $protocolDomain);
        // Get ruleRegex from robots rule
        $ruleRegex = self::convertRobotsRuleToPartialRegex($robotsRule);
        // Resolve trailing "/" in robots rule problem
        if (($ruleRegex[0] . $ruleRegex[1]) !== "\/") // if robots rule not starts from "/"
            $protocolDomain = $protocolDomain . "\/";

        return "/" . $protocolDomain . $ruleRegex . "/"; // Construct valid php regex
    }

    /**
     * Converts relative url to the absolute.
     *
     * @param $relativeUrl string The relative url.
     * @param $urlAbsoluteLocation string The absolute url;
     * @return null|string - converted relative url or null if it's impossible to convert.
     */
    public static function convertRelativeURLtoAbsolute($relativeUrl, $urlAbsoluteLocation): ?string
    {
        // Check whether the url is empty
        if ($relativeUrl === "")
            return null;
        // Check whether the url is already absolute
        else if (preg_match('/^(http|https):\/\/.*$/', $relativeUrl))
            return self::fixUrl($relativeUrl);

        // To bee sure that input URLs has trailing '/'
        $urlAbsoluteLocation = self::fixUrl($urlAbsoluteLocation);
        $relativeUrl = self::fixUrl($relativeUrl);

        // Common data for the all cases
        $protocol = array_slice(explode('/', $urlAbsoluteLocation), 0, 1)[0] . '//';
        $domain = self::fixUrl(array_slice(explode('/', $urlAbsoluteLocation), 2, 1)[0]);
        $newPathStart = $urlAbsoluteLocation;

        // Case relativeUrl consists of one character like "/" or "a"
        if (strlen($relativeUrl) < 2) {
            if ($relativeUrl[0] === '/') // root
                return $protocol . $domain;
            else if ($relativeUrl[0] === '.') // 'this directory'
                return $urlAbsoluteLocation;
            else if (preg_match('/^[\w-]*$/', $relativeUrl[0]))
                return self::fixUrl($urlAbsoluteLocation . $relativeUrl[0]);
            else
                return false;
        }

        // Case ../../path (working only if url starts from sequence of ../../../ and so on)
        $stepsBack = substr_count($relativeUrl, '../');
        if ($stepsBack > 0) {
            $locLevels = array_slice(explode('/', $urlAbsoluteLocation), 2, -1);
            if ($stepsBack <= (count($locLevels) - 1)) { // we can do steps back
                $newPathStart = join('/',
                        array_slice($locLevels, 0, count($locLevels) - $stepsBack)) . '/';
            } else // we can't step back behind the domain(0 level)
                return null;

            // Remove stepbacks
            $relativeUrl = str_replace('../', '', $relativeUrl);
        }

        // Case ./path/path1 (returned in the next case)
        if (($relativeUrl[0] . $relativeUrl[1]) === './')
            $relativeUrl = join('', array_slice(str_split($relativeUrl), 2));

        // Case path/path1 (equals to ./path/path1)
        if ($relativeUrl[0] !== "/") {
            if (preg_match('/^(http|https):\/\/.*$/', $newPathStart))
                return $newPathStart . $relativeUrl;
            else
                return $protocol . $newPathStart . $relativeUrl;
        }

        // Case //path/path1
        if (($relativeUrl[0] . $relativeUrl[1]) === '//')
            return $protocol . join('', array_slice(str_split($relativeUrl), 2));

        // Case /path/path1
        if ($relativeUrl[0] === "/" and $relativeUrl[1] !== "/")
            return $protocol . $domain . join('', array_slice(str_split($relativeUrl), 1));

        return null;
    }
}
