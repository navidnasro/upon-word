<?php

namespace engine\templates\demoOne;

use engine\templates\Article;
use engine\templates\Blog;
use engine\templates\CartBox;
use engine\templates\Footer;
use engine\templates\Header;
use engine\templates\Product;
use engine\templates\Shop;
use engine\templates\TemplateFactory;
use engine\templates\demoOne\CartBox as DemoOneCartBox;
use engine\templates\demoOne\Header as DemoOneHeader;
use engine\templates\demoOne\Footer as DemoOneFooter;
use engine\templates\demoOne\Shop as DemoOneShop;
use engine\templates\demoOne\Product as DemoOneProduct;
use engine\templates\demoOne\Article as DemoOneArticle;
use engine\templates\demoOne\Blog as DemoOneBlog;

class Factory implements TemplateFactory
{
    public function cartBox(): CartBox
    {
        return new DemoOneCartBox();
    }

    public function header(): Header
    {
        return new DemoOneHeader();
    }

    public function footer(): Footer
    {
        return new DemoOneFooter();
    }

    public function shop(): Shop
    {
        return new DemoOneShop();
    }

    public function product(): Product
    {
        return new DemoOneProduct();
    }

    public function article(): Article
    {
        return new DemoOneArticle();
    }

    public function blog(): Blog
    {
        return new DemoOneBlog();
    }
}