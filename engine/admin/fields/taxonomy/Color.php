<?php

namespace engine\admin\fields\taxonomy;

defined('ABSPATH') || exit;

use engine\database\enums\Table;
use engine\database\QueryBuilder;
use WP_Term;

class Color extends Base
{
    private string $taxonomyName = '';

    public function __construct()
    {
        $addAction = false;

        if ($this->setTaxonomyName())
        {
            $builder = QueryBuilder::getInstance();

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
                $addAction = true;
            }
        }

        parent::__construct($this->taxonomyName,$addAction);
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
    public function addField(string $taxonomy): void
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
    public function editField(WP_Term $term, string $taxonomy): void
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
    public function save(int $termID,int $TaxonomyID,array $args): void
    {
        if( isset($_POST['color']) )
            update_term_meta($termID,'color',$_POST['color']);
    }

    /**
     * updates the value of color input in database
     *
     * @param int $termID Term ID.
     * @param int $TaxonomyID Term taxonomy ID.
     * @param array $args Arguments passed to wp_insert_term().
     */
    public function update(int $termID,int $TaxonomyID,array $args): void
    {
        $this->save($termID,$TaxonomyID,$args);
    }
}

new Color();