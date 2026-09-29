package com.{{ vendor }}.{{ package }}

import com.nativephp.mobile.bridge.BridgeFunction
import org.json.JSONObject

/**
 * Android bridge functions for {{ vendor }}/{{ package }}.
 *
 * Flow:
 * PHP calls {{ plugin }}::example()
 * NativePHP invokes {{ plugin }}.Example
 * Kotlin receives the JSON payload here
 * Android APIs can be called from execute()
 * A JSONObject is returned to PHP
 */
class {{ plugin }}Functions {
    class Example : BridgeFunction() {
        override fun execute(payload: JSONObject): JSONObject {
            val response = JSONObject()

            response.put("plugin", "{{ plugin }}")
            response.put("platform", "android")
            response.put("received", payload)

            return response
        }
    }
}
