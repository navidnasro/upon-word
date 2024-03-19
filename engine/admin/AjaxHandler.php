<?php

namespace engine\admin;

use engine\admin\fontawesome\Library;

defined('ABSPATH') || exit;

class AjaxHandler
{
    public function __construct()
    {
        add_action('wp_ajax_iconPagination',[$this,'iconPagination']);
        add_action('wp_ajax_nopriv_iconPagination',[$this,'iconPagination']);
    }

    public function iconPagination(): void
    {
        $page = $_GET['page'];

        require_once ENGINE.'/admin/fontawesome/Library.php';

        $icons = new Library();
        $icons = $icons->getLibrary();

        if (count($icons)/50 < $page || $page < 1)
            wp_die();

        for($i = 50*($page-1);$i < 50*$page;$i++)
        {
            ?>
            <div
                class="icon"
                style="display: flex;border: solid 0px #00c0ff;cursor: pointer;padding: 10px 0px;align-items: center;justify-content: center;width: 15%;margin: 6px"
                data-icon="<?php echo $icons[$i] ?>">
                <i class="<?php echo $icons[$i] ?>"></i>
            </div>
            <?php
        }

        wp_die();
    }
}

new AjaxHandler();