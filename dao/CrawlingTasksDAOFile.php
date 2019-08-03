<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.11.2018
 * Time: 10:27
 */

require_once __DIR__ . "/../domain/crawling_task/CrawlingTask.php";
require_once __DIR__ . "/CrawlingTasksDAO.php";

class CrawlingTasksDAOFile implements CrawlingTasksDAO
{
    private $fileName = "crawlingTasks.json";

    public function getTasks(): array
    {
        if (!file_exists($this->fileName))
            return [];

        $oldContent = file_get_contents($this->fileName);
        $tasksArray = json_decode($oldContent);
        $crawlingTasks = array_map(function($task) {
            return new CrawlingTask($task->{'id'}, $task->{'url'}, $task->{'email'});
        }, $tasksArray);

        return $crawlingTasks;
    }

    public function setTask(CrawlingTask $task): bool
    {
        if (!file_exists($this->fileName)) {
            file_put_contents($this->fileName, json_encode(array(), JSON_PRETTY_PRINT));
        }
        $oldTasks = self::getTasks();

        $found = array_filter($oldTasks, function(CrawlingTask $oldTask) use ($task) {
            return $oldTask->getUrl() === $task->getUrl() and $oldTask->getEmail() === $task->getEmail();
        });
        if (count($found) !== 0) // if task is already exists in the queue
            return false;

        $oldTasks[] = $task;
        file_put_contents($this->fileName, json_encode($oldTasks, JSON_PRETTY_PRINT));

        return true;
    }

    public function removeTask(CrawlingTask $task): bool
    {
        if (!file_exists($this->fileName)) {
            return true;
        }
        $oldTasks = self::getTasks();

        if (in_array($task, $oldTasks)) {
            $newTasks = array_values(array_filter($oldTasks, function(CrawlingTask $oldTask) use ($task) {
                return $oldTask != $task; // != because we need string comparison
            }));
            file_put_contents($this->fileName, json_encode($newTasks, JSON_PRETTY_PRINT));

            return true;
        }
        else
            return false;
    }
}