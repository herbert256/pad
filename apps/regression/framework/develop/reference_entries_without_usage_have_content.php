<?php

  include_once COMMON . '_lib/reference.php';
  include_once APPS . 'reference/_lib/entry.php';

  // Every catalogue item must have real content even before a harvest. Checking all of
  // them also covers SVG icons whose usual gallery happens to provide a usage record now.
  $ok = TRUE;
  $sections = json_decode ( file_get_contents ( APPS . 'reference/_data/reference.json' ), TRUE );

  foreach ( $sections as $section )
    foreach ( getReference ( $section ['dir'], $section ['xref'] ) as $row ) {
      $entry = referenceEntry ( $section ['xref'], $row ['item'] );
      $ok = $ok && ! empty ( $entry ['files'] );
      foreach ( $entry ['files'] ?? [] as $source ) $ok = $ok && trim ( $source ) !== '';
    }

  // Looking up descriptions must not turn this into an arbitrary source-file reader.
  foreach ( [ [ [], 'length' ], [ 'unit', [] ], [ '../pad', 'length' ],
              [ 'unit', '../../config/config' ], [ 'unit', '.hidden' ], [ 'unit', 'no-such-kind' ] ] as [ $xref, $item ] )
    $ok = $ok && referenceEntry ( $xref, $item ) === [];

  foreach ( [ [ 'unit', 'length', 'padConvert' ], [ 'tag/common', 'showcaseExampleStart', 'showcaseExampleEnd' ],
              [ 'config/info', 'trace', 'padInfoTrace' ], [ 'config/outputType', 'format', 'padOutputFormat' ] ] as [ $xref, $item, $text ] ) {
    $response = padCurl ( $padHost . 'reference/?entry&' . http_build_query ( compact ( 'xref', 'item' ) ) . '&padInclude' );
    $ok = $ok && $response ['result'] === 200 && str_contains ( $response ['data'], $text );
  }

  $answer = $ok ? 'Every unharvested reference has content; helpers, wrappers and configuration render; unknown names are refused.' : 'FAIL';

?>
