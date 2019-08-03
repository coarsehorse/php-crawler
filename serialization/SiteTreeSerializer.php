<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.10.2018
 * Time: 17:17
 */

require_once __DIR__ . "/../domain/SiteTree.php";

/**
 * Class TreeSerializer - utility that serializes LevelsWrapper object to
 * the html tree view.
 */
class SiteTreeSerializer
{

    /**
     * Adds the specified link into existing SiteTree hierarchy.
     * Automatically builds missed levels.
     *
     * @param SiteTree $siteTree The SiteTree node.
     * @param array $linkLevelNames The link-based level names.
     * @param Page $page The page to add.
     * @return SiteTree - updated SiteTree.
     */
    private static function siteTreeBuilder(SiteTree $siteTree,
                                           array $linkLevelNames, Page $page): SiteTree
    {
        $lvlName = $linkLevelNames[0];
        $lvlsNum = count($linkLevelNames);

        if ($lvlsNum === 1) { // 1 - like http://domain.com/(domain)
            $siteTree->setLevelName($lvlName);
            $siteTree->setLevelPage($page);
            return $siteTree;
        } else if ($lvlsNum === 2) { // 2 - like http://domain.com/about-us/(domain, about-us)
            $siteTree->setLevelName($lvlName);
            $sublevels = $siteTree->getAnotherTrees();

            $sublvlsPages = [];
            foreach ($sublevels as $sublvl) {
                $p = $sublvl->getLevelPage();
                if(is_null($p))
                    continue;
                $sublvlsPages[] = $p;
            }

            if (!in_array($page, $sublvlsPages)) { // add page to the current level pages
                $oldLevelPages = $siteTree->getLevelPages();
                $oldLevelPages[] = $page;
                $siteTree->setLevelPages($oldLevelPages);
            }
            return $siteTree;
        } else {
            // Trying to get the "next"(index=1) sublevel
            $sublevel = $siteTree->getSublevel("$linkLevelNames[1]");
            if (is_null($sublevel)) {
                // Create the new one if "next" sublevel does not exist
                $sublevel = new SiteTree();
                $sublevel->setLevelName($linkLevelNames[1]);
            }
            $siteTree->updateSublevel(self::siteTreeBuilder($sublevel, array_slice($linkLevelNames, 1), $page));

            return $siteTree;
        }
    }

    /**
     * Converts LewelsWrapper object to the SiteTree tree.
     * Automatically creates missed levels/sublevels.
     * Every single link will have complete levels path like:
     * domain / lvl1 / lvl2 /
     * for link https://domain/lvl1/lvl2/article-about-smth
     * even if no lvl1 or lvl2 was previously created.
     *
     * @param LevelsWrapper $levelsWrapper The LewelsWrapper object to convert.
     * @return SiteTree - converted SiteTree.
     */
    public static function convert(LevelsWrapper $levelsWrapper): SiteTree
    {
        $allPages = [];

        // Get only links
        foreach ($levelsWrapper->getLevels() as $lvl) {
            // Get level pages
            $pages = array_map(function (Pair $pageCounter) {
                return $pageCounter->getKey();
            }, $lvl->getLevelPages());
            $allPages = array_merge($allPages, $pages);
        }
        $allPages = array_values(array_unique($allPages)); // comparison by Page->__toString()

        $siteTree = new SiteTree();

        foreach ($allPages as $page) {
            $linkLevelNames = array_slice(explode('/', $page->getUrl()), 2, -1);
            $siteTree = self::siteTreeBuilder($siteTree, $linkLevelNames, $page);
        }

        return $siteTree;
    }

    /**
     * Builds html view for the specified lvl.
     * Calls itself recursively for all the nested levels.
     *
     * @param SiteTree $level The specified level to build.
     * @param string $prevPath The previous level path. Using for dynamic level path building.
     * @return string - the html of specified level with all its sublevels.
     */
    public static function recoursiveViewBuilder(SiteTree $level, string $prevPath): string
    {
        // Construct level path
        $currPath = $prevPath . $level->getLevelName() . "/";

        // This level header
        $inner_html = is_null($level->getLevelPage()) ?
            "<li><a href=\"$currPath\">{$level->getLevelName()}</a></li>\n" :
            "<li><a href=\"{$level->getLevelPage()->getUrl()}\">{$level->getLevelPage()->getH1()}</a></li>\n";

        // If nested levels exist - build their first
        if (count($level->getAnotherTrees()) !== 0) {
            foreach ($level->getAnotherTrees() as $anotherTree) {
                $inner_html .= "<ul>\n";
                $inner_html .= self::recoursiveViewBuilder($anotherTree, $currPath);
                $inner_html .= "</ul>\n";
            }
        }

        $inner_html .= "<ul>\n";
        // Add this level links
        foreach ($level->getLevelPages() as $levelPage) {
            $inner_html .= "<li><a href=\"{$levelPage->getUrl()}\">{$levelPage->getH1()}</a></li>\n";
        }
        $inner_html .= "</ul>\n";

        return $inner_html;
    }

    /**
     * Serializes SiteTree object to its html representation.
     *
     * @param SiteTree $siteTree The SiteTree object to serialize.
     * @param string $protocol The protocol(initial path prefix).
     * @return string
     */
    public static function serialize(SiteTree $siteTree, string $protocol = "http://"): string
    {
        $html = "<!DOCTYPE html>\n";
        $html .= "<html lang=\"en\">\n";
        $html .= "<head>\n";
        $html .= "\t<meta charset=\"UTF-8\">\n";
        $html .= is_null($siteTree->getLevelPage()) ?
            "\t<title>{$siteTree->getLevelName()}</title>\n" :
            "\t<title>{$siteTree->getLevelPage()->getUrl()}</title>\n";
        $html .= "</head>\n";
        $html .= "<body>\n";
        $html .= is_null($siteTree->getLevelPage()) ?
            "<h1>Site tree of {$siteTree->getLevelName()}</h1></br>\n" :
            "<h1>Site tree of <a href=\"{$siteTree->getLevelPage()->getUrl()}\">{$siteTree->getLevelPage()->getUrl()}</a></h1></br>\n";
        $html .= "<ul>\n";
        $html .= self::recoursiveViewBuilder($siteTree, $protocol);
        $html .= "</ul>\n";
        $html .= "</body>\n";
        $html .= "</html>\n";

        return $html;
    }
}
