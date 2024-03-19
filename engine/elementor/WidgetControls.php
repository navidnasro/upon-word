<?php

namespace engine\elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;
use engine\security\Escape;

defined('ABSPATH') || exit;

class WidgetControls
{
    private Widget_Base $widget;
    private Repeater $repeater;
    private bool $isRepeater;
    private const characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-#@?!0123456789';

    /**
     * @param Widget_Base $widget
     */
    public function __construct(Widget_Base $widget)
    {
        $this->widget = $widget;
        $this->isRepeater = false;
    }

    /**
     * @param string $id
     * @param string $label
     * @return void
     */
    public function startContentSection(string $id,string $label): void
    {
//        if (empty($id))
//            $id = substr(str_shuffle(self::characters),0,7);

        $this->widget->start_controls_section(
            $id,
            [
                'label' => Escape::htmlWithTranslation($label),
                'tab' => Controls_Manager::TAB_CONTENT
            ]
        );
    }

    /**
     * @param string $id
     * @param string $label
     * @return void
     */
    public function startStyleSection(string $id,string $label): void
    {
//        if (empty($id))
//            $id = substr(str_shuffle(self::characters),0,7);

        $this->widget->start_controls_section(
            $id,
            [
                'label' => Escape::htmlWithTranslation($label),
                'tab' => Controls_Manager::TAB_STYLE
            ]
        );
    }

    /**
     * @return void
     */
    public function endSection(): void
    {
        $this->widget->end_controls_section();
    }

    /**
     * @param string $id
     * @param string $label
     * @param string $defaultValue
     * @param array $params
     * @return string
     */
    public function addTextControl(string $id,string $label,string $defaultValue,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::TEXT,
            'default' => Escape::htmlWithTranslation($defaultValue),
            'placeholder' => Escape::htmlWithTranslation($label.' را وارد کنید'),
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param int $min
     * @param int $max
     * @param int $step
     * @param int $defaultValue
     * @param array $params
     * @return string
     */
    public function addNumberControl(string $id,string $label,int $min,int $max,int $step,int $defaultValue,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::NUMBER,
            'min' => $min,
            'max' => $max,
            'step' => $step,
            'default' => $defaultValue,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param int $rows
     * @param string $defaultValue
     * @param array $params
     * @return string
     */
    public function addTextAreaControl(string $id,string $label,int $rows = 10,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::TEXTAREA,
            'rows' => $rows,
            'placeholder' => Escape::htmlWithTranslation('متن خود را وارد کنید'),
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $params
     * @return string
     */
    public function addWysiwygControl(string $id,string $label,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::WYSIWYG,
            'default' => Escape::htmlWithTranslation('متن ویرایشگر'),
            'placeholder' => Escape::htmlWithTranslation('متن را وارد کنید'),
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param string $labelOn
     * @param string $labelOff
     * @param string $returnValue
     * @param string $defaultValue
     * @param array $params
     * @return string
     */
    public function addSwitcherControl(string $id,string $label,string $labelOn,string $labelOff,string $returnValue,string $defaultValue,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => Escape::htmlWithTranslation($labelOn),
            'label_off' => Escape::htmlWithTranslation($labelOff),
            'return_value' => $returnValue,
            'default' => $defaultValue,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $options
     * @param string $defaultValue
     * @param array $css
     * @param array $params
     * @return string
     */
    public function addSelectControl(string $id,string $label,array $options,string $defaultValue,array $css,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::SELECT,
            'default' => $defaultValue,
            'options' => $options,
            'selectors' => $css,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $options
     * @param array $defaultValue
     * @param bool $multiple
     * @param bool $blockLabel
     * @param array $params
     * @return string
     */
    public function addMultipleSelectControl(string $id,string $label,array $options,array $defaultValue,bool $multiple,bool $blockLabel,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::SELECT2,
            'default' => $defaultValue,
            'options' => $options,
            'label_block' => $blockLabel,
            'multiple' => $multiple,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $options
     * @param string $defaultValue
     * @param bool $toggle
     * @param array $css
     * @param array $params
     * @return string
     */
    public function addChooseControl(string $id,string $label,array $options,string $defaultValue,bool $toggle,array $css,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::CHOOSE,
            'options' => $options,
            'default' => $defaultValue,
            'toggle' => $toggle,
            'selectors' => $css,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $css
     * @param array $params
     * @return string
     */
    public function addColorControl(string $id,string $label,array $css,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::COLOR,
            'selectors' => $css,
        ];
        
        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $params
     * @return string
     */
    public function addRepeaterControl(string $id,string $label,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::REPEATER,
            'fields' => $this->repeater->get_controls(),
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $options
     * @param array $params
     * @return string
     */
    public function addUrlControl(string $id,string $label,array $options,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::URL,
            'options' => $options,
            'label_block' => true,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $mediaTypes
     * @param array $params
     * @return string
     */
    public function addMediaControl(string $id,string $label,array $mediaTypes,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::MEDIA,
            'media_types' => $mediaTypes,
            'default' => [
                'url' => Utils::get_placeholder_image_src(),
            ],
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $params
     * @return string
     */
    public function addIconControl(string $id,string $label,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::ICONS,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $defaultValues
     * @param array $css
     * @param array $params
     * @return string
     */
    public function addSliderControl(string $id,string $label,array $defaultValues,array $css,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                    'step' => 1,
                ],
            ],
            'default' => $defaultValues,
            'selectors' => $css,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $label
     * @param array $css
     * @param array $params
     * @return string
     */
    public function addDimensionsControl(string $id,string $label,array $css,array $params = []): string
    {
        $defaults = [
            'label' => Escape::htmlWithTranslation($label),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
            'selectors' => $css,
        ];

        return $this->createControl($id,$defaults,$params);
    }

    /**
     * @param string $id
     * @param string $cssSelector
     * @return void
     */
    public function addTypographyControl(string $id,string $cssSelector): void
    {
//        if (empty($id))
//            $id = substr(str_shuffle(self::characters),0,7);

        $defaults = [
            'name' => $id,
            'selector' => '{{WRAPPER}} '.$cssSelector,
        ];

        $this->createGroupControl(Group_Control_Typography::get_type(),$defaults);
    }

    /**
     * @param string $id
     * @param string $cssSelector
     * @return void
     */
    public function addBorderControl(string $id,string $cssSelector): void
    {
//        if (empty($id))
//            $id = substr(str_shuffle(self::characters),0,7);

        $defaults = [
            'name' => $id,
            'selector' => '{{WRAPPER}} '.$cssSelector,
        ];

        $this->createGroupControl(Group_Control_Border::get_type(),$defaults);
    }

    /**
     * @param string $id
     * @param string $cssSelector
     * @return void
     */
    public function addBoxShadow(string $id,string $cssSelector): void
    {
//        if (empty($id))
//            $id = substr(str_shuffle(self::characters),0,7);

        $defaults = [
            'name' => $id,
            'selector' => '{{WRAPPER}} '.$cssSelector,
        ];

        $this->createGroupControl(Group_Control_Box_Shadow::get_type(),$defaults);
    }

    /**
     * Sets up the repeater to hold controls
     *
     * @return void
     */
    public function startRepeaterControls(): void
    {
        $this->isRepeater = true;
        $this->repeater = new Repeater();
    }

    /**
     * Stops future controls to be added into the repeater
     *
     * @return void
     */
    public function endRepeaterControls(): void
    {
        $this->isRepeater = false;
    }

    public function generateRandomID(): string
    {
        return substr(str_shuffle(self::characters),0,7);
    }

    /**
     * @param string $id
     * @param array $defaults
     * @param array $params
     * @return string
     */
    private function createControl(string $id,array $defaults,array $params = []): string
    {
//        if (empty($id))
//            $id = substr(str_shuffle(self::characters),0,7);

        if ($this->isRepeater)
            $this->repeater->add_control($id,wp_parse_args($params,$defaults));

        else
            $this->widget->add_control($id,wp_parse_args($params,$defaults));

        return $id;
    }

    /**
     * @param string $groupControl
     * @param array $params
     * @return void
     */
    private function createGroupControl(string $groupControl,array $params): void
    {
        if ($this->isRepeater)
            $this->repeater->add_group_control($groupControl,$params);

        else
            $this->widget->add_group_control($groupControl,$params);
    }
}