<?php

namespace engine\admin;

use engine\admin\fontawesome\Library;
use engine\enums\Constants;
use engine\storage\Storage;
use engine\utils\Request;

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

    public function getStateCities(): void
    {
        if (!Request::isAjax())
            wp_die();

        $data = Request::post()->getParams();
        $state = $data['state'];

        $cities = Storage::getJsonDataWhere(Constants::Storage.'/json/cities.json',$state);

        $html = '';
        ob_start();
        if ($cities)
        {
            ?>
            <option></option>
            <?php
            foreach ($cities as $cityCode => $cityName)
            {
                ?>
                <option value="<?php echo $cityCode ?>">
                    <?php echo $cityName ?>
                </option>
                <?php
            }

            $html = ob_get_clean();
        }


        wp_send_json([
            'success' => !empty($html),
            'data' => $html,
        ]);

        wp_die();
    }
}

new AjaxHandler();