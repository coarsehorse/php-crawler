<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.11.2018
 * Time: 10:23
 */

require_once __DIR__ . "/../domain/crawling_task/CrawlingTask.php";

interface CrawlingTasksDAO
{
    public function getTasks(): array;
    public function setTask(CrawlingTask $task): bool;
    public function removeTask(CrawlingTask $task): bool;
}