<?php

  // at-states - Austria: 9 states with stable ISO 3166-2 joins, English and source-language aliases. No insets; islands and disconnected pieces stay in geographic positions. Lambert azimuthal equal-area parameters are in `at-states.json`; these preprojected outlines deliberately reject longitude/latitude marker overlays.
  //
  // Source: [Natural Earth 1:10m admin-1 states/provinces, lakes excluded, 5.1.1](https://www.naturalearthdata.com/downloads/10m-cultural-vectors/10m-admin-1-states-provinces/), [public domain](https://www.naturalearthdata.com/about/terms-of-use/). Source archive SHA-256: `7c41f8aa2827c6cfbd458faff584119bb3a628047f0c4b45cff5fa071cc8a11b`. This is generalized visualization geometry, not cadastral data. Names/count checked against the [government list](https://www.bmi.gv.at/412_english/); independent ID and relative-location fixtures are in the focused test.
  //
  // Reproduce with Mapshaper 0.6.121: `MAPSHAPER_MODULE=/path/to/mapshaper.js node docs/subsystems/maps/build-outline.mjs docs/subsystems/maps/at-states.json pad/subs/maps/_lib/at-states.json /path/to/ne_10m_admin_1_states_provinces_lakes.shp`. Shared topology is simplified at 1200 and 300 unit resolution, then rounded to integer SVG units; both levels are checked for real outlines and bounds.
  //
  // `{map:at-states/}` draws labelled regions; ordinary map data/scale/palette options provide choropleths.

return ['outlines'=>'at-states','name'=>'Austria','unit'=>'state'];
