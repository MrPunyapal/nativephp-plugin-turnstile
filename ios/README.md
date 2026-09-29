# iOS Packaging

NativePHP Mobile plugins keep installable iOS source in `resources/ios`.

The file `resources/ios/{{ plugin }}Functions.swift` is copied into the generated iOS project when `{{ vendor }}/{{ package }}` is installed in a NativePHP Mobile app.

Use the Swift symbol from `nativephp.json`:

```json
"ios": "{{ plugin }}Functions.Example"
```

When replacing placeholders, keep the Swift type and manifest bridge target in sync.
