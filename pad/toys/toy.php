<?php

  // The body of a toy, whichever way it is written: {toy 'life', pattern='glider'/}, the toy:
  // type - {toy:life pattern='glider'/}, or a bare {life pattern='glider'/} when nothing else
  // has the name. The caller sets $padToyKind; a toy is a file of toys/types/ - its script and
  // its style are toys/assets/<toy>.js and .css - and the files there are all the toys there
  // are (padToyTypes, lib/toy.php).
  //
  // A toy runs in the browser: the server checks the options it is given and writes the
  // frame, the toy's script animates it - once per page, with the CSP nonce, as the
  // interactive tags write theirs (lib/widget.php). The pattern of a Game of Life, the rules
  // of an L-system stand between its tags as they are written - taken before the level walks
  // them, so they may hold braces.

  include_once PAD . 'toys/_lib/toy.php';

  if ( ! padToyType ( $padToyKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no toy named '" . padMakeSafe ( $padToyKind, 20 ) . "' - " . implode ( ', ', padToyTypes () ) );
    return '';
  }

  // The toy shown goes on the xref record, which the reference application shows: the pages
  // that show each toy.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'toy', $padToyKind );

  $padToySource = $padContent;
  $padContent   = '';

  // The toy's markup is a value: a level loops over the data a data= option named, and would
  // write the toy once per row - so the level gets its one default occurrence back.

  $padToyHtml = padToyTag ( $padToyKind, $padToySource );

  $padData [$pad] = padDefaultData ();

  return ( $padToyHtml === '' ) ? FALSE : $padToyHtml;

?>
