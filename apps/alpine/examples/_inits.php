<?php

  // The example on show: its entry in _data/examples.json gives the header above it and the
  // files under it (_inits.pad, _exits.pad) - the template, the PHP, the page's script in
  // www/alpine/ under the page's own name and the files the entry adds - and the examples
  // before and after it the links at the foot.

  $example     = alpineExample ( $padPage );
  $title       = $example ['title'] . ' - PAD + Alpine';
  $serverChips = $example ['server'];
  $clientChips = $example ['client'];
  $prev        = $example ['prev'];
  $next        = $example ['next'];

  $sourceFiles = implode ( ', ', array_unique ( array_merge ( is_file ( "$alpineWww$padPage.js" ) ? [ "www:$padPage.js" ] : [],
                                                              $example ['files'] ) ) );

?>
