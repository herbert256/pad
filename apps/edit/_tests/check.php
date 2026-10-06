<?php

  // The checks behind the markers: a PHP syntax error on its line; a saved page whose pair
  // never closes, placed by PAD's source map; and text not saved yet, placed on the field
  // the error names.

  $php    = editCheckPhp ( "<?php\n\n  \$a = ;\n\n?>" );
  $phpOk  = count ( editCheckPhp ( "<?php\n\n  \$a = 1;\n\n?>" ) );

  $saved  = editCheckPad ( 'regression/errors', 'syntax/a_case_never_closes.pad' );
  $typed  = editCheckPad ( 'hello', 'index.pad', "<p>{\$message}</p>\n<p>{\$nope}</p>\n" );
  $clean  = editCheckPad ( 'hello', 'index.pad', "<p>{\$message}</p>\n" );
  $none   = editCheckPad ( 'demo', '_include/todo.pad' );

  $phpLine   = $php [0] ['line'] ?? 0;
  $savedAt   = ( $saved ['markers'] [0] ['line'] ?? 0 ) . ':' . ( $saved ['markers'] [0] ['column'] ?? 0 );
  $savedText = $saved ['markers'] [0] ['message'] ?? '';
  $typedAt   = ( $typed ['markers'] [0] ['line'] ?? 0 ) . ':' . ( $typed ['markers'] [0] ['column'] ?? 0 );
  $typedText = $typed ['markers'] [0] ['message'] ?? '';
  $cleanN    = count ( $clean ['markers'] );
  $noneSaid  = $none ['checked'] ? 'checked' : 'not checked';

  $pages = implode ( ' ', array_map ( fn ( $r ) => $r . '=' . ( editPageFor ( 'demo', $r ) ?? '-' ),
                       [ 'todo.pad', '_inits.pad', '_include/todo.pad', 'todo.php', 'sub/page.html', 'products/[id].pad' ] ) );

?>
