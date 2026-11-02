<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  require PAD . 'lib/subsystem.php'; require PAD . 'lib/embed.php'; require PAD . 'subs/embeds/_lib/embed.php';
  $type = padEmbedProvider ( 'observable' );
  $parts = $type ['match'] ( 'https://observablehq.com/@d3/sortable-bar-chart?cell=chart&cell=viewof+order', TRUE );
  if ( ! $parts or padEmbedFind ( 'https://observablehq.com/@d3/sortable-bar-chart?cell=chart&cell=viewof+order' ) [0] !== 'observable' ) throw new Exception ( 'Auto detection' );
  $embed = $type ['embed'] ( $parts, [] );
  foreach ( ['https://observablehq.com/@user/private?key=secret', 'https://observablehq.com/@user/notebook?cell=%3Cscript%3E',
    'https://observablehq.com/@user/notebook?cells=chart,%3Cscript%3E', 'https://observablehq.com/@user/notebook?cells=chart,',
    'https://observablehq.com/@user/notebook?cells=' . implode ( ',', array_fill ( 0, 21, 'chart' ) ),
    'https://observablehq.com/@user/notebook?cells=chart&api_key=secret', 'https://observablehq.com.evil.test/@user/notebook',
    'https://observablehq.com/cbbe8ab362fb60e2' ] as $url ) if ( $type ['match'] ( $url, TRUE ) !== NULL ) throw new Exception ( 'Invalid URL accepted: ' . $url );
  if ( ! str_contains ( $embed ['src'], '?cells=chart%2Cviewof%20order' ) ) throw new Exception ( 'Repeated cell selection' );
  $plural = $type ['match'] ( 'https://observablehq.com/embed/@d3/sortable-bar-chart?cells=chart%2Cviewof+order', TRUE );
  if ( $type ['embed'] ( $plural, [] ) !== $embed ) throw new Exception ( 'Plural cell selection differs' );
  foreach ( [ 'https://observablehq.com/d/', 'https://observablehq.com/embed/', 'https://old.observablehq.com/embed/' ] as $base ) {
    foreach ( [ '', '@123' ] as $version ) {
      $id = 'cbbe8ab362fb60e2' . $version;
      $match = $type ['match'] ( $base . $id . '?cells=chart,viewof+order', TRUE );
      if ( ! $match or padEmbedFind ( $base . $id ) [0] !== 'observable' ) throw new Exception ( 'ID URL not recognized' );
      $frame = $type ['embed'] ( $match, [] );
      if ( $frame ['src'] !== 'https://observablehq.com/embed/' . $id . '?cells=chart%2Cviewof%20order'
        or $frame ['link'] !== 'https://observablehq.com/d/' . $id ) throw new Exception ( 'ID embed/fallback route' );
    }
  }
  $override = $type ['embed'] ( $plural, [ 'cell' => 'mutable value,chart,chart' ] );
  if ( ! str_ends_with ( $override ['src'], '?cells=mutable%20value%2Cchart' ) ) throw new Exception ( 'Override or duplicate removal' );
  if ( ! str_ends_with ( $type ['embed'] ( $plural, [ 'cell' => 'chart' ] ) ['src'], '?cell=chart' ) ) throw new Exception ( 'Single cell selection' );
  if ( $type ['embed'] ( $plural, [ 'cell' => 'chart,<script>' ] ) ['src'] !== '' ) throw new Exception ( 'Invalid override accepted' );
  if ( $type ['embed'] ( $parts, [ 'height' => 99999 ] ) ['src'] !== '' ) throw new Exception ( 'Dimension bound' );
  echo "observable: named/ID/pinned routes, plural/repeated/overridden cells, URL rejection and viewer construction passed\n";
?>
