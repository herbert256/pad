# Text analysis

`padTextAnalysis(kind, textOrRows, options = [])` provides `counts`, `ngrams` and
`similarity`. Each string is valid UTF-8, at most 1 MiB and 100000 tokens. Invalid
inputs call `padError`. Default `normalization='nfc'` requires PHP intl; explicitly
choose `none` when it is unavailable. `caseFold=true` uses Unicode case folding.
`locale='und'` is the sole supported locale: no language-specific segmentation,
stemming or stop words. Result metadata states these choices.

Tokens are runs starting with Unicode letters/numbers, continuing through letters,
numbers and combining marks, with internal straight/curly apostrophes. Emoji and
punctuation are not word tokens. Counts report normalized byte/codepoint/grapheme
counts, token/unique-token counts, blank-line-separated paragraphs, and reading
minutes using explicit `wordsPerMinute=200` (0 < rate <= 10000).

`ngrams` uses contiguous tokens with `n=2` (1..5) and `limit=100` (1..10000). Counts
sort descending with bytewise lexical ties. N-grams span punctuation and paragraph
boundaries; they are measurements of the token stream, not linguistic phrases.

`similarity` accepts at most 200 identity-keyed document strings, 4 MiB/100000 total
tokens, and at most 5000000 pairwise token lookups. `metric='jaccard'` compares token
sets; `metric='cosine'` compares token frequency vectors. Two empty documents score
1, one empty document scores 0. `threshold=0` filters scores inclusively in [0,1].
Pairs sort by decreasing score with stable input-order ties. Scores are symmetric;
this is lexical overlap, not semantic similarity or a plagiarism verdict.

`/showcase/?analysis/duplicates` is a local duplicate-document report.
