<?php

  // The engine's own four as formats: JSON, YAML and CSV written by padFormatWrite are read
  // back by padFormatRead as the rows they were - YAML quoting the no that would be a boolean
  // in YAML 1.1, CSV quoting a comma and making a formula text - and XML reads only.

  $engineRows = [ [ 'name' => 'Ann', 'country' => 'no', 'note' => 'a, b' ], [ 'name' => 'Bob', 'country' => 'nl', 'note' => '=1+1' ] ];

  $engineOut = [];

  foreach ( [ 'json', 'yaml', 'csv' ] as $engineKind ) {
    $engineText  = padFormatWrite ( $engineKind, $engineRows );
    $engineOut [] = "$engineKind: " . str_replace ( "\n", ' | ', trim ( $engineText ) ) . ' => ' . json_encode ( padFormatRead ( $engineKind, $engineText ) );
  }

  $engineOut = implode ( ' / ', $engineOut ) . ' / xml: ' . ( padFormatFind ( 'xml', 'write' ) ?: 'reads only' );

?>
