<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 004 04.11.18
 * Time: 6:49 PM
 */

require_once __DIR__ . "/../logger/Logger.php";
require_once __DIR__ . "/../domain/crawling_task/CrawlingTask.php";
require_once __DIR__ . "/../domain/crawling_task/CrawlingTaskResponse.php";
require_once __DIR__ . "/../dao/CrawlingTasksDAOFile.php";
require_once __DIR__ . "/../domain/crawling_result/CrawlingResult.php";
require_once __DIR__ . "/../domain/crawling_status/CrawlingStatusResponse.php";
require_once __DIR__ . "/../dao/CrawlingResultsDAOFile.php";

// Increase limits
ini_set('memory_limit', '16384M');
set_time_limit(PHP_INT_MAX);

/* Routes */
// php -S localhost:8910 server.php | php -c "C:\php-no-pthreads.ini" -S localhost:8910 server.php

Logger::log("[server] Accepted {$_SERVER['REQUEST_METHOD']} request \"{$_SERVER['REQUEST_URI']}\""
    . " from {$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']}");

$resultsDao = new CrawlingResultsDAOFile();

// Handle GET requests
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Handle static content
    if (preg_match('/\.css|\.js|\.jpg|\.png|\.ico$/', $_SERVER['REQUEST_URI'], $match)) {
        $mimeTypes = [
            '.css' => 'text/css',
            '.js'  => 'application/javascript',
            '.jpg' => 'image/jpg',
            '.png' => 'image/png',
            '.ico' => 'image/x-icon'
        ];
        $path = __DIR__ . '/net-starter' . $_SERVER['REQUEST_URI'];
        if (is_file($path)) {
            header("Content-Type: {$mimeTypes[$match[0]]}");
            require $path;
        }
    }
    // Handle main page GET request
    elseif (preg_match('/^\/$/', $_SERVER["REQUEST_URI"])) {
        http_response_code(200);
        require './net-starter/net-starter.html';
    }
    // Handle task status GET request
    elseif (preg_match('/^\/status\?.*$/', $_SERVER["REQUEST_URI"])) {
        if (empty($_GET['id'])) {
            http_response_code(200);
            echo json_encode(new CrawlingStatusResponse("bad_id",
                new CrawlingResult(0, "", 0)));
        } else {
            $id = intval($_GET['id']);
            $results = $resultsDao->getCrawlingResults();
            if (count($results) === 0) {
                http_response_code(200);
                echo json_encode(new CrawlingStatusResponse("not_ready",
                    new CrawlingResult(0, "", 0)));
            } else {
                $foundResult = array_values(array_filter($results, function (CrawlingResult $result) use ($id) {
                    return $result->getId() === $id;
                }));
                if (count($foundResult) === 0) {
                    http_response_code(200);
                    echo json_encode(new CrawlingStatusResponse("not_ready",
                        new CrawlingResult(0, "", 0)));
                } else {
                    /** @var CrawlingResult $foundResult */
                    $foundResult = $foundResult[0];
                    http_response_code(200);
                    echo json_encode(new CrawlingStatusResponse("done", $foundResult));
                }
            }
        }
    }
    // Reject all other GET requests
    else {
        http_response_code(404);
        echo "What are you looking for? Main page: <a href='/'>link</a>";
    }
}
// Handle POST requests
elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle crawl POST request
    if (preg_match('/^\/crawl$/', $_SERVER["REQUEST_URI"])) {

        // Check the input data

        if (empty($_POST['url'])) {
            http_response_code(200);
            echo json_encode(new CrawlingTaskResponse(0, "Posted URL is empty"));
        } else {
            $url_pattern = '/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9]\.[^\s]{2,})/';
            if (!preg_match($url_pattern, $_POST['url'])) {
                http_response_code(200);
                echo json_encode(new CrawlingTaskResponse(0, "Posted URL is not valid"));
            }
        }

        if (empty($_POST['email'])) {
            http_response_code(200);
            echo json_encode(new CrawlingTaskResponse(0, "Posted e-mail is empty"));
        } elseif (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
            http_response_code(200);
            echo json_encode(new CrawlingTaskResponse(0, "Posted e-mail is not valid"));
        } else {
            usleep(100000); // 0.1 sec, prevents equal "id"(timestamp) of different tasks
            $date = new DateTime();
            $task = new CrawlingTask($date->getTimestamp(), $_POST['url'], $_POST['email']);
            $dao = new CrawlingTasksDAOFile();
            if ($dao->setTask($task)) {
                Logger::log("[server] Accepted crawl task from {$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} {$task}");
                $message = "Your crawling task is accepted. Site to crawl: "
                    . "{$task->getUrl()} Receiver email: {$task->getEmail()}";
                http_response_code(200);
                echo json_encode(new CrawlingTaskResponse($task->getId(), $message));
            }
            else {
                Logger::log("[server] Rejected crawl task from {$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} {$task}");
                $message = "Your crawling task is already in the queue. Please wait.";
                http_response_code(200);
                echo json_encode(new CrawlingTaskResponse(0, $message));
            }
        }
    }
    // Reject all other POST requests
    else {
        http_response_code(405);
        echo "What are you looking for?";
    }
// Reject all other requests
}
// Handle other requests
else {
    http_response_code(404);
    echo "What are you looking for?";
}
