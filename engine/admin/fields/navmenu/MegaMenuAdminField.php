<?php

namespace engine\admin\fields\navmenu;

use WP_Post;

defined('ABSPATH') || exit;

class MegaMenuAdminField
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('wp_nav_menu_item_custom_fields',[$this,'addField'],10,2);
        add_action('wp_update_nav_menu_item',[$this,'saveField'],10,2);
    }

    /**
     * Hooks into wp_nav_menu_item_custom_fields to add a new menu field.
     *
     * @param int $itemID
     * @param WP_Post $item
     * @return void
     */
    public function addField(int $itemID,WP_Post $item): void
    {
        $megaMenu = get_post_meta($itemID, 'megamenu', true );

        $megaMenus = get_posts(
            [
                'post_type' => 'megamenu'
            ]
        );

        ?>

        <p class="field-megamenu_meta description-wide">
            <label for="edit-menu-item-attr-<?php echo $itemID; ?>">
               انتخاب مگامنو
                <select id="edit-menu-item-attr-<?php echo $itemID; ?>"
                        name="megamenu-<?php echo $itemID ?>">
                    <option value="">پیشفرض قالب</option>
                    <?php
                    foreach ($megaMenus as $menu)
                    {
                        ?>
                        <option value="<?php echo $menu->ID; ?>" <?php echo $megaMenu == $menu->ID ? 'selected' : '' ?>>
                            <?php echo $menu->post_title ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </label>
        </p>

        <?php
    }

    /**
     * Hooks into wp_update_nav_menu_item to save our custom field.
     *
     * @param int $menuID ID of the updated menu.
     * @param int $menuItemDbID ID of the updated menu item.
     * @return void
     */

    public function saveField(int $menuID, int $menuItemDbID): void
    {
        if (isset($_POST['megamenu-'.$menuItemDbID]))
            update_post_meta( $menuItemDbID, 'megamenu', sanitize_text_field($_POST['megamenu-'.$menuItemDbID]));

    }
}

new MegaMenuAdminField();