<?php

  // The example on show: its entry in _data/examples.json gives the header above it and the
  // files under it (_inits.pad, _exits.pad), the examples before and after it in the list
  // the links at the foot.

  $example     = reactExample ( $padPage );
  $title       = $example ['title'] . ' - PAD + React';
  $serverChips = $example ['server'];
  $clientChips = $example ['client'];
  $prev        = $example ['prev'];
  $next        = $example ['next'];

?>
