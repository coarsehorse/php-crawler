<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06.11.2018
 * Time: 10:12
 */

class CrawlingTaskResponse implements JsonSerializable
{
    /**
     * @var int The task id. 0 if task rejected, >0 otherwise.
     */
    private $id;

    /**
     * @var string The response message.
     */
    private $message;

    /**
     * CrawlingTaskResponse constructor.
     * @param int $id
     * @param string $message
     */
    public function __construct(int $id, string $message)
    {
        $this->id = $id;
        $this->message = $message;
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
    public function getMessage(): string
    {
        return $this->message;
    }

    public function __toString(): string
    {
        return "CrawlingTaskResponse[id={$this->id} message={$this->message}]";
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
            "message" => $this->message
        ];
    }
}