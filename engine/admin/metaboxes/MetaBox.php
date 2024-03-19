<?php

namespace engine\admin\metaboxes;

use WP_Post;

defined('ABSPATH') || exit;

interface MetaBox
{
    public function ui(WP_Post $post): void;
    public function save(int $postID): void;
    public function getID(): string;
    public function getTitle(): string;
    public function getScreen(): string;
    public function getContext(): string;
    public function getPriority(): string;
    public function uiArgs(): array | null;
}