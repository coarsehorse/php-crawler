<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06.11.2018
 * Time: 11:00
 */

interface CrawlingResultsDAO
{
    public function getCrawlingResults(): array;
    public function addCrawlingResult(CrawlingResult $newResult): bool;
}