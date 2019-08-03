<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.11.2018
 * Time: 10:36
 */

require_once __DIR__ . "/../domain/Rules.php";
require_once __DIR__ . "/../util/Utils.php";

/**
 * Class ImageSitemapSerializer - utility that serializes LevelsWrapper object to the valid xml images sitemap.
 */
class ImagesSitemapSerializer
{
    /**
     * Serializes given LevelsWrapper.
     *
     * @param LevelsWrapper $levelsWrapper The object to serialize.
     * @param Rules $rules The rules for the images sitemap.
     * @return string - valid images sitemap in xml format.
     */
    public static function serialize(LevelsWrapper $levelsWrapper, Rules $rules): string {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n\t\t";
        $xml .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'. "\n";
        $xml .= self::imagesSitemapBuilder($levelsWrapper, $rules);
        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Builds the repeated part of images sitemap.
     * Sorting pages by levels and after that by page url.
     *
     * @param LevelsWrapper $levelsWrapper The site crawled levels.
     * @param Rules $rules The rules for the images sitemap.
     * @return string - repeated xml part of images sitemap.
     */
    public static function imagesSitemapBuilder(LevelsWrapper $levelsWrapper, Rules $rules): string {
        $xml = "";
        foreach ($levelsWrapper->getLevels() as $level) { // construct per level
            // Get level pages
            $pages = array_map(function (Pair $pageCounterPair) {
                return $pageCounterPair->getKey();
            }, $level->getLevelPages());
            // Sort by name
            usort($pages, function (Page $p1, Page $p2) {
                if ($p1->getUrl() === $p2->getUrl())
                    return 0;
                return ($p1->getUrl() < $p2->getUrl()) ? -1 : 1;
            });
            // Write out to the xml
            foreach ($pages as $p) { /** @var Page $p*/
                // Filter images
                $filteredImages = array_values(array_filter($p->getImages(), function ($img) use ($rules) {
                    // Check the rules
                    return $rules->validate($img);
                }));

                if (count($filteredImages) === 0)
                    continue;

                $xml .= "\t<url>\n"
                    . "\t\t<loc>{$p->getUrl()}</loc>\n";

                foreach ($filteredImages as $img) {
                    $escaped = Utils::escapeXmlAmpersants($img);
                    $xml .= "\t\t<image:image>\n"
                        . "\t\t\t<image:loc>$escaped</image:loc>\n"
                        . "\t\t</image:image>\n";
                }

                $xml .= "\t</url>\n";
            }
        }

        return $xml;
    }

    /**
     * Filters out images that not belongs to the site domain.
     *
     * @param array $imgs The array of image urls.
     * @param string $domainUrl The url of the page where this images from.
     * @return array - filtered images.
     */
    public static function filterImages(array $imgs, $domainUrl): array {
        $domain = explode("/", $domainUrl)[2];
        return array_values(array_filter($imgs, function ($img) use ($domain) {
            return preg_match('/^(http|https):\/\/' . $domain . '\/.*$/', $img) === 1;
        }));
    }
}