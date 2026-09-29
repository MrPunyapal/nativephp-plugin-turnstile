# Bridge Functions

A bridge function connects PHP to native platform code.

## PHP to Android

```text
{{ namespace }}\Facades\{{ plugin }}::example()
  -> {{ namespace }}\Plugin::example()
  -> "{{ plugin }}.Example"
  -> com.{{ vendor }}.{{ package }}.{{ plugin }}Functions.Example
  -> Android platform logic
  -> JSONObject response
```

## PHP to iOS

```text
{{ namespace }}\Facades\{{ plugin }}::example()
  -> {{ namespace }}\Plugin::example()
  -> "{{ plugin }}.Example"
  -> {{ plugin }}Functions.Example
  -> iOS platform logic
  -> dictionary response
```

## Adding A Function

1. Add a `bridge_functions` entry to `nativephp.json`.
2. Add the Kotlin class under `resources/android`.
3. Add the Swift class under `resources/ios`.
4. Add a PHP method on `{{ namespace }}\Contracts\{{ plugin }}Contract`.
5. Implement the method on `{{ namespace }}\Plugin`.
6. Add tests for the manifest and PHP call.
