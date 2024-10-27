<?php

namespace engine\admin\fields\productAttributes;

defined('ABSPATH') || exit;

abstract class Base
{
    public function __construct()
    {
        add_action('woocommerce_after_add_attribute_fields',[$this,'addField']);
        add_action('woocommerce_after_edit_attribute_fields',[$this,'editField']);
        add_action('woocommerce_attribute_added',[$this,'save'],10,2);
        add_action('woocommerce_attribute_updated',[$this,'update'],10,3);
    }

    abstract public function addField();
    abstract public function editField();
    abstract public function save(int $id,array $data);
    abstract public function update(int $id,array $data,string $oldSlug);
}