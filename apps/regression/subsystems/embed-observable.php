<?php
  define ( 'PAD', dirname ( __DIR__, 3 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/embed.php'; require PAD . 'subs/embeds/_lib/embed.php';
  $type = padEmbedProvider ( 'observable' );
  $parts = $type ['match'] ( 'https://observablehq.com/@d3/sortable-bar-chart?cell=chart&cell=viewof+order', TRUE );
  if ( ! $parts or padEmbedFind ( 'https://observablehq.com/@d3/sortable-bar-chart?cell=chart&cell=viewof+order' ) [0] !== 'observable' ) throw new Exception ( 'Auto detection' );
  $embed = $type ['embed'] ( $parts, [] );
  foreach ( ['https://observablehq.com/@user/private?key=secret', 'https://observablehq.com/@user/notebook?cell=%3Cscript%3E', 'https://observablehq.com.evil.test/@user/notebook'] as $url ) if ( $type ['match'] ( $url, TRUE ) !== NULL ) throw new Exception ( 'Invalid URL accepted' );
  if ( ! str_contains ( $embed ['src'], 'cell=chart&cell=viewof%20order' ) ) throw new Exception ( 'Cell selection' );
  if ( $type ['embed'] ( $parts, [ 'height' => 99999 ] ) ['src'] !== '' ) throw new Exception ( 'Dimension bound' );
  echo "observable: URL selection/rejection, auto detection and viewer construction passed\n";
?>
