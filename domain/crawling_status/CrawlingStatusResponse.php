<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06.11.2018
 * Time: 12:07
 */

require_once __DIR__ . "/../../domain/crawling_result/CrawlingResult.php";

class CrawlingStatusResponse implements JsonSerializable
{
    /**
     * @var string $status The word that describes current task status.
     */
    private $status;

    /**
     * @var CrawlingResult $crawlingResult The crawling result.
     */
    private $crawlingResult;

    /**
     * CrawlingStatusResponse constructor.
     * @param string $status
     * @param CrawlingResult $crawlingResult
     */
    public function __construct(string $status, CrawlingResult $crawlingResult)
    {
        $this->status = $status;
        $this->crawlingResult = $crawlingResult;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return CrawlingResult
     */
    public function getCrawlingResult(): CrawlingResult
    {
        return $this->crawlingResult;
    }

    public function __toString(): string
    {
        return "CrawlingStatusResponse[status={$this->status} crawlingResult={$this->crawlingResult}]";
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
            "status" => $this->status,
            "crawlingResult" => $this->crawlingResult
        ];
    }
}