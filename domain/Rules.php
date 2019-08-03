<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.11.2018
 * Time: 11:54
 */

/**
 * Class Rules - Data structure that represents collection of rules.
 * One rule is a valid php regexp.
 * There are two arrays of rules: exceptions and approvals.
 * Once the Rules objects is initialized, you can check:
 *  - is any string falls under the one of the exceptions regexep with isInExceptions() method
 *  - is any string falls under the one of the approvals regexep with isInApprovals() method
 */
class Rules implements JsonSerializable
{
    /** @var string[] $exceptions */
    private $exceptions;

    /** @var string[] $approvals */
    private $approvals;

    /**
     * Checks whether the $str falls under the one of the $exceptions regexep.
     *
     * @param string $str The string to check.
     * @return bool - true if one of the $exceptions regexp returned true, false otherwise.
     */
    public function isInExceptions(string $str): bool {
        foreach ($this->exceptions as $exc)
            if (preg_match($exc, $str))
                return true;

        return false;
    }

    /**
     * Checks whether the $str falls under the one of the $approvals regexep.
     *
     * @param string $str The string to check.
     * @return bool - true if one of the $approvals regexp returned true, false otherwise.
     */
    public function isInApprovals(string $str): bool {
        foreach ($this->approvals as $apr)
            if (preg_match($apr, $str))
                return true;

        return false;
    }

    /**
     * Merges the $exceptions with the new.
     *
     * @param array $newExceptions The new exceptions.
     */
    public function appendExceptions(array $newExceptions): void {
        $this->exceptions = array_values(array_merge($this->exceptions, $newExceptions));
    }

    /**
     * Merges the $approvals with the new.
     *
     * @param array $newApprovals
     */
    public function appendApprovals(array $newApprovals): void {
        $this->approvals = array_values(array_merge($this->approvals, $newApprovals));
    }

    /**
     * Validates the specified $str against the rules.
     *
     * @param string $str The string to validate.
     * @return bool - true if the specified string matches any of the approval rules or not matches any exceptions rules.
     * false otherwise.
     */
    public function validate(string $str): bool {
        if ($this->isInApprovals($str))
            return true;
        elseif (!$this->isInExceptions($str))
            return true;
        else
            return false;
    }

        /**
     * Rules constructor.
     * @param string[] $exceptions
     * @param string[] $approvals
     */
    public function __construct(array $exceptions = [], array $approvals = [])
    {
        $this->exceptions = $exceptions;
        $this->approvals = $approvals;
    }

    /**
     * @return string[]
     */
    public function getExceptions(): array
    {
        return $this->exceptions;
    }

    /**
     * @return string[]
     */
    public function getApprovals(): array
    {
        return $this->approvals;
    }

    public function __toString(): string
    {
        $exc = join(", ", $this->exceptions);
        $apr = join(", ", $this->approvals);

        return "Rules[exceptions=[$exc] approvals=[$apr]]";
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
            "exceptions" => $this->exceptions,
            "approvals" => $this->approvals
        ];
    }
}