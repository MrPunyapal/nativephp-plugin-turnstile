# Permissions

Declare permissions only when the native implementation needs them.

## Android

Add Android permissions to `nativephp.json`:

```json
"permissions": [
  "android.permission.CAMERA"
]
```

Add hardware or software features only when required:

```json
"features": [
  "android.hardware.camera"
]
```

## iOS

Add Info.plist usage descriptions to `ios.info_plist`:

```json
"info_plist": {
  "NSCameraUsageDescription": "{{ description }}"
}
```

Permission descriptions must explain the feature in user-facing language.
