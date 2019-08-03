<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15.11.2018
 * Time: 10:19
 */

require_once __DIR__ . "/../crawling/LevelsWrapper.php";
require_once __DIR__ . "/../Pair.php";
require_once __DIR__ . "/../Page.php";
require_once __DIR__ . "/../Rules.php";
require_once __DIR__ . "/SitemapUrl.php";
require_once __DIR__ . "/Hreflang.php";
require_once __DIR__ . "/../../util/Utils.php";

/**
 * Class Sitemap - Data wrapper for the site sitemap.
 * Provides convenient methods for working with the sitemap elements.
 */
class Sitemap implements JsonSerializable
{
    /** @var string $header */
    private $header;

    /** @var SitemapUrl[] $sitemapUrls */
    private $sitemapUrls;

    /** @var string $footer */
    private $footer;

    /**
     * Sitemap constructor. Converts $levelsWrapper object to the sitemap
     * with taken into account provided $rules. Also you can specify custom $changefreq.
     */
    public function __construct(LevelsWrapper $levelsWrapper, Rules $rules,
                                string $changefreq = "daily")
    {
        $this->header = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $this->header .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
            . ' xmlns:xhtml="http://www.w3.org/1999/xhtml/">';
        $this->sitemapUrls = [];
        $this->footer = '</urlset>';

        date_default_timezone_set('Europe/Kiev');
        $lastmod = date("Y-m-d");

        foreach ($levelsWrapper->getLevels() as $level) { // process level by level
            // Evaluate priority
            $lvlNum = $level->getLevelNum();
            $priority = 0.9;
            if ($lvlNum < 3)
                $priority = 1.0;
            elseif ($lvlNum < 5)
                $priority = 0.9;
            else
                for ($i = 0; $i < $lvlNum - 4; $i++) { // - 4 because 1-4 lvls are reserved
                    $priority = round($priority - 0.1, 1);
                    if ($priority <= 0.5)
                        break;
                }
            $priority = number_format($priority, 1);

            // Get the level pages
            $pages = array_map(function (Pair $pageCounter) {
                return $pageCounter->getKey();
            }, $level->getLevelPages());

            // Filter sitemap pages
            $rules->appendExceptions(['/^.*(page|p)\/\d+\/$/', '/^.*\?.*$/']); // paginations + get params
            $filteredPages = array_values(array_filter($pages, function (Page $page) use ($rules) {
                // Filter out pages with canonicals that forward to another page
                if ($page->getCanonicalUrl() !== "" and $page->getCanonicalUrl() !== $page->getUrl())
                    return false;
                if ($page->isNoIndex())
                    return false;
                return $rules->validate($page->getUrl());
            }));

            // Sort by name
            usort($filteredPages, function (Page $p1, Page $p2) {
                if ($p1->getUrl() === $p2->getUrl())
                    return 0;
                return ($p1->getUrl() < $p2->getUrl()) ? -1 : 1;
            });

            // Construct Sitemap urls
            foreach ($filteredPages as $p) {
                /** @var Page $p */
                $hreflangs = $p->getHreflangs();
                $hreflangs = array_map(function(Pair $hreflang) {
                    return new Hreflang($hreflang->getKey(), $hreflang->getValue());
                }, $hreflangs);

                $this->sitemapUrls[] = new SitemapUrl($p->getUrl(), $lastmod, $changefreq, $priority, $hreflangs);
            }
        }
    }

    /**
     * Split an sitemap into chunks(valid sitemaps with less <url> elements inside).
     * Each chunk will consists of $chunkSize elements or less.
     *
     * @param int $chunkSize The quantity of <url> elements in the resulting sitemaps.
     * @return array - the valid sitemaps(original sitemap chunks) in xml text form.
     */
    public function createSitemapChunks($chunkSize = 10): array
    {
        $urlChunks = array_chunk($this->sitemapUrls, $chunkSize);
        $urlChunksXML = [];

        foreach ($urlChunks as $chunk) {
            /** @var SitemapUrl[] $chunk */
            $urlsXml = [];

            foreach ($chunk as $url)
                $urlsXml[] = $url->xmlSerialize();

            $urlsXml = join("\n", $urlsXml);

            $urlChunksXML[] = $this->header . "\n" . $urlsXml . "\n" . $this->footer . "\n";
        }

        return $urlChunksXML;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            "header" => $this->header,
            "sitemapUrls" => $this->sitemapUrls,
            "footer" => $this->footer
        ];
    }

    public function __toString(): string
    {
        $tempSitemapUrls = [];
        foreach ($this->sitemapUrls as $surl)
            $tempSitemapUrls[] = $surl->__toString();
        $tempSitemapUrls = join(", ", $tempSitemapUrls);
        $tempSitemapUrls = "[" . $tempSitemapUrls . "]";

        return "Sitemap[header={$this->header} sitemapUrls={$tempSitemapUrls} footer={$this->footer}]";
    }
}