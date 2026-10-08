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

  // What {source} shows under the example besides its template and PHP: the component the
  // browser runs - www/react/ under the page's own name - and the files the entry adds.

  $sourceFiles = implode ( ', ', array_merge ( is_file ( dirname ( APPS ) . "/www/$padApp/$padPage.js" ) ? [ "www:$padPage.js" ] : [],
                                             $example ['files'] ) );

?>
