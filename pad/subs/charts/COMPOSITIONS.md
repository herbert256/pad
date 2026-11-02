# Ribbon, lollipop and ternary

All three accept at most 1,000 rows, `label`, `title`, `width`, `height`, and `client`.
They have matching PHP and browser renderers and accessible per-point titles.

* `ribbon`: fields `x`, `value`, `low`, `high`. X is numeric (convert timestamps to epoch seconds first). Bounds are supplied quantities, never inferred confidence intervals. `low <= value <= high`. Any null interval member breaks the band; a known estimate remains a point. All other nonnumeric values fail. Duplicate x fails. `order='ascending'` sorts by x; `order='input'` preserves explicit ordering. Both keep missing rows as run breaks.
* `lollipop`: fields `label`, `value`; values may be negative or zero. The stem starts at zero.
* `ternary`: fields `a`, `b`, `c` at the top, left, right vertices. Nonnegative components normalize by their positive total; zero totals and nonfinite values fail. `normalize=false` instead requires a total of one (tolerance 1e-9). Axis labels use the chosen field names. A point's accessible description contains its normalized components.

```pad
{chart:ribbon}[{"x":1,"value":3,"low":2,"high":4},{"x":2,"value":4,"low":3,"high":6}]{/chart:ribbon}
{chart:lollipop}[{"label":"Loss","value":-3},{"label":"Gain","value":5}]{/chart:lollipop}
{chart:ternary}[{"label":"Equal","a":1,"b":1,"c":1},{"label":"A","a":1,"b":0,"c":0}]{/chart:ternary}
```

`palette='viridis', usage='sequential', scheme='dark'` explicitly checks the palette's intended scale and named scheme. `usage` accepts categorical, sequential, diverging or cyclic. Categorical limits are checked against actual drawn color slots, in PHP and in the browser. Omit `usage` to retain the existing sampling of any palette; `scheme` does not recolor the palette. Category limits concern distinct color series/slots, not the number of observations.
