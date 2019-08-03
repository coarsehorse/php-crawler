<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 29.10.2018
 * Time: 18:39
 */

require_once __DIR__ . "/../domain/Pair.php";

/**
 * Class Page - Data structure that represents web page.
 * Provides convenient access to the page data.
 */
class Page implements JsonSerializable
{
    /**
     * @var string $url
     */
    private $url;
    /**
     * @var string $h1
     */
    private $h1;
    /**
     * @var string $title
     */
    private $title;
    /**
     * @var string[] $domainLinks
     */
    private $domainLinks;

    /** @var Pair[] $hreflangs */
    private $hreflangs;

    /** @var string[] $images */
    private $images;

    /** @var string $canonicalUrl */
    private $canonicalUrl;

    /** @var bool $noIndex */
    private $noIndex;

    /**
     * Page constructor.
     * @param string $url
     * @param string $h1
     * @param string $title
     * @param string[] $domainLinks
     * @param Pair[] $hreflangs
     * @param string[] $images
     * @param string $canonicalUrl
     * @param bool $noIndex
     */
    public function __construct(string $url, string $h1, string $title, array $domainLinks,
                                array $hreflangs, $images, string $canonicalUrl, bool $noIndex)
    {
        $this->url = $url;
        $this->h1 = $h1;
        $this->title = $title;
        $this->domainLinks = array_values(array_unique($domainLinks));
        $this->hreflangs = $hreflangs;
        $this->images = $images;
        $this->canonicalUrl = $canonicalUrl;
        $this->noIndex = $noIndex;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getH1(): string
    {
        return $this->h1;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string[]
     */
    public function getDomainLinks(): array
    {
        return $this->domainLinks;
    }

    /**
     * @return Pair[]
     */
    public function getHreflangs(): array
    {
        return $this->hreflangs;
    }

    /**
     * @return string[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return string
     */
    public function getCanonicalUrl(): string
    {
        return $this->canonicalUrl;
    }

    /**
     * @return bool
     */
    public function isNoIndex(): bool
    {
        return $this->noIndex;
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
            "url" => $this->url,
            "h1" => $this->h1,
            "title" => $this->title,
            "domainLinks" => $this->domainLinks,
            "hreflangs" => $this->hreflangs,
            "images" => $this->images,
            "canonicalUrl" => $this->canonicalUrl,
            "noIndex" => $this->noIndex
        ];
    }


    /**
     * [Important] Change carefully, toString() used for Page object comparison in array_unique().
     * Represents page like its url.
     * @return string - The Page in string representation.
     */
    public function __toString(): string
    {
        return "Page[url={$this->url}]";
    }
}