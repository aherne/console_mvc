<?php

namespace Lucinda\MVC\Response\Attachment;

use Lucinda\MVC\Response;

/**
 * Models a partial HTTP response attachment (single byte range)
 */
final class Partial implements Response
{
    const SUPPORTED_STATUSES = [206, 416];
    private FileToUpload $fileToUpload;
    private string $filePath;
    private bool $cleanupAfter;
    private string $range;

    /**
     * Sets up the class with file about to be outputted, requested byte range and whether or not to delete it afterwards
     *
     * @param string $filePath
     * @param string $range
     * @param bool $cleanupAfter
     */
    public function __construct(string $filePath, string $range, bool $cleanupAfter)
    {
        if (!preg_match('/^bytes=\d*-\d*$/', trim($range))) {
            throw new Exception("Invalid range header: ".$range);
        }

        $this->fileToUpload = new FileToUpload($filePath);
        $this->filePath = $filePath;
        $this->range = trim($range);
        $this->cleanupAfter = $cleanupAfter;
    }

    /**
     * Executes partial attachment logic
     *
     * @return void
     */
    public function run(): void
    {
        $fileSize = $this->fileToUpload->getSize();
        if ($fileSize === 0) {
            $this->sendUnsatisfiableRange(0);
        }

        [$start, $end] = $this->getRangeCoordinates($fileSize);
        $length = $end - $start + 1;

        $this->sendPartialContent($start, $end, $length, $fileSize);

        $handle = fopen($this->filePath, 'rb');
        if ($handle === false) {
            throw new Exception("File could not be opened: ".$this->filePath);
        }

        try {
            if (fseek($handle, $start) !== 0) {
                throw new Exception("File could not be seeked: ".$this->filePath);
            }

            $remaining = $length;
            $chunkSize = 8192;

            while ($remaining > 0 && !feof($handle)) {
                $buffer = fread($handle, min($chunkSize, $remaining));
                if ($buffer === false) {
                    throw new Exception("File could not be partially read: ".$this->filePath);
                }

                $written = strlen($buffer);
                if ($written === 0) {
                    break;
                }

                echo $buffer;
                $remaining -= $written;
                flush();
            }
        } finally {
            fclose($handle);

            if ($this->cleanupAfter) {
                $this->fileToUpload->cleanup();
            }
        }

        exit;
    }

    /**
     * Computes start/end byte coordinates from Range header
     *
     * @param int $fileSize
     * @return array<int,int>
     */
    private function getRangeCoordinates(int $fileSize): array
    {
        preg_match('/^bytes=(\d*)-(\d*)$/', $this->range, $matches);

        $startRaw = $matches[1];
        $endRaw = $matches[2];

        if ($startRaw === '' && $endRaw === '') {
            $this->sendUnsatisfiableRange($fileSize);
        }

        // suffix range: bytes=-500
        if ($startRaw === '') {
            $suffixLength = (int) $endRaw;
            if ($suffixLength <= 0) {
                $this->sendUnsatisfiableRange($fileSize);
            }

            $start = max(0, $fileSize - $suffixLength);
            $end = $fileSize - 1;

            return [$start, $end];
        }

        $start = (int) $startRaw;

        // open-ended range: bytes=500-
        if ($endRaw === '') {
            if ($start >= $fileSize) {
                $this->sendUnsatisfiableRange($fileSize);
            }

            return [$start, $fileSize - 1];
        }

        $end = (int) $endRaw;

        if ($start > $end || $start >= $fileSize) {
            $this->sendUnsatisfiableRange($fileSize);
        }

        $end = min($end, $fileSize - 1);

        return [$start, $end];
    }

    /**
     * Sends 416 response and terminates
     *
     * @param int $fileSize
     * @return void
     */
    private function sendUnsatisfiableRange(int $fileSize): void
    {
        http_response_code(416);
        header('Content-Range: bytes */'.$fileSize);
        exit;
    }

    /**
     * Sends 206 response without terminating
     * 
     * @param int $start
     * @param int $end
     * @param int $length
     * @param int $fileSize
     * @return void
     */
    private function sendPartialContent(int $start, int $end, int $length, int $fileSize): void
    {
        http_response_code(206);
        header('Content-Type: '.$this->fileToUpload->getMimeType());
        header('Content-Disposition: attachment; filename="' . $this->fileToUpload->getSimpleName() . '"');
        header('Accept-Ranges: bytes');
        header('Content-Range: bytes '.$start.'-'.$end.'/'.$fileSize);
        header('Content-Length: '.$length);
    }
}