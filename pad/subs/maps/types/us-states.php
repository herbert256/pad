<?php

  // us-states - The fifty states of the United States and the District of Columbia, each
  // under its ISO 3166-2 code - US-CA, or CA alone, the postal code - or its name; Alaska and
  // Hawaii (its main islands, without the Northwestern Hawaiian Islands) drawn below the
  // lower 48 at the left as on every map of the states. The outlines are subs/maps/_lib/
  // us-states.json, from Natural Earth's 1:10m admin-1 states and provinces with the large
  // lakes cut out (public domain, naturalearthdata.com), in the Albers equal-area conic
  // projection of the United States with Alaska and Hawaii moved in - the composite of
  // d3's geoAlbersUsa. Puerto Rico, Guam, the Virgin Islands, American Samoa, the Northern
  // Mariana Islands and the minor outlying islands have their ISO 3166-2 codes too, but no
  // outline here: a value of theirs is named in the title as one the view does not show.

  return [ 'outlines' => 'us-states',
           'name'     => 'United States',
           'unit'     => 'state' ];

?>
