<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.11.2018
 * Time: 16:57
 */

// 1. Make hreflangs field in Page.php temporarily PUBLIC

// 2. Add this to the SitemapSerializer.php before hreflangs building
//$hrefls = [new Pair("en", "http://en.com/lang/"),
//    new Pair("fr-du", "http://fr-du.com/lang/")];
//$p->hreflangs = array_merge($p->hreflangs, $hrefls);

//or  2. Add this to the Sitemap.php after original hreflangs constructing
//
//$hrefls = [new Hreflang("en", "http://en.com/lang/"),
//    new Hreflang("fr-du", "http://fr-du.com/lang/")];
//$hreflangs = array_merge($hreflangs, $hrefls);
//

// 3. Make hreflangs field in Page.php PRIVATE back