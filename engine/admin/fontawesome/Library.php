<?php

namespace engine\admin\fontawesome;

defined('ABSPATH') || exit;

class Library
{
    private mixed $icons;
    private array $library;

    function __construct()
    {
        //read json file contents
        $this->icons = file_get_contents(__DIR__.'/package.json');
        //decode read json file content into php associative array
        $this->icons = json_decode($this->icons);
        //generate library
        $this->generate();
    }

    /**
     * Auto generates fontawesome library from included json file into php array
     *
     * @return void
     */
    private function generate() : void
    {
        foreach($this->icons as $icon)
            $this->library[] = $icon->content;
    }

    /**
     * Returns the generated library as php array
     *
     * @return array
     */
    public function getLibrary() : array
    {
        return $this->library;
    }
}