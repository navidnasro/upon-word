<?php

namespace engine\utils;

use WP_Post;

defined('ABSPATH') || exit;

class Walker
{
    private int $objectID;
    private string $type;
    private string $title;
    private string $classes;
    private string $url;
    private int $menuItemParentID;
    private int $menuItemID;
    private bool $isAncestor;
    private bool $isParent;
    private bool $hasChildren;

    public function __construct(WP_Post $dataObject)
    {
        $this->objectID = $dataObject->object_id;
        $this->type = $dataObject->type;
        $this->title = $dataObject->title;
        $this->classes = implode(' ',$dataObject->classes);
        $this->url = $dataObject->url;
        $this->menuItemParentID = $dataObject->menu_item_parent;
        $this->menuItemID = $dataObject->db_id;
        $this->isAncestor = $dataObject->current_item_ancestor;
        $this->isParent = $dataObject->current_item_parent;
        $this->hasChildren = in_array('menu-item-has-children',$dataObject->classes);
    }

    public static function getDataObject(WP_Post $dataObject): Walker
    {
        return new self($dataObject);
    }

    /**
     * @return string
     */
    public function getClasses(): string
    {
        return $this->classes;
    }

    /**
     * @return int
     */
    public function getMenuItemID(): int
    {
        return $this->menuItemID;
    }

    /**
     * @return int
     */
    public function getMenuItemParentID(): int
    {
        return $this->menuItemParentID;
    }

    /**
     * @return int
     */
    public function getObjectID(): int
    {
        return $this->objectID;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isParent(): bool
    {
        return $this->isParent;
    }

    /**
     * @return bool
     */
    public function isAncestor(): bool
    {
        return $this->isAncestor;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->hasChildren;
    }
}