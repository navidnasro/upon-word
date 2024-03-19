<?php

namespace engine\woocommerce;

defined('ABSPATH') || exit;

class ProductVariant
{
    private $variant;

    private $ID;

    private $description;
    private $dimentions;
    private $attributeName;
    private $regularPrice;
    private $displayPrice;
    private $image;
    private $imageID;
    private $isInStock;
    private $salePercentage;

    function __construct($variant)
    {
        $this->variant = $variant;

        $this->setAttributeName();
        $this->setRegularPrice();
        $this->setDisplayPrice();
        $this->setImage();
        $this->setImageID();
        $this->setIsInStock();
        $this->setID();
        $this->setDescription();
        $this->setDimentions();
        $this->setSalePercentage();
    }

    public function setAttributeName($value = null): void
    {
        if(is_null($value))
            $this->attributeName = $this->getAttributeLabel();

        else
        $this->attributeName = $value;
    }

    public function setRegularPrice($value = null): void
    {
        if(is_null($value) && $this->variant['display_regular_price'] != $this->variant['display_price'])
            $this->regularPrice = $this->variant['display_regular_price'];

        else if($this->variant['display_regular_price'] == $this->variant['display_price'])
            $this->regularPrice = $this->variant['display_price'];

        else
           $this->regularPrice = $value;
    }

    public function setDisplayPrice($value = null): void
    {
        if(is_null($value))
            $this->displayPrice = $this->variant['display_price'];

        else
            $this->displayPrice = $value;
    }

    public function setImage($value = null): void
    {
        if(is_null($value))
            $this->image = $this->variant['image'];

        else
            $this->image = $value;
    }

    public function setImageID($value = null): void
    {
        if(is_null($value))
            $this->imageID = $this->variant['image_id'];

        else
            $this->imageID = $value;
    }

    public function setIsInStock($value = null): void
    {
        if(is_null($value))
            $this->isInStock = $this->variant['is_in_stock'];

        else
            $this->isInStock = $value;
    }

    public function setID($value = null): void
    {
        if(is_null($value))
            $this->ID = $this->variant['variation_id'];

        else
            $this->ID = $value;
    }

    public function setDescription($value = null): void
    {
        if(is_null($value))
            $this->description = $this->variant['variation_description'];

        else
            $this->description = $value;
    }

    public function setDimentions($value = null): void
    {
        if(is_null($value))
            $this->dimentions = $this->variant['dimensions'];

        else
            $this->dimentions = $value;
    }

    public function setSalePercentage($value = null): void
    {
        if(is_null($value))
            $this->salePercentage = 100 - round(((int)$this->displayPrice*100) / $this->regularPrice);
        
        else
            $this->salePercentage = $value;
    }

    public function getSalePercentage()
    {
        return $this->salePercentage;
    }

    public function getDimentions()
    {
        return $this->dimentions;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getID()
    {
        return $this->ID;
    }

    public function getAttributeName()
    {
        return $this->attributeName;
    }

    public function getRegularPrice()
    {
        return $this->regularPrice;
    }

    public function getDisplayPrice()
    {
        return $this->displayPrice;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getImageID()
    {
        return $this->imageID;
    }

    public function getIsInStock()
    {
        return $this->isInStock;
    }

    private function getAttributeLabel(): string
    {
        $name = str_replace(
            [
                'attribute_pa_',
                'attribute_',
                'pa_',
            ],
            '',
            array_keys($this->variant['attributes'])
        )[0];

        if (isset($this->variant['attributes']['attribute_pa_'.$name]))
            return urldecode($this->variant['attributes']['attribute_pa_'.$name]);

        else if (isset($this->variant['attributes']['attribute_'.$name]))
            return urldecode($this->variant['attributes']['attribute_'.$name]);

        else
            return urldecode($this->variant['attributes']['pa_'.$name]);
    }
}