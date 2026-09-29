package com.{{ vendor }}.{{ package }}

import org.json.JSONObject

/**
 * Standalone Android starter for {{ vendor }}/{{ package }}.
 *
 * NativePHP consumes the installable bridge implementation from
 * resources/android/{{ plugin }}Functions.kt. This file documents a conventional
 * Android source layout for maintainers who later split native code into a
 * dedicated Android module.
 */
class BridgeFunctions {
    fun example(payload: JSONObject): JSONObject {
        val response = JSONObject()

        response.put("plugin", "{{ plugin }}")
        response.put("platform", "android")
        response.put("received", payload)

        return response
    }
}
