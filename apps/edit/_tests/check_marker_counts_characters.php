<?php

  // The marker on text not saved yet stands on the tag the error names, counted in
  // characters as the editor counts its columns - and as the engine counts its own: the
  // column was the tag's byte offset in its line, so every é before it put the marker one
  // column further right, under typo}</ instead of {$typo}.

  $at = function ( $text ) {
    $marker = editCheckPad ( 'hello', 'index.pad', $text ) ['markers'] [0] ?? [];
    return ( $marker ['line'] ?? 0 ) . ':' . ( $marker ['column'] ?? 0 ) . '-' . ( $marker ['endColumn'] ?? 0 );
  };

  $plain  = $at ( "<p>Een {\$typo}</p>\n" );
  $accent = $at ( "<p>Één {\$typo}</p>\n" );
  $second = $at ( "<p>{\$message}</p>\n<p>Één naïef {\$typo}</p>\n" );

?>
