<?php

namespace engine\enums;

defined('ABSPATH') || exit;

enum Defaults
{
    const Favicon = Constants::IMG.'/theme-favicon.png'; // favicon image
    const Logo = Constants::IMG.'/logo.png'; // logo image
    const ProductNoImage = Constants::IMG.'/product-no-image.jpg'; // product image not set
    const PostNoImage = Constants::IMG.'/post-no-image.jpg'; // post image not set
    const TermNoImage = Constants::IMG.'/empty-cart.png'; // post image not set
    const NotFound = Constants::IMG.'404.png'; // 404 image
    const CssFile = Constants::CSS.'/style.css'; // css main file (general file)
    const SwiperCss = Constants::CSS.'/plugins/swiper-bundle.min.css'; // swiper css file
    const FontawesomeCss = Constants::CSS.'/plugins/fontawesome.min.css'; // fontawesome css file
    const Colors = Constants::CSS.'/colors.css'; // color variables css file
    const Fonts = Constants::CSS.'/fonts.css'; // fonts css file
    const JsFile = Constants::JS.'/script.js'; // js main file (general file)
    const Jquery = Constants::JS.'/plugins/jquery.js'; // jquery file
    const Tailwind = Constants::JS.'/plugins/tailwind.js'; // tailwind file
    const FontawesomeJs = Constants::JS.'/plugins/fontawesome.min.js'; // fontawesome css file
    const SwiperJs = Constants::JS.'/plugins/swiper-bundle.min.js'; // swiper js file
}