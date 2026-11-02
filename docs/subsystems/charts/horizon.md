# horizon

`{chart:horizon label='day', value='north,south', bands=3, band=10}` folds signed series into compact strips. Rows are equally spaced in input order; numeric columns are series. `bands` is 1–6; `band` sets the width in original units. Without a positive band width the maximum absolute value divided by bands is used (1 for all zero). Values beyond an explicit band range saturate.

Positive and negative magnitudes are folded upward in the first and second series colors; successive bands darken through opacity. Threshold crossings are linearly interpolated before folding, including sign changes. Missing observations break the area. The description includes original values and band scale. Height expands to at least 45 pixels per series. Standard palette, title, dimensions and client options apply; when overriding the palette, the first two slots denote positive/negative.

Use at least two observations for an area. The chart parity runner covers positive/negative threshold crossings, zero, missing samples and explicit scale.
