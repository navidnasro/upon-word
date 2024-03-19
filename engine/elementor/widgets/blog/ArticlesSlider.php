<?php

namespace engine\elementor\widgets\blog;

use Elementor\Widget_Base;
use engine\elementor\WidgetControls;
use engine\elementor\widgets\Register;

defined('ABSPATH') || exit;

class ArticlesSlider extends Widget_Base
{
    public function get_name(): string
    {
        return 'ArticlesSlider';
    }

    public function get_title(): string
    {
        return 'اسلایدر مقالات';
    }

    public function get_icon(): string
    {
        return 'eicon-thumbnails-right';
    }

    public function get_categories(): array
    {
        return [ 'ribar-category' ];
    }

    protected function register_controls(): void
    {
        $controlManager = new WidgetControls($this);

        $controlManager->startContentSection('dhkwj','تنظیمات');

        $controlManager->addTypographyControl('jfwl','.class');
        $controlManager->addColorControl('jfwlkfw','رنگ',
            [
                '{{WRAPPER}} .title' => 'color: {{VALUE}}',
            ]
        );

        $controlManager->endSection();
    }

    protected function render(): void
    {

    }
}

Register::register(new ArticlesSlider());