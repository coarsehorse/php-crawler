<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06.11.2018
 * Time: 11:07
 */

class CrawlingResult implements JsonSerializable
{
    /**
     * @var int The crawling task id.
     */
    private $id;

    /**
     * @var string The result body.
     */
    private $resultBody;

    /**
     * @var int The crawling time in sec.
     */
    private $crawlingTime;

    /**
     * CrawlingResult constructor.
     * @param int $id
     * @param string $resultBody
     * @param int $crawlingTime
     */
    public function __construct(int $id, string $resultBody, int $crawlingTime)
    {
        $this->id = $id;
        $this->resultBody = $resultBody;
        $this->crawlingTime = $crawlingTime;
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
    public function getResultBody(): string
    {
        return $this->resultBody;
    }

    /**
     * @return int
     */
    public function getCrawlingTime(): int
    {
        return $this->crawlingTime;
    }

    public function __toString(): string
    {
        return "CrawlingResult[id={$this->id} resultBody=... crawlingTime={$this->crawlingTime}]";
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
            "resultBody" => $this->resultBody,
            "crawlingTime" => $this->crawlingTime
        ];
    }
}