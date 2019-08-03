<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15.11.2018
 * Time: 10:34
 */

require_once __DIR__ . "/../../util/Utils.php";

/**
 * Class Hreflang - Data structure that represents hreflang element.
 */
class Hreflang implements JsonSerializable
{
    /** @var string $rel */
    private $rel;

    /** @var string $hreflang */
    private $hreflang;

    /** @var string $href */
    private $href;

    /**
     * Hreflang constructor.
     * @param string $rel
     * @param string $hreflang
     * @param string $href
     */
    public function __construct(string $hreflang, string $href)
    {
        $this->rel = "alternate";
        $this->hreflang = $hreflang;
        $this->href = $href;
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
            "rel" => $this->rel,
            "hreflang" => $this->hreflang,
            "href" => $this->href
        ];
    }

    /**
     * Serializes object to sitemap xml representation.
     *
     * @return string - serialized object in sitemap xml representation.
     */
    public function xmlSerialize(): string
    {
        $escaped = Utils::escapeXmlAmpersants($this->href);
        return "\t\t<xhtml:link\n"
            . "\t\t\trel=\"{$this->rel}\"\n"
            . "\t\t\threflang=\"{$this->hreflang}\"\n"
            . "\t\t\thref=\"{$escaped}\"/>";
    }

    public function __toString(): string
    {
        return "Hreflang[rel={$this->rel} hreflang={$this->hreflang} href={$this->href}]";
    }
}