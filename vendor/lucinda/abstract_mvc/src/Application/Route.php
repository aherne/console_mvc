<?php
namespace Lucinda\MVC\Application;

/**
 * Encapsulates an abstract route
 */
class Route
{
    private $id;
    private $controller;
    private $view;
    private $format;
    
    /**
     * Detects route info from <exception> tag
     *
     * @param \SimpleXMLElement $info
     */
    public function __construct(\SimpleXMLElement $info)
    {
        $this->id = (string) $info["id"];
        $this->controller = (string) $info["controller"];
        $this->view = (string) $info["view"];
        $this->format = (string) $info["format"];
    }
    
    /**
     * Gets route unique identifier (eg: url)
     *
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }

    /**
     * Gets controller class name that handles exception handled.
     *
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Gets file that holds what is displayed when error response is rendered.
     *
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }
    
    /**
     * Gets response format.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}
