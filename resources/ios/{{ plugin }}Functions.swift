import Foundation
import NativePHP

/// iOS bridge functions for {{ vendor }}/{{ package }}.
///
/// Flow:
/// PHP calls {{ plugin }}::example()
/// NativePHP invokes {{ plugin }}.Example
/// Swift receives the payload here
/// iOS APIs can be called from execute()
/// A dictionary is returned to PHP
public enum {{ plugin }}Functions {
    public final class Example: BridgeFunction {
        public init() {}

        public func execute(payload: [String: Any]) async throws -> Any {
            return [
                "plugin": "{{ plugin }}",
                "platform": "ios",
                "received": payload,
            ]
        }
    }
}
