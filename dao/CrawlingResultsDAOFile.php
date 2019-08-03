<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06.11.2018
 * Time: 11:03
 */

require_once __DIR__ . "/../domain/crawling_result/CrawlingResult.php";
require_once __DIR__ . "/../dao/CrawlingResultsDAO.php";

class CrawlingResultsDAOFile implements CrawlingResultsDAO
{
    private $fileName = "crawlingResults.json";

    public function getCrawlingResults(): array
    {
        if (!file_exists($this->fileName))
            return [];

        $oldContent = file_get_contents($this->fileName);
        $resultsArray = json_decode($oldContent);
        $crawlingResults = array_map(function($result) {
            return new CrawlingResult($result->{'id'}, $result->{'resultBody'}, $result->{'crawlingTime'});
        }, $resultsArray);

        return $crawlingResults;
    }

    public function addCrawlingResult(CrawlingResult $newResult): bool
    {
        if (!file_exists($this->fileName)) {
            file_put_contents($this->fileName, json_encode(array(), JSON_PRETTY_PRINT));
        }

        $oldResults = self::getCrawlingResults();

        if (in_array($newResult, $oldResults))
            return false;

        $oldResults[] = $newResult;
        file_put_contents($this->fileName, json_encode($oldResults, JSON_PRETTY_PRINT));

        return true;
    }
}