# TEXTBEFORE and TEXTAFTER

Both functions accept `text, delimiter, [instance_num], [match_mode], [match_end], [if_not_found]`. Defaults are instance 1, case-sensitive matching and no boundary match.

- `TEXTBEFORE` returns the text before a delimiter; `TEXTAFTER` returns the text after it.
- Positive instances count from the start, negative instances from the end. Matches in the chosen direction do not overlap.
- `match_mode=1` ignores case. Delimiters are literal; `*` and `?` have no wildcard meaning. Matching and slicing preserve UTF-8 characters.
- `match_end=1` counts the boundary in the search direction as one additional delimiter: the end for forward searches, the beginning for reverse searches.
- A missing match returns `if_not_found`, preserving that value's type, or `#N/A` if omitted. An unused fallback error does not replace a successful result.
- Instance zero, an absolute instance larger than the text's character count, invalid UTF-8, or a mode other than 0/1 returns `#VALUE!`. Empty input text returns empty text. Numeric option values are truncated toward zero, as other formula counts are.
- An empty delimiter matches immediately: before returns empty text from the start or all text from the end; after returns the inverse.

```text
=TEXTBEFORE("Ada Lovelace"," ")        Ada
=TEXTAFTER("a/b/c","/",-1)             c
=TEXTBEFORE("Socrates"," ",,,1)        Socrates
=TEXTAFTER("caféÉfin","é",2,1)         fin
=TEXTBEFORE("abc","!",1,0,0,"none")   none
```

Argument behavior follows Microsoft's [TEXTBEFORE](https://support.microsoft.com/en-us/excel/functions/textbefore-function) and [TEXTAFTER](https://support.microsoft.com/en-us/excel/functions/textafter-function) references. PAD uses its existing scalar formula model; delimiter arrays and spilled-array results are not supported.
