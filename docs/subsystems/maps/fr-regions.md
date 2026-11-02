# fr-regions

Thirteen metropolitan regions (including Corsica) and five overseas regions. The bottom row has separate labeled insets for Guadeloupe, Martinique, Guyane, La Réunion and Mayotte. Insets use different scales; their areas must not be compared with each other or the mainland. Other overseas collectivities are outside this view. Natural Earth departments are dissolved on trimmed `region_cod`; ISO region codes (`FR-IDF`, etc.), short codes, names and INSEE region-number strings (`11`, `01`, `06`) join. [Official region API/code reference](https://geo.api.gouv.fr/decoupage-administratif/regions).

Use `{map:auto 'fr-regions', key='code', value='amount'}...{/map:auto}`. Missing values retain the standard empty fill.

Boundary source: [Natural Earth 1:10m admin-1 without large lakes](https://www.naturalearthdata.com/downloads/10m-cultural-vectors/10m-admin-1-states-provinces/), version 5.1.1, [public domain](https://www.naturalearthdata.com/about/terms-of-use/). Mapshaper 0.6.121 weighted Visvalingam simplification retains shared topology and shapes at 1200×1200 and 300×300; local Lambert azimuthal equal-area projections, integer coordinates. These are generalized reporting outlines, not cadastral boundaries. No region is replaced by a placeholder polygon.

Reproduce with Node and mapshaper 0.6.121:

```sh
node docs/subsystems/maps/build-fr-regions.mjs docs/subsystems/maps/fr-regions.json pad/subs/maps/_lib/fr-regions.json /path/ne_10m_admin_1_states_provinces_lakes.shp
```

`MAPSHAPER_MODULE` can point to an installed mapshaper.js. Generator config records source, projection, aliases and simplification parameters. Regression validates joins, all 18 nonempty outlines, finite bounds and the 200 KB shape budget.
