<?php

  // One example, three panels: the .php and .pad sources coloured by the _common colours
  // library, the .html as what it is - the example's rendered output. The name is taken
  // from the query, so it is held to the shape a harvest writes and to the store itself.

  $exDir = DATA . 'examples/';

  // The query travels along on every link, so "back to the search" lands on the results
  // the visitor came from - the index runs the same search for a GET as for the form.

  $q     = padMakeSafe ( $q ?? '', 200 );

  $item  = padMakeSafe ( $item ?? '', 200 );
  $show  = '';

  if ( $item and preg_match ( '#^[a-zA-Z0-9_/-]+$#', $item ) ) {

    $exHtml = realpath ( $exDir . "$item.html" );

    if ( $exHtml and str_starts_with ( $exHtml, realpath ( $exDir ) . '/' ) )
      $show = $item;

  }

  $hasPhp = ( $show and file_exists ( $exDir . "$show.php" ) ) ? 1 : 0;
  $hasPad = ( $show and file_exists ( $exDir . "$show.pad" ) ) ? 1 : 0;

  $srcPhp = ( $hasPhp ) ? padColorsFile ( $exDir . "$show.php" ) : '';
  $srcPad = ( $hasPad ) ? padColorsFile ( $exDir . "$show.pad" ) : '';

  // The stored render is output as-is, so its braces are escaped the way the colours
  // library escapes its own - the exit unescapes them, the level loop never sees them.

  $result = ( $show ) ? padEscape ( padFileGet ( $exDir . "$show.html" ) ) : '';

  // The example's name is the title, exactly as it is - the common header would space its
  // underscores and capitalise it, so the page writes its own.

  if ( $show )
    $skipTitle = 1;

  $title  = ( $show ) ? $show : 'Examples';

?>