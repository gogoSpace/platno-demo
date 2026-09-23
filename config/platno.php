<?php

declare(strict_types=1);
use App\Plugins\Hero;
use App\Plugins\Quote;
use Platno\Plugins\Columns;
use Platno\Plugins\Divider;
use Platno\Plugins\File;
use Platno\Plugins\Group;
use Platno\Plugins\Heading;
use Platno\Plugins\Image;
use Platno\Plugins\Link;
use Platno\Plugins\RichText;
use Platno\Plugins\Text;

return [
    'theme' => ['background' => '#f5f4ed', 'text' => '#232621', 'accent' => '#304de3', 'surface' => '#eae9e1', 'width' => 1240, 'font' => 'system'],
    'assets' => ['disk' => 'demo', 'max_kilobytes' => 5120],
    'plugins' => [
        Text::class,
        Image::class,
        File::class,
        Heading::class,
        Link::class,
        Group::class,
        Columns::class,
        Divider::class,
        RichText::class,
        Hero::class,
        Quote::class,
    ],
];
