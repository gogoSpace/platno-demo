<?php

declare(strict_types=1);

namespace App\Demo;

final class ProductTemplate
{
    public function __construct(private readonly ContentBlocks $blocks) {}

    /** @return array{version: int, blocks: list<array<string, mixed>>} */
    public function document(string $imageIdentifier): array
    {
        return ['version' => 1, 'blocks' => [
            $this->blocks->make('demo.hero', [
                'eyebrow' => 'Arc Objects / The everyday collection',
                'title' => "An ordinary day.\nAn extraordinary blue.",
                'description' => 'A generous curve. A colour with presence. A familiar object, seen a little differently.',
                'image' => $imageIdentifier,
                'alt' => 'A cobalt blue ceramic pitcher with a loop handle beside an ivory bowl on limestone plinths.',
                'caption' => 'Edition 01 — Cobalt & chalk',
                'variant' => 'product',
            ]),
            $this->blocks->make('columns', [
                'left' => [$this->blocks->make('heading', ['text' => 'Form follows ritual.'])],
                'right' => [
                    $this->blocks->make('rich-text', ['content' => $this->blocks->paragraphs([
                        'A slow breakfast. Water for the table. A handful of stems brought in from outside. The things we use every day become part of the way we live.',
                        'Arc begins with those small rituals. Simple forms, tactile surfaces, and just enough character to make the ordinary feel considered.',
                    ])]),
                ],
            ]),
            $this->blocks->make('group', [
                'tone' => 'muted',
                'children' => [
                    $this->blocks->make('text', ['text' => 'THE DETAILS']),
                    $this->blocks->make('columns', [
                        'left' => [
                            $this->blocks->make('heading', ['text' => 'A softer silhouette.', 'level' => 'h3']),
                            $this->blocks->make('text', ['text' => 'The open loop handle balances a rounded body. Nothing added for the sake of it; every curve has a reason to be there.']),
                        ],
                        'right' => [
                            $this->blocks->make('heading', ['text' => 'A place at your table.', 'level' => 'h3']),
                            $this->blocks->make('text', ['text' => 'Deep cobalt meets warm ivory. Two quiet companions that work just as well on their own, from an early coffee to a long Sunday lunch.']),
                        ],
                    ]),
                ],
            ]),
            $this->blocks->make('demo.quote', [
                'quote' => 'The useful things should be the beautiful things.',
                'author' => 'Arc Objects',
                'detail' => 'Objects for the life in between.',
                'tone' => 'cobalt',
            ]),
            $this->blocks->make('divider'),
            $this->blocks->make('columns', [
                'left' => [$this->blocks->make('heading', ['text' => 'Keep good company.', 'level' => 'h3'])],
                'right' => [$this->blocks->make('text', ['text' => 'Thoughtful objects. Familiar rituals. A little more colour in the everyday.'])],
            ]),
        ]];
    }
}
