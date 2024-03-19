<?php

namespace engine\admin\taxonomies;

use WP_Term;

defined('ABSPATH') || exit;

interface Taxonomy
{
    public function createFields(): void;
    public function editFields(WP_Term $term): void;
    public function insert(int $termID): void;
    public function update(int $termID): void;
    public function getPostType(): string;
    public function getID(): string;
    public function getLabels(): array;
    public function getDescription(): string;
    public function isPublic(): bool;
    public function isHierarchical(): bool;
    public function hasAdminColumn(): bool;
}