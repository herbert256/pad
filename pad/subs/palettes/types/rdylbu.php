<?php

  // rdylbu - ColorBrewer's diverging scale RdYlBu of eleven classes, from a dark red through
  // orange to a pale yellow middle and on through light blue to a dark indigo (Cynthia Brewer,
  // colorbrewer2.org), for values on both sides of a middle - hotter and colder, above and below:
  // the spread of a rainbow, but ColorBrewer marks it safe for colour-blind eyes at every number of
  // classes, where its Spectral and RdYlGn are at most possibly so, and only up to five. From
  // colorbrewer_schemes.js of ColorBrewer 2.0 (axismaps/colorbrewer), checked against
  // d3-scale-chromatic. Apache-2.0 licence: this product includes color specifications and designs
  // developed by Cynthia Brewer (http://colorbrewer.org/).

  return [ 'name'   => 'RdYlBu',
           'type'   => 'diverging',
           'colors' => [ '#a50026', '#d73027', '#f46d43', '#fdae61', '#fee090', '#ffffbf', '#e0f3f8', '#abd9e9',
                         '#74add1', '#4575b4', '#313695' ] ];

?>
