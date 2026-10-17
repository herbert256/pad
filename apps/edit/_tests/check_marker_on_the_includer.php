<?php

  // An error the engine places in another file than the page - the snippet {lost} brings in
  // - is marked on the page's own {lost} that included it (line 4), the first of the
  // engine's includers that is this page, and the message names the snippet's file and
  // line; text not saved yet has no includers the engine can point into, and the marker
  // goes on line 1. The text search found the snippet's {$lost} where the page held the same
  // text: on line 2, in the {if} branch that never runs. The render is the engine's own,
  // of the language server's fixture page.

  $at = function ( $marker ) {
    return ( $marker ['line'] ?? 0 ) . ':' . ( $marker ['column'] ?? 0 ) . '-' . ( $marker ['endColumn'] ?? 0 );
  };

  $fixture = editHome () . '/editors/fixture/apps';
  $file    = "$fixture/shop/included.pad";
  $text    = file_get_contents ( $file );

  [ $code, $out ] = editRun ( [ editPhpBinary (), editHome () . '/editors/render.php', 'shop', 'included', "--apps=$fixture" ] );

  $json    = json_decode ( $out, TRUE );
  $saved   = editPadMarker ( 'shop', $file, $text, $json, FALSE, 'included' );
  $unsaved = editPadMarker ( 'shop', $file, $text, $json, TRUE, 'included' );

  $savedAt   = $at ( $saved );
  $savedSaid = $saved ['message'] ?? '';
  $unsavedAt = $at ( $unsaved );

?>
