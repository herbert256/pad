# IT region outlines

Italy dissolves provincial outlines by region_cod into its twenty regions, retaining Sicily and Sardinia in their geographic positions. Current regional identifiers are independent of historical provincial splits in the source.

Source: [Natural Earth 1:10m admin-1 states/provinces, lakes excluded, version 5.1.1](https://www.naturalearthdata.com/downloads/10m-cultural-vectors/10m-admin-1-states-provinces/), [public domain](https://www.naturalearthdata.com/about/terms-of-use/). The versioned JSON configuration records grouping, aliases and projection. Mapshaper 0.6.121 dissolves boundaries, projects LAEA, and simplifies topology at 1200x1200 and 300x300 with keep-shapes. Checked-in SVG paths use integer coordinates. These are generalized visualization boundaries, not cadastral/geodetic data.

Reproduce with `MAPSHAPER_MODULE=/path/to/mapshaper.js node docs/subsystems/maps/build-outline.mjs docs/subsystems/maps/it-regions.json pad/subs/maps/_lib/it-regions.json /path/to/ne_10m_admin_1_states_provinces_lakes.shp`.

`php tests/subsystems/map-it-regions.php` checks all 20 identifiers/aliases, both geometry resolutions, actual non-placeholder outlines and bounds. Every region has an accessible title; unprovided values retain the map's no-value styling. The existing coordinate-marker/bubble restriction on preprojected regional outlines remains in force; geographic operations return longitude/latitude and cannot be overlaid directly on these views.
