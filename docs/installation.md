# Installation

This template becomes installable after placeholders are replaced and the package is published.

```bash
composer require {{ vendor }}/{{ package }}
```

Laravel auto-discovery loads `{{ namespace }}\Providers\{{ plugin }}ServiceProvider`.

For local development inside a NativePHP Mobile app:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../{{ package }}"
    }
  ]
}
```

Then install:

```bash
composer require {{ vendor }}/{{ package }}:@dev
```
