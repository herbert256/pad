<?php

  // puor - ColorBrewer's diverging scale PuOr of eleven classes, from a dark brown through orange
  // to a light grey middle and on through lilac to a dark purple (Cynthia Brewer,
  // colorbrewer2.org), in ColorBrewer's order, its orange end first: orange against purple, two
  // hues red-green colour-blind eyes still tell apart, and ColorBrewer marks it safe for them. From
  // colorbrewer_schemes.js of ColorBrewer 2.0 (axismaps/colorbrewer), checked against
  // d3-scale-chromatic, which lists it from its purple end. Apache-2.0 licence: this product
  // includes color specifications and designs developed by Cynthia Brewer (http://colorbrewer.org/).

  return [ 'name'   => 'PuOr',
           'type'   => 'diverging',
           'colors' => [ '#7f3b08', '#b35806', '#e08214', '#fdb863', '#fee0b6', '#f7f7f7', '#d8daeb', '#b2abd2',
                         '#8073ac', '#542788', '#2d004b' ] ];

?>
