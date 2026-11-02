# ES region outlines

Spain includes 17 autonomous communities and the autonomous cities Ceuta and Melilla. Canary Islands use a separate labeled LAEA inset centered at 28.3 N, 15.5 W; Balearic Islands and both autonomous cities remain at their geographic positions. Natural Earth region_cod uses several province-style values; configuration explicitly maps ES.NA to ES-NC, ES.MU to ES-MC, ES.LO to ES-RI, ES.PM to ES-IB, and corrects Melilla's duplicated ES.CE grouping to ES-ML.

Source: [Natural Earth 1:10m admin-1 states/provinces, lakes excluded, version 5.1.1](https://www.naturalearthdata.com/downloads/10m-cultural-vectors/10m-admin-1-states-provinces/), [public domain](https://www.naturalearthdata.com/about/terms-of-use/). The versioned JSON configuration records grouping, aliases and projection. Mapshaper 0.6.121 dissolves boundaries, projects LAEA, and simplifies topology at 1200x1200 and 300x300 with keep-shapes. Checked-in SVG paths use integer coordinates. These are generalized visualization boundaries, not cadastral/geodetic data.

Reproduce with `MAPSHAPER_MODULE=/path/to/mapshaper.js node docs/subsystems/maps/build-insets.mjs docs/subsystems/maps/es-regions.json pad/subs/maps/_lib/es-regions.json /path/to/ne_10m_admin_1_states_provinces_lakes.shp`.

`php tests/subsystems/map-es-regions.php` checks all 19 identifiers/aliases, both geometry resolutions, actual non-placeholder outlines and bounds. Every region has an accessible title; unprovided values retain the map's no-value styling. The existing coordinate-marker/bubble restriction on preprojected regional outlines remains in force; geographic operations return longitude/latitude and cannot be overlaid directly on these views.
