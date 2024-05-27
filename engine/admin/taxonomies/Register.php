<?php

namespace engine\admin\taxonomies;

use engine\Loader;

defined('ABSPATH') || exit;

class Register
{
    public static function registerTaxonomy(Taxonomy $taxonomy): void
    {
        add_action('init',function () use ($taxonomy){

            register_taxonomy(
                $taxonomy->getID(),
                $taxonomy->getPostType(),
                [
                    'labels' => $taxonomy->getLabels(),
                    'description' => $taxonomy->getDescription(),
                    'public' => $taxonomy->isPublic(),
                    'hierarchical' => $taxonomy->isHierarchical(),
                    'show_admin_column' => $taxonomy->hasAdminColumn()
                ],
            );

        });

        add_action($taxonomy->getID().'_add_form_fields',[$taxonomy,'createFields'],10);
        add_action($taxonomy->getID().'_edit_form_fields',[$taxonomy,'editFields']);
        add_action('edited_'.$taxonomy->getID(),[$taxonomy,'insert']);
        add_action('created_'.$taxonomy->getID(),[$taxonomy,'update']);
    }
}

Loader::require(__DIR__,'Taxonomy.php');