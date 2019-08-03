<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15.11.2018
 * Time: 10:28
 */

require_once __DIR__ . "/../../util/Utils.php";

/**
 * Class SitemapUrl - Data structure that represents
 * sitemap url element with all its subelements.
 */
class SitemapUrl implements JsonSerializable
{
    /** @var string $loc */
    private $loc;

    /** @var string $lastmod */
    private $lastmod;

    /** @var string $changefreq */
    private $changefreq;

    /** @var float $priority */
    private $priority;

    /** @var Hreflang[] $hreflangs */
    private $hreflangs;

    /**
     * SitemapUrl constructor.
     * @param string $loc
     * @param string $lastmod
     * @param string $changefreq
     * @param float $priority
     * @param Hreflang[] $hreflangs
     */
    public function __construct(string $loc, string $lastmod, string $changefreq, float $priority, array $hreflangs)
    {
        $this->loc = $loc;
        $this->lastmod = $lastmod;
        $this->changefreq = $changefreq;
        $this->priority = $priority;
        $this->hreflangs = $hreflangs;
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
            "loc" => $this->loc,
            "lastmod" => $this->lastmod,
            "priority" => $this->priority,
            "hreflangs" => $this->hreflangs
        ];
    }

    /**
     * Serializes object to sitemap xml representation.
     *
     * @return string - serialized object in sitemap xml representation.
     */
    public function xmlSerialize(): string
    {
        $tempHreflangs = [];
        foreach ($this->hreflangs as $h)
            $tempHreflangs[] = $h->xmlSerialize();
        $tempHreflangs = join("\n", $tempHreflangs);
        $tempPriority = number_format($this->priority, 1);
        $escapedLoc = Utils::escapeXmlAmpersants($this->loc);

        return "\t<url>\n"
            . "\t\t<loc>{$escapedLoc}</loc>\n"
            . "\t\t<lastmod>{$this->lastmod}</lastmod>\n"
            . "\t\t<changefreq>{$this->changefreq}</changefreq>\n"
            . "\t\t<priority>{$tempPriority}</priority>\n"
            . (empty($tempHreflangs) ? "" : "$tempHreflangs\n")
            . "\t</url>";
    }

    public function __toString(): string
    {
        $tempHreflangs = [];
        foreach ($this->hreflangs as $h)
            $tempHreflangs[] = $h->__toString();
        $tempHreflangs = join(", ", $tempHreflangs);
        $tempHreflangs = "[" . $tempHreflangs . "]";
        $tempPriority = number_format($this->priority, 1);

        return "SitemapUrl[loc={$this->loc} lastmod={$this->lastmod} changefreq={$this->changefreq}"
            . " priority={$tempPriority} hreflangs={$tempHreflangs}]";
    }
}