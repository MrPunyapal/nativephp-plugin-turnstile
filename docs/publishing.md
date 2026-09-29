# Publishing

1. Replace placeholders.
2. Run validation.
3. Push to GitHub.
4. Create the package on Packagist.
5. Tag the first stable version.

```bash
composer validate --strict
composer test
composer lint
git tag v1.0.0
git push origin v1.0.0
```

Packagist will detect the `nativephp-plugin` package type from `composer.json`.
