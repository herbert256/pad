<?php

  // The example on show: its entry in _data/examples.json gives the header above it and the
  // files under it (_inits.pad, _exits.pad) - the template, the PHP and the sources of its
  // islands in _frontend/src/ - and the examples before and after it the links at the foot.

  $example     = islandsExample ( $padPage );
  $title       = $example ['title'] . ' - PAD + islands';
  $serverChips = $example ['server'];
  $clientChips = $example ['client'];
  $prev        = $example ['prev'];
  $next        = $example ['next'];
  $sourceFiles = implode ( ', ', $example ['files'] );

?>
