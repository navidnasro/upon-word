<?php

namespace engine\ajax;

class LiveSearch extends AjaxHandler
{
    protected string $name = 'live_search';

    public function handle(): void
    {
        var_dump('live searching...');

        wp_die();
    }
}