# ca-provinces

All ten provinces and three territories, including Nunavut and Arctic islands, in one equal-area northern view. Full ISO codes, Canada Post short codes and English/French names are accepted. `QC`/`Québec`, `NL`/`Terre-Neuve-et-Labrador` and `NT`/`Territoires du Nord-Ouest` are explicit aliases. [Canadian official code table](https://www150.statcan.gc.ca/n1/pub/46-26-0002/462600022022002-eng.htm).

Use `{pad:map 'ca-provinces', key='code', value='amount'}...{/pad:map}`. Missing values retain the standard empty fill.

Boundary source: [Natural Earth 1:10m admin-1 without large lakes](https://www.naturalearthdata.com/downloads/10m-cultural-vectors/10m-admin-1-states-provinces/), version 5.1.1, [public domain](https://www.naturalearthdata.com/about/terms-of-use/). Mapshaper 0.6.121 weighted Visvalingam simplification retains shared topology and shapes at 1200×1200 and 300×300; local Lambert azimuthal equal-area projections, integer coordinates. These are generalized reporting outlines, not cadastral boundaries. No region is replaced by a placeholder polygon.

Reproduce with Node and mapshaper 0.6.121:

```sh
node docs/subsystems/maps/build-outline.mjs docs/subsystems/maps/ca-provinces.json pad/subs/maps/_lib/ca-provinces.json /path/ne_10m_admin_1_states_provinces_lakes.shp
```

`MAPSHAPER_MODULE` can point to an installed mapshaper.js. Generator config records source, projection, aliases and simplification parameters. Regression validates joins, all 13 nonempty outlines, finite bounds and the 200 KB shape budget.
