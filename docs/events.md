# Events

Use events when native code needs to notify the Laravel application about asynchronous state.

The template includes:

```php
{{ namespace }}\Events\{{ plugin }}Event
```

Events listed in `nativephp.json` document the package-level event surface:

```json
"events": [
  "{{ namespace }}\\Events\\{{ plugin }}Event"
]
```

Keep event payloads serializable because mobile events can cross process and platform boundaries.
