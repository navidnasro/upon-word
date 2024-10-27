<?php

namespace engine\templates\demoTwo;

use engine\templates\CartBox as CartBoxInterface;

class CartBox implements CartBoxInterface
{
    public function render(): void
    {
        // TODO: Implement render() method.
        // TODO: render the cart box template for demo 2
    }

    public function prepare(array $data = []): CartBoxInterface
    {
        // TODO: Implement prepare() method.
        return $this;
    }

    public function getResponsive(): CartBoxInterface
    {
        // TODO: Implement getResponsive() method.
        return $this;
    }

    public function openWrapper(): void
    {
        // TODO: Implement openWrapper() method.
    }

    public function image(): void
    {
        // TODO: Implement image() method.
    }

    public function title(): void
    {
        // TODO: Implement title() method.
    }

    public function price(): void
    {
        // TODO: Implement price() method.
    }

    public function closeWrapper(): void
    {
        // TODO: Implement closeWrapper() method.
    }
}