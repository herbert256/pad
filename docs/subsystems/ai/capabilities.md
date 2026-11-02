# Adapter capabilities and native structured outputs

`padAiCapabilities()` returns metadata for every installed provider; pass a provider name to inspect one. Metadata describes **implemented PAD adapter behavior**, not every model or custom endpoint. Text, local JSON and local schema validation are available throughout. Tools and streaming are false because this helper does not implement those response protocols.

Existing `json` / `schema` requests remain local validation unless `native=>true` is explicitly added. Native JSON and schema requests are supported for OpenAI chat completions, Azure's compatible v1 adapter, and Ollama chat. Anthropic Messages supports native schema requests. Other adapters fail explicitly before transport when native output is requested. A model or custom endpoint may still reject a valid adapter request; its error propagates normally. No API models/defaults were changed and no paid provider requests were made during validation.

OpenAI/Azure use `response_format`; Ollama uses `format`; Anthropic uses `output_config.format`. A plain native JSON request adds an explicit JSON-object instruction. All native responses are still checked locally, and native/local cache entries are separated. Schema property maps serialize as JSON objects even when empty. Native requests never silently remove bounds, change optional fields to required fields, or fall back to another mode.

OpenAI native schemas require an object root, explicit types, `additionalProperties=false` on objects, every property required exactly once, and items on arrays. Anthropic requires closed objects and rejects the bounds this adapter does not support natively (minimum/maximum, string/array lengths); use local validation for these contracts. The local PAD subset remains the schema authority. Provider-specific complexity/model restrictions can still produce a server error.

```php
$schema = ['type'=>'object', 'properties'=>['ok'=>['type'=>'boolean']],
  'required'=>['ok'], 'additionalProperties'=>false];
$text = padAi('Return whether the supplied record is complete.',
  ['provider'=>'openai', 'schema'=>$schema, 'native'=>true]);
```

Checked against [OpenAI structured outputs](https://developers.openai.com/api/docs/guides/structured-outputs?api-mode=chat), [Ollama structured outputs](https://docs.ollama.com/capabilities/structured-outputs) and [Anthropic structured outputs](https://platform.claude.com/docs/en/build-with-claude/structured-outputs), 2026-11-02. `php tests/subsystems/ai-capabilities.php` checks mocked request/response shapes, unsupported modes, invalid answers, cache separation and errors. The broader AI regression suite uses `padCurlFake`.
