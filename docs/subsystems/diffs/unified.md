# Unified context diffs

`{diff:unified $before, $after, context=3/}` renders escaped unified diff text with old/new labels and context hunks. `context` is an integer 0..100. Explicit `\ No newline at end of file` markers distinguish an absent final newline from an empty line. CRLF normalizes to LF; lone CR remains data. Input is UTF-8, at most 1 MiB and 6000 combined lines.

Include `PAD.'subs/diffs/_lib/unified.php'` to use `padDiffUnifiedHunks($before,$after,$context)` directly. Each hunk contains `old`, `oldCount`, `new`, `newCount`, and ordered `lines` with `=`, `-`, `+` operations. `padDiffUnified` renders the hunks. The existing `padDiffCompare('unified',...)` returns the same typed line-change records as the line comparator; use the hunk helper when ranges/context are required. This is a display/export representation, not a patch executor.

`php tests/subsystems/diff-unified.php` checks ranges, newline markers, escaping and randomized reconstruction of both input streams.
