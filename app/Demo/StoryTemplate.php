<?php

declare(strict_types=1);

namespace App\Demo;

final class StoryTemplate
{
    public function __construct(private readonly ContentBlocks $blocks) {}

    /** @return array{version: int, blocks: list<array<string, mixed>>} */
    public function document(string $imageIdentifier): array
    {
        return ['version' => 1, 'blocks' => [
            $this->blocks->make('demo.hero', [
                'eyebrow' => 'A coastal journal',
                'title' => "The quiet\nbetween places.",
                'description' => 'A few days at the edge of the water. No particular plan. Nothing we needed to bring home.',
                'image' => $imageIdentifier,
                'alt' => 'A pale limestone house above Mediterranean cliffs, with the blue sea stretching into the distance.',
                'caption' => 'The sort of view that asks nothing of you.',
                'variant' => 'story',
            ]),
            $this->blocks->make('columns', [
                'left' => [
                    $this->blocks->make('heading', ['text' => 'An unhurried arrival.']),
                    $this->blocks->make('text', ['text' => "Words by Mira Ellis\nLate summer · A three-minute escape"]),
                ],
                'right' => [
                    $this->blocks->make('rich-text', ['content' => $this->blocks->paragraphs([
                        'We reached the house just as the afternoon light turned the stone the colour of warm bread. Below us, the sea moved in long, almost imperceptible lines.',
                        'There was no list of things to see. We opened the shutters, set our bags beside the door, and watched a small boat trace the curve of the bay.',
                        'By the second morning, even the thought of checking the time seemed unnecessary. Coffee tasted better outside. A walk could take all day.',
                    ])]),
                ],
            ]),
            $this->blocks->make('demo.quote', [
                'quote' => 'Some places give you something to remember. Others give you room to notice.',
                'author' => 'From the notebook',
                'detail' => 'The coast, somewhere in late summer',
                'tone' => 'ink',
            ]),
            $this->blocks->make('group', [
                'tone' => 'muted',
                'children' => [
                    $this->blocks->make('heading', ['text' => 'Taking the slower road.']),
                    $this->blocks->make('rich-text', ['content' => $this->blocks->paragraphs([
                        'The best part of the journey was the part we had not planned: a narrow path between dry-stone walls, an empty table in the shade, the sound of plates being set down in a kitchen.',
                        'We stayed a little longer than we meant to. On the way home, nobody put on any music.',
                    ])]),
                ],
            ]),
            $this->blocks->make('divider'),
            $this->blocks->make('text', ['text' => 'FIELDNOTES — Small stories from a wider world.']),
        ]];
    }
}
