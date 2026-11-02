# Shared schema validation

`padSchema(kind, value, schema, options = [])` returns `{valid, issues, kind}`.
`issues` is empty or contains the first deterministic `{path, code, message}`.
Paths retain the AI boundary's `$`, `.property`, `[index]` notation; property names
containing dots/brackets are printed literally. There is no coercion or remote I/O.
No options are currently supported.

The `jsonschema` kind implements only the existing PAD AI subset: `type`,
`properties`, `required`, `additionalProperties` (boolean), `items`, scalar `enum`,
`minimum`, `maximum`, `minLength`, `maxLength`, `minItems`, `maxItems`. Unknown
keywords, including `$ref`, fail. This is not full JSON Schema compliance. Types
are object, array, string, integer, number, boolean, null. Finite integral floats
satisfy integer; enums preserve PHP scalar identity (1 differs from 1.0).

JSON objects are stdClass and arrays are PHP lists, including the distinction
between `{}` and `[]`. Null differs from absence. Strings must be valid UTF-8;
length constraints count code points, not graphemes. Nonfinite numbers, resources,
custom objects and associative arrays in JSON data fail. Schema nesting is limited
to 32 logical schema levels, value validation to 32 levels when traversed by the
schema. All inputs additionally have a 64-level structural and 100000-value bound.

`record` accepts the same schema with implied `type='object'` and adapts its top-level
associative PHP array (including empty array) to an object. Nested values remain
explicit JSON objects/lists. `rowset` applies that record adapter to at most 10000
rows and returns issue paths such as `$[2].name`.

AI's `padAiJsonSchemaError` and `padAiJsonValueError` delegate to this extracted
core; their messages and original validation behavior remain unchanged. The public
helper adds input bounds; provider/cache/answer handling is unchanged.
`/showcase/?analysis/import-contract` uses the same nested contract at a local JSON
import boundary and at the real AI response validator, without calling a provider.
