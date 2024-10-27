<?php

namespace engine\templates;

interface CartBox
{
    public function prepare(array $data = []): CartBox;
    public function getResponsive(): CartBox;
    public function openWrapper(): void;
    public function image(): void;
    public function title(): void;
    public function price(): void;
    public function closeWrapper(): void;
    public function render(): void;
}