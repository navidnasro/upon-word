<?php

namespace engine\templates;

interface TemplateFactory
{
    public function cartBox(): CartBox;
    public function header(): Header;
    public function footer(): Footer;
    public function shop(): Shop;
    public function product(): Product;
    public function article(): Article;
    public function blog(): Blog;
}