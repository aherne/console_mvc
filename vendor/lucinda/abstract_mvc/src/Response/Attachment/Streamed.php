<?php

namespace Lucinda\MVC\Response\Attachment;

use Lucinda\MVC\Response;

/**
 * Models a streamed HTTP response attachment
 */
final class Streamed implements Response
{
    private FileToUpload $fileToUpload;
    private string $filePath;
    private bool $cleanupAfter;
    private int $chunkSize;

    /**
     * Sets up the class with file about to be outputted and whether or not to delete it afterwards
     *
     * @param string $filePath
     * @param bool $cleanupAfter
     * @param int $chunkSize
     */
    public function __construct(string $filePath, bool $cleanupAfter, int $chunkSize = 8192)
    {
        if ($chunkSize < 1) {
            throw new Exception("Chunk size must be greater than zero");
        }

        $this->fileToUpload = new FileToUpload($filePath);
        $this->filePath = $filePath;
        $this->cleanupAfter = $cleanupAfter;
        $this->chunkSize = $chunkSize;
    }

    /**
     * Executes streamed attachment logic
     *
     * @return void
     */
    public function run(): void
    {
        header('Content-Type: '.$this->fileToUpload->getMimeType());
        header('Content-Disposition: attachment; filename="' . $this->fileToUpload->getSimpleName() . '"');
        header('Accept-Ranges: none');

        $handle = fopen($this->filePath, 'rb');
        if ($handle === false) {
            throw new Exception("File could not be opened: ".$this->filePath);
        }

        try {
            while (!feof($handle)) {
                $buffer = fread($handle, $this->chunkSize);
                if ($buffer === false) {
                    throw new Exception("File could not be streamed: ".$this->filePath);
                }

                echo $buffer;
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
}