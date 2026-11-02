# Structured comparisons

`padDiffCompare($kind, $before, $after, $options = [])` returns change records with `op` (`add`, `remove`, `replace`), JSON Pointer `path`, and `before`/`after` only when present. No records are emitted for equal values. `padDiffRender($records, $side, $from, $to)` renders an escaped accessible HTML table. It never applies changes.

Kinds: `words`, `lines`, `json`, `rows`. Text comparisons report token or line positions; their records are descriptive, not an executable JSON Patch. Existing `padDiffWords`, `padDiffLines` and `{pad:diff $old, $new, lines, side/}` output is preserved. `kind='lines'` is an alias for `lines`.

`json` accepts JSON text or PHP JSON values (list arrays, associative arrays, stdClass, finite scalars). A string input is decoded as JSON: encode a string scalar with `json_encode`. Object key order is ignored; array positions and scalar types matter, including integer versus float. Missing properties differ from null. Empty PHP arrays represent JSON arrays; use `(object) []` for an empty object.

`rows` requires lists of arrays and `['key' => 'id']`. Keys must be nonempty strings or integers, unique on each side. Integer 1 and string "1" are distinct identities. Row reordering alone produces no changes. Paths start with the escaped typed key (`/i:1` or `/s:1`); each record also contains the original `key`. Missing/duplicate keys throw an exception. Nested properties use the JSON rules.

Template forms: `{pad:diff $before, $after, kind='json', side/}` and `{pad:diff $before, $after, kind='rows', key='id'/}`. Inputs are bounded to 20000 nodes per side, depth 64 and 1 MiB per text; the record API bounds text comparisons to 20000 tokens in total. Invalid JSON, non-finite numbers and non-JSON objects are rejected. Run `php apps/regression/subsystems/diffs.php`.
