<?php

namespace Lucinda\MVC\Response\Attachment;

use Lucinda\MVC\Response;

/**
 * Models a HTTP response attachment
 */
final class File implements Response
{
    private FileToUpload $fileToUpload;
    private string $filePath;
    private bool $cleanupAfter;

    /**
     * Sets up the class with file about to be outputed and whether or not to delete it afterwards
     * 
     * @param string $filePath
     * @param bool $cleanupAfter
     */
    public function __construct(string $filePath, bool $cleanupAfter)
    {
        $this->fileToUpload = new FileToUpload($filePath);
        $this->filePath = $filePath;
        $this->cleanupAfter = $cleanupAfter;
    }

    
    /**
     * Executes attachment logic
     *
     * @return void
     */
    public function run(): void
    {
        header('Content-Type: '.$this->fileToUpload->getMimeType());
        header('Content-Disposition: attachment; filename="' . $this->fileToUpload->getSimpleName() . '"');
        header('Content-Length: '.$this->fileToUpload->getSize());
        try {
            $result = readfile($this->filePath);
            if ($result === false) {
                throw new Exception("File could not be read: ".$this->filePath);
            }
        } finally {            
            if ($this->cleanupAfter) {
                $this->fileToUpload->cleanup();
            }
        }        
        exit;
    }
}