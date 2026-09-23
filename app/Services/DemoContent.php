<?php

declare(strict_types=1);

namespace App\Services;

use App\Demo\ProductTemplate;
use App\Demo\StoryTemplate;
use App\Demo\StudioTemplate;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Platno\Assets\AssetLibrary;
use Platno\Models\Page;
use Platno\Publishing\DraftManager;
use RuntimeException;
use Throwable;

final class DemoContent
{
    public function __construct(
        private readonly AssetLibrary $assets,
        private readonly DraftManager $drafts,
        private readonly StudioTemplate $studio,
        private readonly StoryTemplate $story,
        private readonly ProductTemplate $product,
    ) {}

    /** @return array<string, array{name: string, title: string, description: string, category: string, image: string, slug: string, accent: string}> */
    public static function templates(): array
    {
        return [
            'studio' => [
                'name' => 'The studio',
                'title' => 'Forma Studio',
                'description' => 'A considered home for an independent design practice.',
                'category' => 'Portfolio & practice',
                'image' => '/images/atelier.jpg',
                'slug' => 'forma-studio',
                'accent' => '#304de3',
            ],
            'story' => [
                'name' => 'The story',
                'title' => 'Fieldnotes',
                'description' => 'An unhurried photo essay from the edge of the water.',
                'category' => 'Journal & editorial',
                'image' => '/images/story.jpg',
                'slug' => 'the-quiet-between-places',
                'accent' => '#304de3',
            ],
            'product' => [
                'name' => 'The object',
                'title' => 'Arc Objects',
                'description' => 'A small collection with a generous point of view.',
                'category' => 'Product & collection',
                'image' => '/images/object.jpg',
                'slug' => 'arc-objects',
                'accent' => '#304de3',
            ],
        ];
    }

    public function seed(string $template): Page
    {
        $metadata = self::templates()[$template] ?? null;

        if ($metadata === null) {
            throw ValidationException::withMessages(['template' => 'Choose an available page template.']);
        }

        $existingPage = Page::query()->where('slug', $metadata['slug'])->first();

        if ($existingPage !== null) {
            return $existingPage;
        }

        $sourcePath = public_path(ltrim($metadata['image'], '/'));

        if (! is_file($sourcePath)) {
            throw new RuntimeException('The local demo image has not been prepared: '.basename($sourcePath));
        }

        $asset = $this->assets->upload(new UploadedFile($sourcePath, basename($sourcePath), 'image/jpeg', null, true));

        try {
            $document = match ($template) {
                'studio' => $this->studio->document($asset->getKey()),
                'story' => $this->story->document($asset->getKey()),
                'product' => $this->product->document($asset->getKey()),
            };

            return $this->drafts->create($metadata['slug'], $metadata['title'], $document);
        } catch (Throwable $exception) {
            try {
                $this->assets->delete($asset->getKey());
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }
    }
}
