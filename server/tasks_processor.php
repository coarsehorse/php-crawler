<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.11.2018
 * Time: 11:40
 */

require_once __DIR__ . "/../domain/crawling_task/CrawlingTask.php";
require_once __DIR__ . "/../domain/crawling_result/CrawlingResult.php";
require_once __DIR__ . "/../dao/CrawlingTasksDAOFile.php";
require_once __DIR__ . "/../dao/CrawlingResultsDAOFile.php";
require_once __DIR__ . "/../logger/Logger.php";
require_once __DIR__ . "/../crawler/WebCrawler.php";
require_once __DIR__ . "/../serialization/SiteTreeSerializer.php";
require_once __DIR__ . "/../domain/Rules.php";

// Increase limits
ini_set('memory_limit', '16384M');
set_time_limit(PHP_INT_MAX);

$tasksDao = new CrawlingTasksDAOFile();
$resultsDao = new CrawlingResultsDAOFile();

while (true) {
    $tasks = $tasksDao->getTasks();
    foreach ($tasks as $task) {
        /** @var CrawlingTask $task */
        Logger::log("[task-processor] $task processing started");
        ob_start(); // suppress crawler output(echos)
        $time_start = microtime(true);
        $crawlingResult = WebCrawler::startCrawling($task->getUrl(), new Rules());
        $time_end = microtime(true);
        $crawling_time = $time_end - $time_start;
        ob_end_clean();
        Logger::log("[task-processor] $task processing finished");
        $crawledSiteTree = SiteTreeSerializer::convert($crawlingResult);
        $crawledSiteTreeView = SiteTreeSerializer::recoursiveViewBuilder($crawledSiteTree, "https://");
        $crawlingResult = new CrawlingResult($task->getId(), $crawledSiteTreeView, $crawling_time);

        if ($resultsDao->addCrawlingResult($crawlingResult))
            Logger::log("[task-processor] $crawlingResult has been successfully added into db");
        else
            Logger::log("[task-processor] $crawlingResult has not been added into db,"
                . " result with the same id is already exists");

        if ($tasksDao->removeTask($task))
            Logger::log("[task-processor] $task successfully removed from the queue");
        else
            Logger::log("[task-processor] $task error removing task from the queue, it's absent");
    }
    sleep(5); // check delay
}