# be-provinces

Ten provinces plus Brussels-Capital (`BE-BRU`), which is a region and belongs to no province. Brussels is drawn as its own polygon, not combined with Flemish Brabant. Full ISO 3166-2 codes, short codes, English names and French/Dutch aliases join case-insensitively. Luxembourg here means the Belgian province. [Belgian federal government](https://www.belgium.be/en/about_belgium/government/provinces).

Use `{map:auto 'be-provinces', key='code', value='amount'}...{/map:auto}`. Missing values retain the standard empty fill.

Boundary source: [Natural Earth 1:10m admin-1 without large lakes](https://www.naturalearthdata.com/downloads/10m-cultural-vectors/10m-admin-1-states-provinces/), version 5.1.1, [public domain](https://www.naturalearthdata.com/about/terms-of-use/). Mapshaper 0.6.121 weighted Visvalingam simplification retains shared topology and shapes at 1200×1200 and 300×300; local Lambert azimuthal equal-area projections, integer coordinates. These are generalized reporting outlines, not cadastral boundaries. No region is replaced by a placeholder polygon.

Reproduce with Node and mapshaper 0.6.121:

```sh
node docs/subsystems/maps/build-outline.mjs docs/subsystems/maps/be-provinces.json pad/subs/maps/_lib/be-provinces.json /path/ne_10m_admin_1_states_provinces_lakes.shp
```

`MAPSHAPER_MODULE` can point to an installed mapshaper.js. Generator config records source, projection, aliases and simplification parameters. Regression validates joins, all 11 nonempty outlines, finite bounds and the 200 KB shape budget.
