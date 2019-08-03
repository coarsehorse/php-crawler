<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 004 04.11.18
 * Time: 10:39 PM
 */

class CrawlingTask implements JsonSerializable
{
    /**
     * @var int $id The task id based on creation time.
     */
    private $id;

    /**
     * @var string $url The URL to crawl.
     */
    private $url;

    /** @var string The receiver email. */
    private $email;

    /**
     * CrawlTask constructor.
     * @param string $url
     * @param string $email
     */
    public function __construct(int $id, string $url, string $email)
    {
        $this->id = $id;
        $this->url = $url;
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
    public function getEmail(): string
    {
        return $this->email;
    }

    public function __toString(): string
    {
        return "CrawlingTask[id={$this->id} url={$this->url}, email={$this->email}]";
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
            "id" => $this->id,
            "url" => $this->url,
            "email"=> $this->email
        ];
    }
}