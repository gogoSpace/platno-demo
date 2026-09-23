<?php

declare(strict_types=1);

namespace App\Demo;

use Illuminate\Support\Str;
use Platno\Plugins\PluginRegistry;

final class ContentBlocks
{
    public function __construct(private readonly PluginRegistry $plugins) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array{id: string, type: string, version: int, data: array<string, mixed>}
     */
    public function make(string $type, array $data = []): array
    {
        $plugin = $this->plugins->get($type);

        return [
            'id' => (string) Str::uuid(),
            'type' => $type,
            'version' => $plugin->schemaVersion(),
            'data' => array_replace($plugin->defaults(), $data),
        ];
    }

    /**
     * @param  list<string>  $paragraphs
     * @return list<array{type: string, runs: list<array{text: string, bold: bool, italic: bool, link: string}>}>
     */
    public function paragraphs(array $paragraphs): array
    {
        return array_map(static fn (string $paragraph): array => [
            'type' => 'paragraph',
            'runs' => [['text' => $paragraph, 'bold' => false, 'italic' => false, 'link' => '']],
        ], $paragraphs);
    }
}
