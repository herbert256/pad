<?php

  // ylgnbu - ColorBrewer's multi-hue sequential scale YlGnBu of nine classes, from a pale yellow
  // through green and teal to blue and a dark navy (Cynthia Brewer, colorbrewer2.org): its hue turns
  // as its lightness falls, so the steps of a map or a heatmap tell apart better than in one hue,
  // and ColorBrewer marks it safe for colour-blind eyes. From colorbrewer_schemes.js of ColorBrewer
  // 2.0 (axismaps/colorbrewer), checked against d3-scale-chromatic. Apache-2.0 licence: this product
  // includes color specifications and designs developed by Cynthia Brewer (http://colorbrewer.org/).

  return [ 'name'   => 'YlGnBu',
           'type'   => 'sequential',
           'colors' => [ '#ffffd9', '#edf8b1', '#c7e9b4', '#7fcdbb', '#41b6c4', '#1d91c0', '#225ea8', '#253494',
                         '#081d58' ] ];

?>
