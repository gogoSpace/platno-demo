<?php

declare(strict_types=1);

namespace App\Demo;

final class StudioTemplate
{
    public function __construct(private readonly ContentBlocks $blocks) {}

    /** @return array{version: int, blocks: list<array<string, mixed>>} */
    public function document(string $imageIdentifier): array
    {
        return ['version' => 1, 'blocks' => [
            $this->blocks->make('demo.hero', [
                'eyebrow' => 'Independent design practice · Copenhagen',
                'title' => "A practice in\npossibility.",
                'description' => 'We bring a curious eye and a considered hand to the things you see, touch, and spend time with.',
                'image' => $imageIdentifier,
                'alt' => 'Sunlight crossing a quiet design studio with a travertine table and a cobalt blue chair.',
                'caption' => 'A place for thinking. A space for making.',
                'variant' => 'studio',
            ]),
            $this->blocks->make('columns', [
                'left' => [
                    $this->blocks->make('heading', ['text' => 'Good things take shape.']),
                ],
                'right' => [
                    $this->blocks->make('rich-text', ['content' => $this->blocks->paragraphs([
                        'Forma is a small studio with a wide field of view. We work at the meeting point of identity, objects, and space — looking for the simple idea that makes everything click.',
                        'Our process begins with a conversation and a blank sheet of paper. What follows is a close collaboration, from the first question to the final detail.',
                    ])]),
                ],
            ]),
            $this->blocks->make('group', [
                'tone' => 'muted',
                'children' => [
                    $this->blocks->make('text', ['text' => 'OUR PRACTICE']),
                    $this->blocks->make('columns', [
                        'left' => [
                            $this->blocks->make('heading', ['text' => 'Identities with character.', 'level' => 'h3']),
                            $this->blocks->make('text', ['text' => 'Clear ideas, distinctive visual worlds, and the small details that make a brand feel like itself. Built to live beyond a launch.']),
                        ],
                        'right' => [
                            $this->blocks->make('heading', ['text' => 'Spaces with a point of view.', 'level' => 'h3']),
                            $this->blocks->make('text', ['text' => 'Materials, light, and thoughtful proportions. Places that feel good to be in, and objects that earn their place in them.']),
                        ],
                    ]),
                ],
            ]),
            $this->blocks->make('demo.quote', [
                'quote' => 'Make fewer things. Give each one more thought.',
                'author' => 'The Forma approach',
                'detail' => 'Curiosity first. Clarity always.',
                'tone' => 'cobalt',
            ]),
            $this->blocks->make('divider'),
            $this->blocks->make('columns', [
                'left' => [$this->blocks->make('heading', ['text' => 'A new perspective starts here.', 'level' => 'h3'])],
                'right' => [$this->blocks->make('text', ['text' => "Based in Copenhagen. Working with good people everywhere.\nBrand identity · Art direction · Spatial design"])],
            ]),
        ]];
    }
}
