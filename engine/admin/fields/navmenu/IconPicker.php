<?php

namespace engine\admin\fields\navmenu;

use engine\admin\fontawesome\Library;
use engine\enums\Constants;
use engine\enums\Defaults;
use WP_Post;

defined('ABSPATH') || exit;

class IconPicker
{
    private mixed $icons;
    private string $elements;

    function __construct()
    {
        require_once ENGINE.'/admin/fontawesome/Library.php';

        $this->icons = new Library();
        $this->icons = $this->icons->getLibrary();

        $this->generateElements();

        add_action('wp_nav_menu_item_custom_fields',[$this,'addField'],10,2);
        add_action('wp_update_nav_menu_item',[$this,'saveField'],10,2);
        add_action('admin_enqueue_scripts',[$this,'enqueue']);
    }

    /**
     * Generates icons html elements
     *
     * @return void
     */
    private function generateElements(): void
    {
        ob_start();
//        foreach($this->icons as $icon)
        for($i = 0;$i < 50;$i++)
        {
            ?>
            <div
                class="icon"
                style="display: flex;border: solid 0px #00c0ff;cursor: pointer;padding: 10px 0px;align-items: center;justify-content: center;width: 15%;margin: 6px"
                data-icon="<?php echo $this->icons[$i] ?>">
                <i class="<?php echo $this->icons[$i] ?>"></i>
            </div>
            <?php
        }
        $this->elements = ob_get_clean();
    }

    public function enqueue(): void
    {
        wp_enqueue_script('iconpicker-js',Constants::JS.'/icon-picker.js',[],false,'true');
        wp_enqueue_style('fontawesome-css',Defaults::FontawesomeCss,[],false,'true');
        wp_enqueue_script('fontawesome-js',Defaults::FontawesomeJs,[],false,'true');
        wp_localize_script('iconpicker-js','url',['ajax_url' => admin_url('admin-ajax.php')]);
    }

    /**
     * @param int $itemID
     * @param WP_Post $item
     * @return void
     */
    public function addField(int $itemID,WP_Post $item): void
    {
        $selectedIcon = get_post_meta($itemID,'icon',true);

        ?>
        <style>
            .icon:hover{
                border: solid 1px #00c0ff !important;
            }
        </style>
        <div class="field-icon_meta description-wide">
            <button
                type="button"
                class="button icon-picker">
                انتخاب آیکن
                <span>
                    <?php
                    if(str_contains($selectedIcon,'<svg'))
                        echo $selectedIcon;

                    else
                    {
                        ?>
                        <i class="<?php echo $selectedIcon ? $selectedIcon : '' ?>"></i>
                        <?php
                    }
                    ?>
                </span>
            </button>
            <div
                class="icon-wrapper"
                style="display: none;overflow-y: scroll;height: 230px;width: 250px;background-color: #f5f5f5f5;align-items: center;justify-content: center;flex-wrap: wrap;">
                <div class="icon-search" style="width: 95%;margin-top: 10px;align-self: flex-start;">
                    <input type="text" placeholder="جست و جو" style="width: 100%;height: 35px">
                </div>
                <div class="icon-upload" style="width: 45%;margin-left: 5px;margin-top: 10px;align-self: flex-start;">
                    <button
                        class="button"
                        type="button"
                        style="width: 100%;height: 35px">
                        آیکن دلخواه
                    </button>
                </div>
                <div class="icon-remove" style="width: 45%;margin-top: 10px;align-self: flex-start;">
                    <button
                        class="button"
                        type="button"
                        style="width: 100%;height: 35px;color: #b12222;border-color: #b12222;">
                        حذف آیکن
                    </button>
                </div>
                <div class="icons" style="display: flex;align-items: center;justify-content: center;flex-wrap: wrap;">
                    <?php
                        echo $this->elements;
                    ?>
                </div>
                <div style="display: flex;align-items: center;justify-content: center;flex-wrap: wrap">
                    <?php
                    for ($i = 1;$i <= count($this->icons)/50;$i++)
                    {
                        ?>
                        <span
                            class="icon-pagination"
                            data-page="<?php echo $i ?>"
                            style="width: 20px;height: 20px;margin: 2px;border: 1px solid black;border-radius: 8px;display: flex;align-items: center;justify-content: center;cursor: pointer">
                            <?php echo $i ?>
                        </span>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <input id="icon-name"
                   type="hidden"
                   name="icon-<?php echo $itemID?>"
                   value="<?php echo $selectedIcon ? htmlspecialchars($selectedIcon) : '' ?>">
        </div>
        <?php
    }

    /**
     * @param int $menuID
     * @param int $menuItemDbID
     * @return void
     */
    public function saveField(int $menuID,int $menuItemDbID): void
    {
        //sanitize_text_field()
        if (isset($_POST['icon-'.$menuItemDbID]))
            update_post_meta( $menuItemDbID, 'icon', $_POST['icon-'.$menuItemDbID]);
    }
}

new IconPicker();