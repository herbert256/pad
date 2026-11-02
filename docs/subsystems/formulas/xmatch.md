# Formula: XMATCH

`XMATCH(value, lookup_array, [match_mode], [search_mode])` returns a one-based position. Match modes 0 exact, -1 nearest smaller, 1 nearest larger, 2 wildcard (*, ?, ~); search 1 forward, -1 reverse, 2 ascending binary, -2 descending binary. Binary modes require sorted data of comparable types; wildcard requests scan in the selected direction. Linear duplicate matches follow direction; binary duplicate selection is unspecified. Uses existing case-insensitive, type-aware XLOOKUP comparisons. Missing matches return #N/A, invalid modes or non-vector ranges #VALUE!. Example: `=XMATCH("Grape",A1:A8)`.

Reference contract and examples: https://support.microsoft.com/en-us/excel/functions/xmatch-function . Run `php tests/subsystems/formula-xmatch.php`.
