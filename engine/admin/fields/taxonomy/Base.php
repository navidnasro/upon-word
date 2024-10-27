<?php

namespace engine\admin\fields\taxonomy;

defined('ABSPATH') || exit;

use WP_Term;

abstract class Base
{
    public function __construct(string $taxonomy = '',bool $addAction = false)
    {
        if (!empty($taxonomy) && $addAction)
        {
            add_action($taxonomy.'_add_form_fields',[$this,'addField']);
            add_action($taxonomy.'_edit_form_fields',[$this,'editField'],10,2);
            add_action('created_'.$taxonomy,[$this,'save'],10,3);
            add_action('edited_'.$taxonomy,[$this,'update'],10,3);
        }
    }

    abstract public function addField(string $taxonomy);
    abstract public function editField(WP_Term $term, string $taxonomy);
    abstract public function save(int $termID,int $TaxonomyID,array $args);
    abstract public function update(int $termID,int $TaxonomyID,array $args);
}