<?php

namespace engine\admin\metaboxes;

use engine\Loader;

defined('ABSPATH') || exit;

class Register
{
    public static function registerMetaBox(MetaBox $metaBox): void
    {
        add_action('add_meta_boxes',function () use ($metaBox) {

            add_meta_box(
                $metaBox->getID(),
                $metaBox->getTitle(),
                [$metaBox,'ui'],
                $metaBox->getScreen(),
                $metaBox->getContext(),
                $metaBox->getPriority(),
                $metaBox->uiArgs()
            );

        });

        add_action('save_post',[$metaBox,'save']);
    }
}
//Loads entire classes within namespace
Loader::require(__DIR__,'MetaBox.php');