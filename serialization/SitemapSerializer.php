<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 23.10.2018
 * Time: 12:35
 */

require_once __DIR__ . "/../domain/crawling/LevelsWrapper.php";
require_once __DIR__ . "/../domain/Pair.php";
require_once __DIR__ . "/../domain/Page.php";
require_once __DIR__ . "/../domain/Rules.php";
require_once __DIR__ . "/../domain/sitemap/Sitemap.php";

/**
 * Class SiteMapXMLSerializer - utility that serializes LevelsWrapper object to the valid xml sitemap.
 */
class SitemapSerializer
{

    /**
     * Serializes given $levelsWrapper object into a valid xml sitemap considering
     * given $rules and restrictions($maxUrlsPerSitemap, $maxSitemapSizeMB).
     *
     * @param LevelsWrapper $levelsWrapper The site crawled levels.
     * @param Rules $rules The sitemap custom exceptons/approvals.
     * @param string $sitemapUrl The future real sitemap url, for example: http://site.com/sitemap.xml
     * Used for building index sitemap.
     * @param int $maxUrlsPerSitemap The max value of <url> elements in the one sitemap file.
     * If more elements is present original sitemap will be chunked into sitemaps
     * with $maxUrlsPerSitemap elements or less each. Also, index sitemap will be created.
     * @param int $maxSitemapSizeMB The max value of GZipped sitemap(or one of the sitemap chunks).
     * If the max value will be exceeded by sitemap or one of its chunks, an attempt will be made
     * to reduce $maxUrlsPerSitemap by $chunkSizeStepPerc percents. This will be repeated until the optimal
     * size of the chunk is found or the chunk size becomes less than $chunkSizeStepPerc(null will be returned).
     * @param int $chunkSizeStepPerc The percents of the $maxSitemapSizeMB.
     * This value will be used in the chunk size selection.
     * @param string $changefreq The sitemap changefreq value.
     * @return array|null - an array of sitemaps in xml format or null
     * if not able to build sitemap with the given restrictions.
     * If a single sitemap was chuncked the first element of returned array will be an index sitemap.
     */
    public static function serialize(LevelsWrapper $levelsWrapper, Rules $rules, $sitemapUrl,
                                     $maxUrlsPerSitemap = 50000, $maxSitemapSizeMB = 50,
                                     int $chunkSizeStepPerc = 10, string $changefreq = "daily"): ?array
    {
        $maxSizeBytes = $maxSitemapSizeMB * 1024 * 1024;
        $chunkSize = $maxUrlsPerSitemap; // will be dynamically change

        $sitemap = new Sitemap($levelsWrapper, $rules, $changefreq); // build sitemap from the whole data

        // Pick up the number of resulting sitemaps
        // considering restrictions($maxUrlsPerSitemap, $maxSitemapSizeMB)
        $chunks = [];
        do {
            $chunks = $sitemap->createSitemapChunks($chunkSize);
            $chunkSizes = array_map(function ($chunk) {
                return Utils::measureGzSize($chunk);
            }, $chunks);
            $tooLarges = array_filter($chunkSizes, function ($chSize) use ($maxSizeBytes) {
                return $chSize > $maxSizeBytes;
            });
            if (count($tooLarges) > 0) { // smallify chunks
                $step = round($maxUrlsPerSitemap / 100 * $chunkSizeStepPerc);
                if ($chunkSize > $step)
                    $chunkSize -= $step;
                else
                    return null;
            }
        } while (count($tooLarges) > 0);

        if (count($chunks) > 1) {
            // Build index sitemap
            $header = "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
            $footer = "</sitemapindex>\n";

            $sitemapDir = explode("sitemap.xml", $sitemapUrl)[0];
            date_default_timezone_set('Europe/Kiev');
            $lastmod = date("Y-m-d");

            $xml = "";
            for ($i = 0; $i < count($chunks); $i++) {
                $xml .= "\t<sitemap>\n"
                    . "\t\t<loc>{$sitemapDir}sitemap{$i}.xml.gz</loc>\n"
                    . "\t\t<lastmod>$lastmod</lastmod>\n"
                    . "\t</sitemap>\n";
            }

            $sitemapIndex = $header . $xml . $footer;

            return array_merge([$sitemapIndex], $chunks);
        }
        else {
            return $chunks;
        }
    }
}