# Versioning

The PHP, Android, iOS, and manifest surfaces are one package contract.

Breaking changes include:

- Renaming a bridge function.
- Removing a manifest event.
- Changing a return payload shape.
- Raising `android.min_version`.
- Raising `ios.min_version`.
- Removing a facade or contract method.

Prefer adding new bridge functions instead of changing existing names.
