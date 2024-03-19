<?php

namespace engine\admin\metaboxes;

use WP_Post;

defined('ABSPATH') || exit;

class ProductCondition implements MetaBox
{
    private string $ID;
    private string $title;
    private string $screen;
    private string $context;
    private string $priority;

    public function __construct()
    {
        $this->ID = 'product-condition';
        $this->title = 'وضعیت محصول';
        $this->screen = 'product'; //screen or post-type   hint : get_post_types() => (adds metabox to all post types)
        $this->context = 'normal'; // Context
        $this->priority = 'high'; // position of meta box
    }

    public function ui(WP_Post $post): void
    {
        $condition = get_post_meta($post->ID,'condition',true);
        ?>
        <div style="display: flex;align-items: center;justify-content: start;">
            <span style="display: flex;align-items: center;justify-content: start;margin-left: 20px">
                <input type="radio" name="condition" value="new" <?php echo $condition == 'new' ? 'checked' : ''?>>
                <span style="margin-right: 10px">نو(پلمپ)</span>
            </span>
            <span>
                <input type="radio" name="condition" value="old" <?php echo $condition == 'old' ? 'checked' : ''?>>
                <span style="margin-right: 10px">دست دوم</span>
            </span>
        </div>
        <?php
    }

    public function save(int $postID): void
    {
        if (isset($_POST['condition']) && $_POST['condition'])
            update_post_meta($postID, 'condition', $_POST['condition']);
    }

    public function getID(): string
    {
        return $this->ID;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getScreen(): string
    {
        return $this->screen;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function uiArgs(): array|null
    {
        return null;
    }
}

Register::registerMetaBox(new ProductCondition());