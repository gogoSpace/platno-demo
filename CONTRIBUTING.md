# Contributing

Platno demo is an **Experimental Preview (pre-alpha)**. Contributions should make the package easier to try, understand, or integrate while preserving the temporary workspace boundary.

## Choose the right repository

Use this repository for the gallery, demo templates, example plugins, framework hosts, visitor ownership, quotas, expiration, and cleanup. Changes to the editor, document format, publication behavior, or package asset APIs belong in [laravel-platno](https://github.com/gogoSpace/laravel-platno).

For a bug, provide a short reproduction, expected and actual behavior, PHP version, browser, chosen template, and integration mode. For a larger feature, explain the use case in an issue before opening a broad pull request. Keep security reports private; see [SECURITY.md](SECURITY.md).

## Development

Follow the sibling-checkout installation in [README.md](README.md#run-locally). The Composer path dependency links `../platno`, so changes there are immediately used by the demo. A pull request changing both repositories should link the related package change and state which package commit it was verified against.

Keep runtime code free of a required frontend build. Native JavaScript modules, Blade, and scoped CSS are intentional. Vue, React, and Livewire are optional host examples for the same editor. New dependencies and changes to the package's public contract deserve discussion before implementation.

Use strict PHP types, descriptive names, Laravel's supported APIs, and small classes with clear responsibilities. Escape values in Blade. Register custom blocks through the plugin contract; do not copy package internals into the application.

## Verification

Run the existing checks before submitting:

```sh
composer check
```

For JavaScript changes, Node 24 can check module syntax without installing any npm dependencies:

```sh
for source in public/scripts/*.js public/adapters/*.js; do
    node --input-type=module --check < "$source"
done
```

Add focused tests for confirmed regressions and changes to ownership, routing, data integrity, quotas, expiration, or cleanup. Extend the guarded temporary-workspace fixture in `tests/TestCase.php`; never point a test at an existing demo registry, local visitor files, or a deployed installation. Do not run destructive database commands against shared environments.

For visual changes, inspect both the editor canvas and the public page at desktop and mobile widths. Verify keyboard focus, long text, empty fields, loading failures, and the affected integration mode. Preserve the same Blade rendering and content styles in the canvas and public output.

## Pull requests

Describe the problem, the resulting behavior, and the checks you actually ran. Include before/after screenshots when they help review a visual change. Call out any unverified behavior or operational change, especially anything touching workspace storage or cleanup. Keep unrelated refactoring out of a focused fix.

Do not include `.env`, application keys, cookies, visitor databases, uploads, or local infrastructure details. Commit source assets only when their provenance and redistribution terms are known; document demo imagery in [docs/assets.md](docs/assets.md).

By contributing, you agree to distribute your contribution under this project's [MIT license](LICENSE).
