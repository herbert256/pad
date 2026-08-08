# Pages moved out of the manual

Seven pages that the manual application held but never showed. Nothing linked them: they were
not in the menu, and no reachable page embedded them, so a reader could only arrive by typing the
url. They are parked here, where an unlinked page is what the application is made of.

| page | what it is |
|------|------------|
| `table_fun` | "10 ways to show the same table" - it embeds the ten `tableFun/` examples, which moved back with it |
| `3_ways_to_make_a_table` | the same ground as the manual's Start end page, at three times the length |
| `name` | how PAD names a tag, and the `name=` option. Nothing in the manual covers it |
| `variable_kinds` | unfinished - it names `$` and then lists `? ! # &` with nothing against any of them |
| `doc3` | was `properties/doc3`; the Properties page embeds doc1, doc2 and doc4 and skips it |
| `z99` | was `php_and_html/z99`, a stub outside the z01..z42 sequence that page walks. It is the one of the seven that uses nothing from `_common`, so it stayed in `regression2/manual/` when the rest moved here |

`manual/hello/index` was the eighth and is not here: it was byte for byte `check/hello/index`,
which this application already had.

`_tags/` and `_lib/` are the manual's, copied for the pages that need them - `{construct}` and
`{source}`, and the `{content 'myContent1'}` definition `doc3` prints through. `{tag}` is not
copied because check has the same file already.

The one link out of here that pointed at a manual page is written absolute, so it still arrives.
