# Syntax highlighters

`padHighlight`, `padHighlightTokens`, the `highlight` pipe and `{pad:highlight}` read language definitions from `pad/subs/highlighters/types`. Existing canonical languages retain their output and PHP uses its native tokenizer. New lexical grammars cover Python (`py`), Go (`golang`), Rust (`rs`), unified diff (`patch`) and TypeScript (`ts`, `tsx`). TypeScript now recognizes type declarations rather than using the JavaScript alias. These are lexical coloring rules, not parsers: embedded languages, nested Rust comments and Python formatted-string interpolation are not parsed separately.

Every token and unmatched character is escaped centrally. Rules cannot insert raw source HTML. Existing line numbers, marking, dedenting and file restrictions are unchanged. Language files return `aliases` and ordered `rules` using existing `hl-*` token classes; PHP keeps its tokenizer path. Invalid language paths are never included.

Run `php apps/regression/subsystems/highlighters.php` for compatibility hashes, grammar, HTML-injection and long-input fixtures.
