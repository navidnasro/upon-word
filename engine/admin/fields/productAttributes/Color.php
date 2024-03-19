<?php

namespace engine\admin\fields\productAttributes;

use engine\database\enums\Table;
use engine\database\QueryBuilder;
use WP_Term;

defined('ABSPATH') || exit;

class Color
{
    public string $taxonomyName;

    public function __construct()
    {
        add_action('woocommerce_after_add_attribute_fields',[$this,'enable']);
        add_action('woocommerce_after_edit_attribute_fields',[$this,'editEnable']);
        add_action('woocommerce_attribute_added',[$this,'saveEnabled'],10,2);
        add_action('woocommerce_attribute_updated',[$this,'updateEnabled'],10,2);

        if ($this->setTaxonomyName())
        {
            $builder = new QueryBuilder();

            $attributeID = $builder->select('attribute_id')
                ->from(Table::WOOCOMMERCE_ATTRIBUTE_TAXONOMIES)
                ->where('attribute_name','=',str_replace('pa_','',$this->taxonomyName))
                ->getVar();

    //        $attributeID = $wpdb->get_var(
    //            "SELECT attribute_id
    //             from {$wpdb->prefix}woocommerce_attribute_taxonomies
    //             WHERE attribute_name='".str_replace('pa_','',self::$taxonomyName)."'"
    //        );

            $attributes = get_option('color_attributes');

            //if the taxonomy is marked as color attributes in database
            if (!empty($attributes) && in_array($attributeID,$attributes))
            {
                add_action($this->taxonomyName.'_add_form_fields',[$this,'addColorField']);
                add_action($this->taxonomyName.'_edit_form_fields',[$this,'editColorField'],10,2);
            }

            add_action('created_'.$this->taxonomyName,[$this,'saveColorField'],10,3);
            add_action('edited_'.$this->taxonomyName,[$this,'saveColorField'],10,3);
        }

    }

    /**
     * Sets taxonomy name of the page for color field
     *
     * @return bool returns true if set , else false
     */
    public function setTaxonomyName(): bool
    {
        //if in admin page and taxonomy page of product post type , get the taxonomy name from url
        if (is_admin() && isset($_GET['taxonomy'],$_GET['post_type'])
        && $_GET['post_type'] === 'product' ) 
        {
            $this->taxonomyName = sanitize_text_field($_GET['taxonomy']);

            return true;
        }

        // if it is a post request to edit-tag.php file
        // if the request is from add/edit forms this hidden input exists
        else if(isset($_POST['taxonomy_name']))
        {
            $this->taxonomyName = $_POST['taxonomy_name'];

            return true;
        }

        return false;
    }

    /**
     * @param string $taxonomy
     * @return void
     */
    public function addColorField(string $taxonomy): void
    {
?>
        <div class="form-field">
            <label for="color" style="display: inline;">
                انتخاب رنگ
            </label>
            <input 
            type="color" 
            id="color" 
            name="color" 
            value="#ff0000">
            <input 
            type="hidden" 
            name="taxonomy_name" 
            value="<?php echo sanitize_text_field($taxonomy) ?>">
        </div>
<?php
    }

    /**
     * @param WP_Term $term
     * @param string $taxonomy
     * @return void
     */
    public function editColorField(WP_Term $term, string $taxonomy): void
    {
        $term_id = $term->term_id;
        $color = get_term_meta($term_id,'color',true);
?>
        <div class="form-field">
            <label for="color" style="display: inline;">
                انتخاب رنگ
            </label>
            <input
            type="color"
            id="color"
            name="color"
            value="<?php echo $color ? $color : '#ff0000' ?>">
            <input
            type="hidden"
            name="taxonomy_name"
            value="<?php echo sanitize_text_field($taxonomy) ?>">
        </div>
<?php
    }

    /**
     * saves the value of color input in database
     * 
     * @param int $termID Term ID.
     * @param int $TaxonomyID Term taxonomy ID.
     * @param array $args Arguments passed to wp_insert_term().
     */
    public function saveColorField(int $termID,int $TaxonomyID,array $args): void
    {
        if( isset($_POST['color']) )
            update_term_meta($termID,'color',$_POST['color']);
    }

    /**
     * Adds a checkbox in attribute create page
     * that determines the attribute is going to be color variated
     */
    public function enable()
    {
?>
        <div class="form-field">
            <input type="checkbox" id="color_enable" name="color_checkbox">
            <label for="color_enable" style="display: inline;">
                ویژگی رنگ
            </label>
            <input type="hidden" name="color" value="disabled">
        </div>
        <script>
            jQuery(document).ready(function($){
                $('#color_enable').on('click',function(){
                    var hiddenInput = $(this).siblings('input');

                    if(hiddenInput.val() == 'disabled')
                        hiddenInput.val('enabled');

                        else
                        hiddenInput.val('disabled');
                });
            });
        </script>
<?php
    }

    public function editEnable(): void
    {
        $id = $_GET['edit'];

        $attributes = get_option('color_attributes');
?>
        <div class="form-field">
            <input type="checkbox" id="color_enable" name="color_checkbox" <?php echo in_array($id,$attributes) ? 'checked' : '' ?>>
            <label for="color_enable" style="display: inline;">
                ویژگی رنگ
            </label>
            <input type="hidden" name="color" value="<?php echo in_array($id,$attributes) ? 'enabled' : 'disabled' ?>">
        </div>
        <script>
            jQuery(document).ready(function($){
                $('#color_enable').on('click',function(){
                    var hiddenInput = $(this).siblings('input');

                    if(hiddenInput.val() == 'disabled')
                        hiddenInput.val('enabled');

                    else
                        hiddenInput.val('disabled');
                });
            });
        </script>
<?php
    }

    /**
     * saves attribute "color enabled" field in the database
     * 
     * @param int $id Added attribute ID.
     * @param array $data Attribute data.
     * @return void
     */
    public function saveEnabled(int $id,array $data): void
    {
        //if color checkbox is checked
        if (isset($_POST['color_checkbox']) && ($_POST['color'] == 'enabled'))
        {
            //getting color attributes stored in the database
            $attributes = get_option('color_attributes');
            
            //if option exists and id is not already included
            if($attributes && !in_array($id,$attributes))
            {
                //adding the current attribute among other color attributes
                $attributes[] = $id;
                //inserting the updated array of ids back into the database
                update_option('color_attributes',$attributes);
            }

            //if option doesn't exist
            else
            {
                //create color attribute array
                $attributes = [$id];
                //insert the array into the database
                add_option('color_attributes',$attributes);
            }
        }
    }

    /**
     * @param int $id Added attribute ID.
     * @param array $data Attribute data.
     * @return void
     */
    public function updateEnabled(int $id,array $data): void
    {
        $attributes = get_option('color_attributes');

        // if enable checkbox is present and is checked
        if(isset($_POST['color_checkbox']) && $_POST['color'] == 'enabled')
        {
            //if option exists and id is not already included
            if($attributes && !in_array($id,$attributes))
            {
                //adding the current attribute among other color attributes
                $attributes[] = $id;
                //inserting the updated array of ids back into the database
                update_option('color_attributes',$attributes);
            }

            //if option doesn't exist
            else
            {
                //create color attribute array
                $attributes = [$id];
                //insert the array into the database
                add_option('color_attributes',$attributes);
            }
        }

        // if enable checkbox is present and is unchecked
        else if (isset($_POST['color']) && $_POST['color'] == 'disabled')
        {
            //if there are ids and this id is also included
            if ($attributes && in_array($id,$attributes))
            {
                //removing the id from array
                unset($attributes[array_search($id,$attributes)]);
                //inserting the updated array of ids back into the database
                update_option('color_attributes',array_values($attributes));
            }
        }
    }

    public function sidebarPanel()
    {
        global $wpdb;

        $colors = $wpdb->get_results(
            'SELECT t1.term_id,name,meta_value 
            FROM (SELECT wp_terms.term_id,name,taxonomy 
            FROM `wp_terms` 
            INNER JOIN wp_term_taxonomy 
            ON
             wp_terms.term_id = wp_term_taxonomy.term_id 
             WHERE taxonomy LIKE "pa_%_color") AS t1 
             INNER join wp_termmeta 
             ON
              t1.term_id = wp_termmeta.term_id 
             WHERE wp_termmeta.meta_key = "color";',
             'ARRAY_A'
        );
?>
		<aside id="colors" class="single__widget widget__bg">
			<h4 class="widget__title position__relative h4">
                فیلتر براساس رنگ
            </h4>
			<div class="widget-content">
				<ul class="colors-filter">
<?php 
                    foreach($colors as $color)
                    {
?>  
                        <li>
                            <a style="background-color: <?php echo $color['meta_value'] ?>;" 
                            href="../../woocommerce?color=<?php echo $color['term_id'] ?>"
                            title="<?php echo $color['name'] ?>">
                                <?php echo $color['name'] ?>
                            </a>
                        </li>
<?php
                    }
?>
                </ul>
            </div>
        </aside>
<?php 
    }
}

new Color();