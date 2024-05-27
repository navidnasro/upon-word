<?php

namespace engine\templates\demoTwo;

use engine\templates\Article;
use engine\templates\Blog;
use engine\templates\CartBox;
use engine\templates\Footer;
use engine\templates\Header;
use engine\templates\Product;
use engine\templates\Shop;
use engine\templates\TemplateFactory;
use engine\templates\demoTwo\CartBox as DemoTwoCartBox;
use engine\templates\demoTwo\Header as DemoTwoHeader;
use engine\templates\demoTwo\Footer as DemoTwoFooter;
use engine\templates\demoTwo\Shop as DemoTwoShop;
use engine\templates\demoTwo\Product as DemoTwoProduct;
use engine\templates\demoTwo\Article as DemoTwoArticle;
use engine\templates\demoTwo\Blog as DemoTwoBlog;

class Factory implements TemplateFactory
{
    public function cartBox(): CartBox
    {
        return new DemoTwoCartBox();
    }

    public function header(): Header
    {
        return new DemoTwoHeader();
    }

    public function footer(): Footer
    {
        return new DemoTwoFooter();
    }

    public function shop(): Shop
    {
        return new DemoTwoShop();
    }

    public function product(): Product
    {
        return new DemoTwoProduct();
    }

    public function article(): Article
    {
        return new DemoTwoArticle();
    }

    public function blog(): Blog
    {
        return new DemoTwoBlog();
    }
}