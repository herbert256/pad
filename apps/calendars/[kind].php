<?php

  if ( ! is_string ( $kind ) || ! isset ( calendarsCatalog () [$kind] ) )
    padAbort ( 404 );

  $title = calendarsCatalog () [$kind] ['name'];
  $calendarAbout = calendarsCatalog () [$kind] ['about'];
  $calendarSource = is_file ( APP . "_examples/$kind.pad" ) ? file_get_contents ( APP . "_examples/$kind.pad" ) : "{calendar:$kind/}";
  $calendarCode = padHighlightTokens ( $calendarSource, 'pad' );
  include_once COMMON . '_lib/playground.php';
  $calendarPlay = padPlayLink ( $calendarSource );

?>
