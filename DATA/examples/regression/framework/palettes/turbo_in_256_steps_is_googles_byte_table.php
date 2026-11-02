<?php

  // turbo sampled in 256 steps answers Google's own 8-bit table, turbo_srgb_bytes of
  // turbo_colormap.c, entry for entry: its first, middle and last colour and an md5 of all of
  // them, made from that table apart from PAD.

  $colors = padPalette ( 'turbo', 256 );
  $first  = $colors [0];
  $mid    = $colors [128];
  $last   = $colors [255];
  $md5    = md5 ( implode ( ' ', $colors ) );

?>
