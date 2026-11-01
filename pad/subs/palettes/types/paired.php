<?php

  // paired - ColorBrewer's qualitative Paired, twelve colours in six pairs, a light and a dark of
  // one hue each - blue, green, red, orange, purple, and a pale yellow with a brown (Cynthia Brewer,
  // colorbrewer2.org): for categories that come in pairs - last year and this, planned and done -
  // the pair's hue telling the group and its lightness the member; ColorBrewer marks it safe for
  // colour-blind eyes up to four colours, possibly to eight, more not. From colorbrewer_schemes.js
  // of ColorBrewer 2.0 (axismaps/colorbrewer), checked against d3-scale-chromatic. Apache-2.0
  // licence: this product includes color specifications and designs developed by Cynthia Brewer
  // (http://colorbrewer.org/).

  return [ 'name'   => 'Paired',
           'type'   => 'categorical',
           'colors' => [ '#a6cee3', '#1f78b4', '#b2df8a', '#33a02c', '#fb9a99', '#e31a1c', '#fdbf6f', '#ff7f00',
                         '#cab2d6', '#6a3d9a', '#ffff99', '#b15928' ] ];

?>
