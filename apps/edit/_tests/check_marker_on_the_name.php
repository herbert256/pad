<?php

  // The marker on a saved page covers the name the engine points at - the field, the pipe
  // function - where it ran from there for the whole tag's length: over $typo}< and over
  // uppr}</p> to the end of the line. On line 1 of a page saved with a byte order mark it
  // starts on the $, where it started one column to the right: the engine counts the mark,
  // the editor's model holds none. Each page is rendered by the engine itself, in an
  // application of its own under DATA/.

  $base = DATA . 'edit-test-' . getmypid () . '/';

  editRemoveTree ( $base );

  editWrite ( $base . 'names/plain.pad', "<p>{\$typo}</p>\n" );
  editWrite ( $base . 'names/pipe.pad',  "<p>{echo 1 | uppr}</p>\n" );
  editWrite ( $base . 'names/index.pad', "\xEF\xBB\xBF<p>{\$typo}</p>\n" );

  $at = function ( $page ) use ( $base ) {
    $file = $base . "names/$page.pad";
    [ $code, $out ] = editRun ( [ editPhpBinary (), editHome () . '/editors/render.php', 'names', $page, "--apps=$base" ] );
    $json = json_decode ( $out, TRUE );
    if ( ! is_array ( $json ) or ! isset ( $json ['error'] ) )
      return "$page: no error reported";
    $marker = editPadMarker ( 'names', $file, file_get_contents ( $file ), $json, FALSE, $page );
    return ( $json ['template'] ['column'] ?? 0 ) . ' -> ' . $marker ['line'] . ':' . $marker ['column'] . '-' . $marker ['endColumn'];
  };

  $plain = $at ( 'plain' );
  $pipe  = $at ( 'pipe' );
  $bom   = $at ( 'index' );

  editRemoveTree ( $base );

?>
