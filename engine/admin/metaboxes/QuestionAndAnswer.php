<?php

namespace engine\admin\metaboxes;

use engine\security\Escape;
use engine\utils\Request;
use WP_Post;

class QuestionAndAnswer implements MetaBox
{
    private string $ID;
    private string $title;
    private string $screen;
    private string $context;
    private string $priority;

    public function __construct()
    {
        $this->ID = 'product-q&a';
        $this->title = Escape::htmlWithTranslation('پرسش و پاسخ');
        $this->screen = 'product'; //screen or post-type   hint : get_post_types() => (adds metabox to all post types)
        $this->context = 'normal'; // Context
        $this->priority = 'high'; // position of meta box
    }

    public function ui(WP_Post $post): void
    {
        $isActive = get_post_meta($post->ID,'q&a',true);
        ?>
        <div style="display: flex;flex-direction: column">
            <p><?php echo Escape::htmlWithTranslation('فعال سازی پرسش و پاسخ برای این محصول') ?></p>
            <div style="display: flex;align-items: center;">
                <span style="display: flex">
                    <span><?php echo Escape::htmlWithTranslation('فعال سازی') ?></span>
                    <input type="radio" name="q&a" value="yes" <?php echo $isActive == 'yes' ? 'selected' : '' ?>>
                </span>
                <span style="display: flex">
                    <span><?php echo Escape::htmlWithTranslation('غیر فعال سازی') ?></span>
                    <input type="radio" name="q&a" value="no" <?php echo $isActive == 'no' || empty($isActive) ? 'selected' : '' ?>>
                </span>
            </div>
        </div>
        <?php
    }

    public function save(int $postID): void
    {
        $request = Request::post();

        if (is_null($request))
            return;

        if ($request->has('q&a'))
            update_post_meta($postID,'q&a',$request->getParam('q&a'));
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

Register::registerMetaBox(new QuestionAndAnswer());