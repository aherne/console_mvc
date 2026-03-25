<?php

namespace Lucinda\MVC\Response\Attachment;

/**
 * Encapsulates logic of a file to upload (attach)
 */
final class FileToUpload
{
    private string $filePath;

    /**
     * @param string $filePath Absolute location of file on disk
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->verify();
    }

    /**
     * Verifies file for existance and readability
     * 
     * @throws Exception If validation fails
     */
    private function verify(): void
    {
        if (!file_exists($this->filePath) || !is_file($this->filePath) || !is_readable($this->filePath)) {
            throw new Exception("File doesn't exist or it's not readable: ".$this->filePath);
        }
    }

    /**
     * Gets file's mime type
     * 
     * @return string
     */
    public function getMimeType(): string
    {
        return mime_content_type($this->filePath) ?: 'application/octet-stream';
    }

    /**
     * Gets attachment's file name
     * 
     * @return string
     */
    public function getSimpleName(): string
    {
        return str_replace('"', '\"', basename($this->filePath));
    }

    /**
     * Gets attachment's file size
     * 
     * @throws Exception If file size couldn't be retrieved.
     */
    public function getSize(): int
    {
        $fileSize = filesize($this->filePath);
        if ($fileSize === false) {
            throw new Exception("File size could not be determined: ".$this->filePath);
        }
        return $fileSize;
    }
    
    /**
     * Deletes attachment file from disk (it was a temp file)
     */
    public function cleanup(): void
    {
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }
}