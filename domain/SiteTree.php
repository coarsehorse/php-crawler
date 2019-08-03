<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 028 28.10.18
 * Time: 11:42 AM
 */

/**
 * Class SiteTree - Data structure that represents site links tree.
 * For example http://domain/ level object:
 * One level contains its name and url ("domain" and http://domain/),
 * level links like: [http://domain/page1, http://domain/page2],
 * and siteTrees array of nested SiteTree objects for links like:
 * http://domain/v1/qwe1 (v1 level) and http://domain/v2/qwe2 (v2 level).
 * So, SiteTree object contains SiteTree objects array which represents links hierarchy.
 */
class SiteTree implements JsonSerializable
{
    /**
     * @var string $levelName
     */
    private $levelName;

    /**
     * @var Page $levelPage
     */
    private $levelPage;

    /**
     * @var Page[] $levelPages
     */
    private $levelPages;

    /**
     * @var SiteTree[] $anotherTrees
     */
    private $anotherTrees;

    public function __construct()
    {
        $this->levelName = "";
        $this->levelPage = null;
        $this->levelPages = [];
        $this->anotherTrees = [];
    }

    /**
     * Updates the existing sublevel by name or adds the new one.
     *
     * @param SiteTree $sublevel The updated or new sublevel.
     */
    public function updateSublevel(SiteTree $sublevel): void
    {
        $notExists = true;
        array_walk($this->anotherTrees, function (SiteTree &$v) use ($sublevel, &$notExists) {
            if ($v->levelName === $sublevel->levelName) {
                $v = $sublevel;
                $notExists = false;
                return;
            }
        });
        if ($notExists)
            $this->anotherTrees[] = $sublevel;
    }

    /**
     * Gives the sublevel if it exists or null;
     *
     * @param string $sublevelName The sublevel name.
     * @return SiteTree|null - the sublevel or null
     */
    public function getSublevel(string $sublevelName): ?SiteTree
    {
        foreach ($this->anotherTrees as $anotherTree)
            if ($anotherTree->levelName === $sublevelName)
                return $anotherTree;
        return null;
    }

    /**
     * @return string
     */
    public function getLevelName(): string
    {
        return $this->levelName;
    }

    /**
     * @param string $levelName
     */
    public function setLevelName(string $levelName): void
    {
        $this->levelName = $levelName;
    }

    /**
     * @return Page
     */
    public function getLevelPage(): ?Page
    {
        return $this->levelPage;
    }

    /**
     * @param Page $levelPage
     */
    public function setLevelPage(Page $levelPage): void
    {
        $this->levelPage = $levelPage;
    }

    /**
     * @return Page[]
     */
    public function getLevelPages(): array
    {
        return $this->levelPages;
    }

    /**
     * @param Page[] $levelPages
     */
    public function setLevelPages(array $levelPages): void
    {
        $this->levelPages = $levelPages;
    }

    /**
     * @return SiteTree[]
     */
    public function getAnotherTrees(): array
    {
        return $this->anotherTrees;
    }

    /**
     * @param SiteTree[] $anotherTrees
     */
    public function setAnotherTrees(array $anotherTrees): void
    {
        $this->anotherTrees = $anotherTrees;
    }

    public function __toString(): string
    {
        $pages = [];
        foreach ($this->levelPages as $p)
            $pages[] = $p->__toString();
        $pages = "[" . join(", ", $pages) . "]";

        $trees = [];
        foreach ($this->anotherTrees as $t)
            $trees[] = $t->__toString();
        $trees = "[" . join(", ", $trees) . "]";

        return "SiteTree[levelName={$this->levelName} levelPage={$this->levelPage} LevelPages=$pages anotherTrees=$trees]";
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
            "levelName" => $this->levelName,
            "levelPage" => $this->levelPage,
            "LevelPages" => $this->levelPages,
            "anotherTrees" => $this->anotherTrees
        ];
    }
}