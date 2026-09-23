# Platno demo engineering codex

This is a standalone Laravel consumer of `platno/laravel`, not the package itself. Keep all presentation, visitor ownership, sandbox quotas and expiration here. Package changes belong in the sibling package repository and require their own verification.

- Read README before work. Use Laravel's supported container, routing, validation and storage APIs. Follow Pint, strict types and descriptive full names.
- No mandatory frontend framework, PrimeVue, UI kit or build dependency. Do not add dependencies without explicit authorization.
- Preserve the package boundary: no package login/auth implementation. Demo cookie ownership is host middleware, never a public share URL.
- Every visitor gets a private SQLite database and media root. All editor endpoints must enter the owner context. Public share links select read-only contexts and expose published references only.
- Expiration is fixed at creation; session visits and public reads never extend it. Every request must fail closed at expiration. Cleanup is separately scheduled and must not delete an active locked workspace.
- Do not edit production data or reuse local demonstrations as tests. Tests may write only to fresh, guarded disposable directories. Cover isolation, expiration, publication, quotas and cleanup; do not test labels or CSS.
- Never commit without an explicit instruction. Never amend, force-push or rewrite history. Use short English commit subjects without prefixes.
- Keep preview servers running. Document actual verification and limitations; Experimental Preview is not production-ready.
