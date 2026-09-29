# Releasing

Use semantic versioning:

- Patch: backwards-compatible bug fixes.
- Minor: backwards-compatible new bridge functions or options.
- Major: breaking PHP API, manifest, Android API, or iOS API changes.

Release checklist:

1. Update `CHANGELOG.md`.
2. Run `composer lint`.
3. Run `composer test`.
4. Create a GitHub release.
5. Tag `vMAJOR.MINOR.PATCH`.

The release workflow validates the package when tags are pushed.
