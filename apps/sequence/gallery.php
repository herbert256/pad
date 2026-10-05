<?php

  // The gallery: every sequence type that needs no parameter, its first 24 terms drawn as a
  // {sparkline}, the first eight written out, and the OEIS entry the terms come from - a
  // link to oeis.org, found in the table the oeis type reads (seqFunOeis in _lib/fun.php).

  $drawn = [];

  foreach ( seqFunTypes () as $type ) {

    $terms = seqFunTerms ( $type, 24 );
    $oeis  = seqFunOeis ( $terms );

    $drawn [] = [
      'type'  => $type,
      'first' => implode ( ', ', array_slice ( $terms, 0, 8 ) ),
      'oeis'  => $oeis ? sprintf ( 'A%06d', $oeis ) : ''
    ];

  }

?>
