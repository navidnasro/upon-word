<?php

namespace engine\settings\codestar;

use CSF;
use engine\enums\Constants;
use engine\security\Escape;

class Sections
{
    private string $prefix;

    public function __construct()
    {
        $this->prefix = Constants::SettingsObjectID;

        // Control core classes for avoid errors
        if(class_exists('CSF'))
        {
            $this->product();
        }
    }

    private function product(): void
    {
        // Create a section
        CSF::createSection($this->prefix,
            [
                'title'  => Escape::htmlWithTranslation('محصول'),
                'fields' => [
                    [
                        'id'         => 'product-card',
                        'type'       => 'radio',
                        'title'      => Escape::htmlWithTranslation('کارت محصول'),
                        'options'    => [
                            'demo1' => Escape::htmlWithTranslation('پیشفرض قالب'),
                            'demo2' => Escape::htmlWithTranslation('مخصوص'),
                        ],
                        'default' => 'demo1'
                    ],
                ]
            ]
        );
    }
}

new Sections();