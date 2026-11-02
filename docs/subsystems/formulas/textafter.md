# Formula: TEXTAFTER

`TEXTAFTER(text, delimiter, [instance_num], [match_mode], [match_end], [if_not_found])` extracts text after the chosen delimiter. Positive occurrence counts from the front, negative from the back, nonoverlapping in that direction. Match mode 0 is case-sensitive, 1 case-insensitive. Match end 1 adds the boundary in the search direction as a final delimiter. Empty delimiter matches immediately; empty text returns empty text. Missing matches give #N/A or the supplied fallback; zero/oversized occurrences or invalid modes give #VALUE!. Uses shared scalar coercion, Unicode matching and the 32767-character cell limit. No delimiter wildcards or dynamic arrays. Example: `=TEXTAFTER("a-b-c","-",-1)`.

Reference contract and examples: https://support.microsoft.com/en-us/excel/functions/textafter-function . Run `php tests/subsystems/formula-textafter.php`.
