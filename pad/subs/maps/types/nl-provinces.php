<?php

  // nl-provinces - The twelve provinces of the Netherlands, each under its ISO 3166-2 code -
  // NL-NH, or NH alone - or its name, in English or Dutch (North Holland, Noord-Holland,
  // Friesland, Fryslân); Belgium and Germany are the land around them. The outlines are
  // subs/maps/_lib/nl-provinces.json, from Natural Earth's 1:10m admin-1 states and provinces
  // with the large lakes cut out - the IJsselmeer and the Markermeer are water (public
  // domain, naturalearthdata.com), in a Lambert azimuthal equal-area projection centred on
  // the country. Bonaire, Saba and Sint Eustatius, the special municipalities of the
  // Caribbean Netherlands, have their ISO 3166-2 codes too (NL-BQ1, NL-BQ2, NL-BQ3), but no
  // outline here: a value of theirs is named in the title as one the view does not show.

  return [ 'outlines' => 'nl-provinces',
           'name'     => 'Netherlands',
           'unit'     => 'province' ];

?>
