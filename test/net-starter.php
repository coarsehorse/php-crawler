<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 02.11.2018
 * Time: 10:43
 */

require_once __DIR__ . "/../../crawler/WebCrawler.php";
require_once __DIR__ . "/../../serialization/SitemapSerializer.php";
require_once __DIR__ . "/../../serialization/SiteTreeSerializer.php";

// Break the limitations
ini_set('memory_limit', '16384M');
set_time_limit(PHP_INT_MAX);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check the form inputs
    $continue = true;

    if (empty($_POST['url'])) {
        echo "<p>URL is empty</p>";
        $continue = false;
    } else {
        $url_pattern = '/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9]\.[^\s]{2,})/';
        if (!preg_match($url_pattern, $_POST['url'])) {
            echo "<p>url is not valid</p>";
            $continue = false;
        }
    }

    if (empty($_POST['email'])) {
        echo "<p>e-mail is empty</p>";
        $continue = false;
    } else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
        echo "<p>e-mail is not valid</p>";
        $continue = false;
    }

    // Run the crawler
    if ($continue) {
//        file_put_contents("done.txt", "the start\n", FILE_APPEND);
//        for ($i = 0; $i < 5; $i++) {
//            file_put_contents("done.txt", "$i\n", FILE_APPEND);
//            sleep(1);
//        }
//        file_put_contents("done.txt", "the end\n", FILE_APPEND);
//        echo "done";
//        die();
        $url = trim($_POST['url']);
        $domain = explode('/', $url)[2];
        $email = trim($_POST['email']);

        $time_start = microtime(true);
        // Show html info page
        echo "<p>INFO BLOCK</p>";
        //
        $crawledLevels = WebCrawler::startCrawling($url);
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        // Construct the unique filename for results
        date_default_timezone_set('Europe/Kiev');
        $currDate = date("d-m-Y(l)-G-i");
        $res_file_prefix = __DIR__ . "/../../results/" . $domain;
        $res_file_sitemap = $res_file_prefix . "-" . $currDate . "-sitemap.xml";
        $res_file_tree = $res_file_prefix . "-" . $currDate . "-tree.html";

        // Serialize crawled data
        $sitemap = SitemapSerializer::serialize($crawledLevels);
        $converted = SiteTreeSerializer::convert($crawledLevels);
        $tree = SiteTreeSerializer::serialize($converted);

        file_put_contents($res_file_sitemap, $sitemap);
        file_put_contents($res_file_tree, $tree);

        // Send email to the user
        /*$to = $email;
        $subject = "crawling results for the $currDate, site: " . $domain;
        $message = "Hello, here is your results of crawling $url for the $currDate:\n"
            . "Sitemap: " . $res_file_sitemap . "\n"
            . "Tree: " . $res_file_tree . "\n\n"
            . "Good luck";
        $headers = array(
            'From' => 'webmaster@example.com',
            'Reply-To' => 'webmaster@example.com',
            'X-Mailer' => 'PHP/' . phpversion()
        );
        mail($to, $subject, $message, $headers);*/
    }
} else {
    echo "This script expects POST data from the form";
}