<?php

namespace engine\ajax;

defined('ABSPATH') || exit;

abstract class AjaxHandler
{
    protected string $name;

    public function init(): void
    {
        add_action('wp_ajax_'.$this->name,[$this,'handle']);
        add_action('wp_ajax_nopriv_'.$this->name,[$this,'handle']);
    }

    abstract public function handle(): void;
}