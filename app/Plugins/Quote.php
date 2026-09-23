<?php

declare(strict_types=1);

namespace App\Plugins;

use Platno\Plugins\Plugin;

final class Quote extends Plugin
{
    public function type(): string
    {
        return 'demo.quote';
    }

    public function label(): string
    {
        return 'Pull quote';
    }

    public function fields(): array
    {
        return [
            'quote' => ['type' => 'textarea', 'label' => 'Quote', 'required' => true, 'default' => 'A thought worth a little more space.', 'max' => 700],
            'author' => ['type' => 'text', 'label' => 'Attribution', 'max' => 150],
            'detail' => ['type' => 'text', 'label' => 'Attribution detail', 'max' => 200],
            'tone' => ['type' => 'select', 'label' => 'Colour', 'options' => ['ink' => 'Ink on ivory', 'cobalt' => 'Cobalt blue']],
        ];
    }

    public function view(): string
    {
        return 'plugins.quote';
    }
}
