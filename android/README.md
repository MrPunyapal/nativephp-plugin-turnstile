# Android Packaging

NativePHP Mobile plugins keep installable Android source in `resources/android`.

The file `resources/android/{{ plugin }}Functions.kt` is copied into the generated Android project when `{{ vendor }}/{{ package }}` is installed in a NativePHP Mobile app.

Use the package name from `nativephp.json`:

```json
"android": "com.{{ vendor }}.{{ package }}.{{ plugin }}Functions.Example"
```

When replacing placeholders, keep the Kotlin package and manifest bridge target in sync.
