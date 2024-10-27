<?php

namespace engine\admin\metaboxes;

use engine\utils\Request;
use WP_Post;

defined('ABSPATH') || exit;

class TitleDescription implements MetaBox
{
    private string $ID;
    private string $title;
    private string $screen;
    private string $context;
    private string $priority;

    public function __construct()
    {
        $this->ID = 'title-desc';
        $this->title = 'توضیح عنوان';
        $this->screen = 'product'; //screen or post-type   hint : get_post_types() => (adds metabox to all post types)
        $this->context = 'normal'; // Context
        $this->priority = 'high'; // position of meta box
    }

    public function ui(WP_Post $post): void
    {
        $title = get_post_meta($post->ID,'title-desc',true);

        ?>
        <div class="title-desc" style="display: flex ; align-items: center; justify-content: flex-start;">
            <p style="margin: 0 10px 0;">
                توضیحی درباره عنوان وارد کنید:
            </p>
            <input style="width: 100%;" type="text" value="<?php echo $title ? $title : '' ?>" name="title-desc">
        </div>

        <?php
    }

    public function save(int $postID): void
    {
        $request = Request::post();

        if (is_null($request))
            return;

        if ($request->has('title-desc') && !empty($request->getParam('title-desc')))
            update_post_meta($postID,'title-desc',$request->getParam('title-desc'));
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

Register::registerMetaBox(new TitleDescription());