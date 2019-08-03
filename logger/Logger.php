<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 004 04.11.18
 * Time: 7:54 PM
 */

class Logger
{
    private static $logFileName =  "log.txt";

    public static function log(string $message, $addNewLine = true): void {
        date_default_timezone_set('Europe/Kiev');
        $date = date("d-m-Y(l)-G:i:s");
        file_put_contents(self::$logFileName, "[$date]\t\t$message" . ($addNewLine ? "\n" : ""), FILE_APPEND);
    }
}