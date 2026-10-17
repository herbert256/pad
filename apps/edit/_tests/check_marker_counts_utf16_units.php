<?php

  // The check's marker counts its columns in UTF-16 units, as Monaco counts them - a
  // JavaScript string's offsets: an emoji, outside the Basic Multilingual Plane, is two of
  // them. Counted in characters the marker stood one column to the left for every emoji
  // before the tag, from the space before {$typo} to before its }. Both ways a marker is
  // placed: on text not saved yet, by the tag the error names, and by the engine's source
  // map, whose column counts characters and points at the name the error is about - the $
  // of {$typo}, column 10 here, as the engine gives it; the marker covers the name.

  $at = function ( $marker ) {
    return ( $marker ['line'] ?? 0 ) . ':' . ( $marker ['column'] ?? 0 ) . '-' . ( $marker ['endColumn'] ?? 0 );
  };

  $text    = "<p>\u{1F600} {\$typo}</p>\n";
  $unsaved = $at ( editCheckPad ( 'hello', 'index.pad', $text ) ['markers'] [0] ?? [] );

  $file   = APPS . 'hello/index.pad';
  $mapped = $at ( editPadMarker ( 'hello', $file, "<p>\u{1F600}\u{1F600} é {\$typo}</p>\n",
                                  [ 'error' => "PAD: there is no field named '\$typo'",
                                    'template' => [ 'path' => $file, 'line' => 1, 'column' => 10, 'tag' => '{$typo}' ] ],
                                  FALSE, 'index' ) );

  $line = $at ( editMarker ( "\u{1F600}\u{1F600}ab", 1, 0, 0, 'the whole line', 'pad' ) );

?>
