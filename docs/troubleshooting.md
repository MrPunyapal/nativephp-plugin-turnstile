# Troubleshooting

## Composer cannot validate the package name

Replace `{{ vendor }}` and `{{ package }}` with lowercase Composer-safe values.

## Kotlin package does not compile

Kotlin package segments cannot contain hyphens. Use a platform-safe package segment and keep `nativephp.json` in sync.

## iOS cannot find the bridge function

Check that `nativephp.json` uses the exact Swift symbol, for example:

```json
"ios": "{{ plugin }}Functions.Example"
```

## Android cannot find the bridge function

Check that `nativephp.json` uses the exact Kotlin target, for example:

```json
"android": "com.{{ vendor }}.{{ package }}.{{ plugin }}Functions.Example"
```

## Tests fail before placeholder replacement

This repository is a template. Replace placeholders before running PHP syntax checks, Composer validation, or native compilation.
