<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.10.2018
 * Time: 18:14
 */

require_once __DIR__ . "/Level.php";
require_once __DIR__ . "/../Page.php";

/**
 * Class LevelsWrapper - wrapper for the Level objects array which provides useful methods.
 */
class LevelsWrapper implements JsonSerializable
{
    /** @var Level[] Level objects array */
    private $levels;

    /**
     * LevelsWrapper constructor.
     */
    public function __construct()
    {
        $this->levels = [];
    }


    /**
     * Adds new crawled level.
     * If some page is exists in another level,
     * it will be omitted on the newly added level and
     * the link counter of that page in another level will be incremented.
     *
     * @param Page[] $pages The array of newly crawled pages.
     */
    public function addNewLevel(array $pages): void
    {
        $newPages = [];

        // Update counter of existing links
        foreach ($pages as $p) {
            if ($this->isPageExists($p))
                $this->incrementPageCounter($p);
            else
                $newPages[] = $p;
        }

        // Create the new level
        if (count($newPages) != 0)
            $this->levels[] = new Level(count($this->levels), $newPages);
    }

    /**
     * Removes specified page with its counter if it exists in one of the levels.
     *
     * @param string $page The link to remove.
     */
    public function removePage(Page $page): void
    {
        foreach ($this->levels as $level)
            if ($level->isPageExists($page)) {
                $level->removePage($page);
                break;
            }
    }

    /**
     * Check whether given page exists in one of the existing levels.
     *
     * @param Page $page The page to check.
     * @return bool true if exists, false otherwise.
     */
    private function isPageExists(Page $page): bool
    {
        foreach ($this->levels as $level)
            if ($level->isPageExists($page))
                return true;

        return false;
    }

    /**
     * Increments counter of the given page
     * if it exists in the one of existing levels.
     *
     * @param Page $page The page to process.
     */
    public function incrementPageCounter(Page $page)
    {
        foreach ($this->levels as $level)
            if ($level->isPageExists($page)) {
                $level->incrementPageCounter($page);
                break;
            }
    }

    /**
     * @return Level[] The array of crawled levels.
     */
    public function getLevels(): array
    {
        return $this->levels;
    }

    public function __toString(): string
    {
        $lvls = [];
        foreach ($this->levels as $lvl)
            $lvls[] = $lvl->__toString();
        $lvls = "[" . join(", ", $lvls) . "]";

        return "LevelsWrapper[levels=$lvls]";
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
            "levels" => $this->levels
        ];
    }
}