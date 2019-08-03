<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.11.2018
 * Time: 10:33
 */

require_once __DIR__ . "/../dao/CrawlingTasksDAOFile.php";
require_once __DIR__ . "/../domain/CrawlingTaskRequest.phpest.php";

$dao = new CrawlingTasksDAOFile();

$t1 = new CrawlingTask("http://asf.com/", "example@me.com");
$t2 = new CrawlingTask("http://qwerty.com.ua/", "noname@mail.com");

$dao->setTask($t1);
$dao->setTask($t2);

$res = $dao->getTasks();

$dao->removeTask($t1);
$res = $dao->getTasks();