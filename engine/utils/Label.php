<?php

namespace engine\utils;

defined('ABSPATH') || exit;

class Label
{
    /**
     * Generates the label array
     *
     * @param string $singular
     * @return string[]
     */
    public static function getLabel(string $singular): array
    {
        $plural = $singular.' ها ';
        $subjectPlural = $singular.' های ';
        $subjectSingular = $singular.' یی ';

        return [
            'name'                  => $plural,
            'singular_name'         => $singular,
            'menu_name'             => $plural,
            'name_admin_bar'        => $singular,
            'add_new'               => 'اضافه کردن جدید',
            'add_new_item'          => ' جدید '.$singular.' اضافه کردن ',
            'new_item'              => ' جدید '.$singular,
            'edit_item'             => ' ویرایش '.$singular,
            'view_item'             => ' مشاهده '.$singular,
            'all_items'             => ' همه '.$plural,
            'search_items'          => ' جست و جو '.$singular,
            'parent_item_colon'     => $subjectPlural.' مادر ',
            'not_found'             => ' هیچ '.$subjectSingular.' یافت نشد ',
            'not_found_in_trash'    => ' هیچ '.$subjectSingular.' در زباله دان نیست ',
            'featured_image'        => ' تصویر '.$singular,
            'set_featured_image'    => ' تنظیم تصویر '.$singular,
            'remove_featured_image' => ' حذف تصویر '.$singular,
            'use_featured_image'    => ' استفاده تصویر به عنوان '.$singular,
            'archives'              => ' آرشیو '.$singular,
            'insert_into_item'      => ' اضافه کردن به '.$singular,
            'uploaded_to_this_item' => ' بارگذاری برای این '.$singular,
            'filter_items_list'     => ' فیلتر کردن لیست '.$plural,
            'items_list_navigation' => ' منو لیست '.$plural,
            'items_list'            => ' لیست '.$plural,
        ];
    }
}