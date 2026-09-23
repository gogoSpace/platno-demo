# Security policy

This project is an **Experimental Preview (pre-alpha)**. Security fixes target the current `main` branch; there are no maintained stable demo releases yet.

## Report privately

Use [GitHub's private vulnerability report form](https://github.com/gogoSpace/platno-demo/security/advisories/new). Do not open a public issue containing an exploit, ownership cookie, private workspace data, or another visitor's share link.

Include the affected commit, a minimal reproduction using your own local or disposable workspace, the expected boundary, and the observed impact. Redact application keys, cookies, personal data, and server credentials. If the report concerns the package rather than this demo host, mention that distinction so it can be coordinated with [laravel-platno](https://github.com/gogoSpace/laravel-platno).

Please avoid accessing another visitor's data, stressing the public service, or continuing beyond the minimum demonstration needed to explain a problem. A local reproduction is preferred.

## Intended boundaries

- An ownership cookie grants edit access only to its unexpired workspace. A public share token must not grant editor access.
- Every workspace has its own content database and private media root. No request may select another visitor's storage using document or asset input.
- Public routes are read-only and expose eligible published content and media. Draft-only assets stay private.
- Expiration is fixed at creation. Access ends at that deadline even if cleanup has not run.
- Cleanup must preserve active workspaces and skip files locked by an in-flight request.
- Upload validation, document validation, escaped rendering, CSRF checks, and demo quotas remain active in every integration mode.

The demo is not a production authentication system or durable storage service. Deployment operators are responsible for HTTPS, private storage, application secrets, scheduler operation, and server permissions.
