<?php

declare(strict_types=1);

namespace App\Plugins;

use Platno\Plugins\Plugin;

final class Hero extends Plugin
{
    public function type(): string
    {
        return 'demo.hero';
    }

    public function label(): string
    {
        return 'Editorial hero';
    }

    public function fields(): array
    {
        return [
            'eyebrow' => ['type' => 'text', 'label' => 'Eyebrow', 'default' => 'A fresh perspective', 'max' => 150],
            'title' => ['type' => 'textarea', 'label' => 'Headline', 'required' => true, 'default' => 'Make something worth sharing.', 'max' => 200],
            'description' => ['type' => 'textarea', 'label' => 'Introduction', 'max' => 700],
            'image' => ['type' => 'asset', 'kind' => 'image', 'label' => 'Cover image', 'required' => true],
            'alt' => ['type' => 'text', 'label' => 'Image description', 'required' => true, 'default' => 'Describe the scene in the cover image.', 'max' => 500],
            'caption' => ['type' => 'text', 'label' => 'Image caption', 'max' => 200],
            'variant' => ['type' => 'select', 'label' => 'Composition', 'options' => ['studio' => 'Studio', 'story' => 'Editorial', 'product' => 'Product']],
        ];
    }

    public function view(): string
    {
        return 'plugins.hero';
    }
}
