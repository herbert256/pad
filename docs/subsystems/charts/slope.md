# slope

`{chart:slope label='name', from='before', to='after'}...{/chart:slope}` connects numeric observations across two named period axes. Explicit from/to fields, or the first two numeric fields, name the periods. Negative values are supported. An absent endpoint has no dot or connecting line; a row with neither endpoint is omitted.

Both axes share a value scale. Endpoint labels are separated vertically with short leader lines back to the actual values, preserving the meaning of ties. Height expands by 18 pixels per observed row when necessary; narrow labels are shortened with the full text in a tooltip and accessible description. Colors repeat after eight observations. Standard title, width/height, palette and client options apply.

Run the chart parity runner with `--kinds=slope`; regression covers ties, missing values, negative observations, escaping and narrow width.
