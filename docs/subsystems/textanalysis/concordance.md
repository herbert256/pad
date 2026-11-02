# textanalysis: concordance

Query is a normalized token/phrase (default token mode, 1..32 tokens) or literal substring. Reuses existing normalization and case folding. Returns original UTF-8 byteOffset/byteLength and unchanged matched text; context is bounded in extended grapheme clusters (0..200). Substring matches must begin/end at normalized grapheme boundaries, so case-fold expansion never splits an original grapheme. ≤1 MiB input, 100000 graphemes/tokens, 1024 query bytes and 1000 results; truncated signals further matches. Results are data: consuming HTML widgets must escape left/text/right before highlighting.

Runnable example: `apps/tags/examples/textanalysis/concordance.pad`. Independent fixture: `php tests/subsystems/expansion-textanalysis-concordance.php`.
