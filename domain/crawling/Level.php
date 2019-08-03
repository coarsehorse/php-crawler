<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 22.10.2018
 * Time: 17:35
 */

require_once __DIR__ . "/../Pair.php";

/**
 * Class Level - wrapper for the one crawled level.
 * Contains level number and array of Pair(page, counter).
 * Where page = Page object, counter = page frequency counter.
 * If the same page crawled on the different levels page counter will be incremented.
 */
class Level implements JsonSerializable
{
    /** @var int Level number */
    private $levelNum;

    /** @var Pair[] Array of Pair objects.
     * Pair object contains page and frequency counter.
     */
    private $levelPages;

    /**
     * Level constructor.
     *
     * @param $levelNum int The level number
     * @param $levePages Page[] The array of Pair(Page => counter).
     */
    public function __construct($levelNum, $levePages)
    {
        $this->levelNum = $levelNum;
        $this->levelPages = [];
        // Convert each level page to the Pair[page => counter]
        foreach ($levePages as $lvlPage) {
            @$this->levelPages[] = new Pair($lvlPage, 1);
        }
    }

    /**
     * Check whether given page exists in that level pages.
     *
     * @param Page $page The searched page.
     * @return bool true if page is found, false otherwise.
     */
    public function isPageExists(Page $page): bool
    {
        return in_array($page->getUrl(), array_map(function (Pair $p) {
            return $p->getKey()->getUrl();
        }, $this->levelPages));
    }

    /**
     * Increments the counter of the given page if it exists at this level.
     *
     * @param Page $page The searched page.
     */
    public function incrementPageCounter(Page $page): void
    {
        $this->levelPages = array_map(function (Pair $pagePair) use ($page) {
            if ($pagePair->getKey()->getUrl() === $page->getUrl())
                return new Pair($pagePair->getKey(), $pagePair->getValue() + 1);
            else
                return $pagePair;
        }, $this->levelPages);
    }

    /**
     * Removes the Pair(page, counter) if it exists
     * in this level pages array.
     *
     * @param Page $page The page to remove.
     */
    public function removePage(Page $page): void {
        $temp = [];

        for ($i = 0; $i < count($this->levelPages); $i++) {
            if ($this->levelPages[$i]->getKey()->getUrl() !== $page->getUrl())
                $temp[] = $this->levelPages[$i];
        }
        $this->levelPages = $temp;
    }

    /**
     * @return int - the level number.
     */
    public function getLevelNum()
    {
        return $this->levelNum;
    }

    /**
     * @return Pair[] - the array of pages.
     */
    public function getLevelPages(): array
    {
        return $this->levelPages;
    }

    public function __toString(): string
    {
        $pages = [];
        foreach ($this->levelPages as $p)
            $pages[] = $p->__toString();
        $pages = "[" . join(", ", $pages) . "]";

        return "Level[levelNum={$this->levelNum} levelPages=$pages]";
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
            "levelNum" => $this->levelNum,
            "levelPages" => $this->levelPages
        ];
    }
}