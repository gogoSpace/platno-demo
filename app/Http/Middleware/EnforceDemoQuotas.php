<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Platno\Models\Asset;
use Platno\Models\Page;
use Platno\Models\Publication;
use Symfony\Component\HttpFoundation\Response;

final class EnforceDemoQuotas
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethodSafe()) {
            return $next($request);
        }
        $document = $request->input('document');
        if ($document !== null) {
            $bytes = is_string($document) ? strlen($document) : strlen(json_encode($document, JSON_THROW_ON_ERROR));
            if ($bytes > config('demo.max_document_bytes')) {
                $this->reject('document', 'This temporary demo has reached its document size limit.');
            }
        }
        if ($request->routeIs('platno.pages.store') && Page::query()->count() >= config('demo.max_pages')) {
            $this->reject('title', 'This demo allows up to three pages. Keep editing an existing page.');
        }
        if ($request->routeIs('platno.pages.publish') && Publication::query()->count() >= config('demo.max_publications')) {
            $this->reject('revision', 'This temporary demo has reached its publication limit.');
        }
        if ($request->routeIs('platno.assets.store')) {
            $file = $request->file('file');
            if (! $file instanceof UploadedFile) {
                $this->reject('file', 'Choose a single file to upload.');
            }
            $size = $file->getSize();
            if (Asset::query()->count() >= config('demo.max_assets') || Asset::query()->sum('size') + $size > config('demo.max_asset_bytes')) {
                $this->reject('file', 'This demo media library is full. Remove an unused upload before adding another.');
            }
        }

        return $next($request);
    }

    private function reject(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}
