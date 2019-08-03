<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 30.10.2018
 * Time: 10:54
 */


/**
 * Class Pair - Data structure that represents pair of any two objects.
 */
class Pair implements JsonSerializable
{
    private $key;
    private $value;

    /**
     * Pair constructor.
     * @param $key
     * @param $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return "Pair[key={$this->key} value={$this->value}]";
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
            "key" => $this->key,
            "value" => $this->value
        ];
    }
}