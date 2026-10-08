<?php

  // The example on show: its entry in _data/examples.json gives the header above it and the
  // files under it (_inits.pad, _exits.pad), the examples before and after it the links at
  // the foot. An htmx request for a part of the page gets no wrapper at all - the fragment
  // it asked for is the whole answer.

  $example     = htmxExample ( $padPage );
  $title       = $example ['title'] . ' - PAD + htmx';
  $serverChips = $example ['server'];
  $clientChips = $example ['client'];
  $prev        = $example ['prev'];
  $next        = $example ['next'];
  $sourceFiles = implode ( ', ', $example ['files'] );

?>
