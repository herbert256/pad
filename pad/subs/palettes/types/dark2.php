<?php

  // dark2 - ColorBrewer's qualitative Dark2, eight deep colours to tell categories apart - teal
  // green, orange, slate violet, magenta, green, mustard, brown and grey (Cynthia Brewer,
  // colorbrewer2.org): darker and more saturated than its pastels, so thin lines and small marks
  // stay visible on a light surface; ColorBrewer marks its first three safe for colour-blind eyes,
  // four to six possibly, more not. From colorbrewer_schemes.js of ColorBrewer 2.0
  // (axismaps/colorbrewer), checked against d3-scale-chromatic. Apache-2.0 licence: this product
  // includes color specifications and designs developed by Cynthia Brewer (http://colorbrewer.org/).

  return [ 'name'   => 'Dark2',
           'type'   => 'categorical',
           'colors' => [ '#1b9e77', '#d95f02', '#7570b3', '#e7298a', '#66a61e', '#e6ab02', '#a6761d', '#666666' ] ];

?>
