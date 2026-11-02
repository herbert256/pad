# AI provider subsystem

Provider adapters remain directory driven. `padAi($prompt, $options)` sends a prompt and returns text. All transport tests use `padCurlFake`; no network access is required.

## Local structured-answer validation

Set `json => TRUE` to require a valid JSON answer. Set `schema => [...]` (a PHP array) or a JSON object string such as `schema => '{"type":"object"}'` to also validate a supported schema; a schema implies JSON mode. This is local response validation, not a provider-native structured-output setting. Ask for JSON in the prompt; fences and surrounding prose are rejected. The returned value remains the original trimmed text. On success `padAiStore('json')` holds the decoded value (objects remain `stdClass`, arrays remain arrays). It is reset at the start of every call; `padAiError()` distinguishes successful JSON `null` from failure.

Supported schema keywords: `type` (`object`, `array`, `string`, `integer`, `number`, `boolean`, `null`), `properties`, `required`, boolean `additionalProperties`, `items`, scalar-or-null `enum` (strict typed equality), `minimum`, `maximum`, `minLength`, `maxLength`, `minItems`, `maxItems`. String lengths count Unicode characters. Schemas and validation are bounded to 32 levels. Unknown keywords or invalid schema shapes are author errors before transport; this deliberately does not implement full JSON Schema.

Invalid responses follow existing provider error behaviour: empty text plus `padAiError`, and a strict-check error when enabled. Only validated responses are cached; JSON/schema contracts have separate cache keys, and cached structured text is revalidated. Default provider request shapes remain unchanged.

`padAiCapabilities($provider)` exposes adapter capabilities. Add `native=>true` to opt into supported provider-native JSON/schema requests, while retaining local validation. Unsupported modes fail explicitly. See [capability contracts and examples](../../../docs/subsystems/ai/capabilities.md). The shared local validator now lives in `schemas`; the existing AI wrappers preserve their error messages.
