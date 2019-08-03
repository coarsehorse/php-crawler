<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 15.11.2018
 * Time: 12:35
 */

/**
 * Class Utils - class that provides convenient methods for for the general purposes.
 */
class Utils
{
    /**
     * Compresses $data text to specified file $gzFileToWrite.
     *
     * @param string $gzFileToWrite The file(.gz) path to write compressed data.
     * @param string $data The text to compress.
     * @param int $level The compression level. Default is 9(highest).
     * @return bool - true if text successfully compressed and wrote, false otherwise.
     */
    public static function compressToGz(string $gzFileToWrite, string $data, int $level = 9): bool
    {
        // Open the gz file
        $fp = gzopen($gzFileToWrite, 'w' . $level);
        $bytes = gzwrite($fp, $data);

        if (gzclose($fp) and $bytes > 0)
            return true;
        return false;
    }

    /**
     * Measures how many disk space the
     * specified text will occupy in compressed state.
     *
     * @param string $data The text to compress.
     * @return int - compressed data size in bytes.
     */
    public static function measureGzSize($data): int
    {
        usleep(100); // for unique filename
        $tempFile = "temp_" . microtime(true) . ".gz";

        Utils::compressToGz($tempFile, $data);
        $size = filesize($tempFile);
        unlink($tempFile);

        return $size;
    }


    public static function escapeXmlAmpersants(string $str): string
    {
        return str_replace("&", "&amp;", $str);
    }
}