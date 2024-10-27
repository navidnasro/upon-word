<?php

namespace engine\templates;

use engine\templates\demoOne\Factory as Demo1Factory;
use engine\templates\demoTwo\Factory as Demo2Factory;
use engine\utils\CodeStar;

defined('ABSPATH') || exit;

class DemoFactory
{
    private static ?TemplateFactory $factory = null;

    /**
     * Returns selected factory
     *
     * @return TemplateFactory|null
     * @usage at boot time
     */
    public static function getDemoFactory(): ?TemplateFactory
    {
        if (CodeStar::getOption('active-demo') == 'demo1')
        {
            if (is_null(self::$factory))
                self::$factory = new Demo1Factory();

            return self::$factory;
        }

        else if (CodeStar::getOption('active-demo') == 'demo2')
        {
            if (is_null(self::$factory))
                self::$factory = new Demo2Factory();

            return self::$factory;
        }

        return self::$factory;
    }

    /**
     * Returns factory for a specific demo
     *
     * @param int $demoNumber
     * @return TemplateFactory|null
     * @usage in app decision
     */
    public static function getFactory(int $demoNumber): ?TemplateFactory
    {
        if ($demoNumber == 1)
            return new Demo1Factory();

        else if ($demoNumber == 2)
            return new Demo2Factory();

        else
            return self::$factory;
    }
}