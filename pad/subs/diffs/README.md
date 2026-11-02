# Diffs

Five kinds compare two positional values: `{diff:words $before, $after/}`. The old PAD diff tag is removed. Word and line algorithms, CSS classes and PHP helpers (`_lib/diff.php`) are retained.

| Kind | Input | Result |
| --- | --- | --- |
| `words` | Two scalar texts | Inline text with escaped deletions and insertions |
| `lines` | Two scalar texts | One column with old and new line numbers and changed words |
| `side` | Two scalar texts | Old and new lines next to one another, changed words highlighted |
| `json` | JSON strings or PHP arrays/objects/scalars | Structural added, removed and changed fields by JSON Pointer path |
| `rows` | Two JSON/native lists of records, `key='id'` | Records matched by key, with added/removed records and changed fields |

`from` and `to` label the versions. Text layouts keep their original empty-label default; structured comparisons default to Before and After. The word kind also accepts the earlier `lines` and `side` options for applications migrating existing option lists.

JSON objects ignore member order; arrays compare by index. Scalar comparisons preserve types (`1` differs from `"1"`, and `false` differs from `0`); integer and decimal PHP representations are retained too. JSON null is different from an absent field, shown as **Absent**. Paths escape `~` as `~0` and `/` as `~1`; `(root)` names the document itself. Empty objects and arrays remain distinct. `same` includes unchanged leaf values; otherwise a comparison without changes says No changes.

Record keys must be unique nonempty strings or integers in each input. An integer key and its decimal string match (`1` and `"1"`); a leading-zero string remains distinct. Record order does not matter. Paths begin with the escaped record key and continue through its changed fields. Missing, empty, unsupported or duplicate keys are errors, never silently matched by position.

Both inputs must be valid JSON-compatible values; malformed JSON, recursion and invalid UTF-8 are rejected. Everything displayed, including paths and labels, is escaped. Both structured kinds use `_lib/structured.php`, whose `padDiffJsonChanges` and `padDiffRowChanges` helpers return change records with explicit presence flags for use outside HTML.

Runnable examples for every kind live in `apps/tags/examples/diff/` and the tags catalog. Add a file under `types/` to register a new kind.
