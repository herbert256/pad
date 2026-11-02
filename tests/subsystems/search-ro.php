<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  $language = include PAD . 'subs/search/types/ro.php';
  $words = gzfile ( __DIR__ . '/fixtures/search-ro/voc.txt.gz' );
  $expected = gzfile ( __DIR__ . '/fixtures/search-ro/output.txt.gz' );
  if ( count ( $words ) !== count ( $expected ) ) throw new Exception ( 'fixture count differs' );
  foreach ( $words as $i => $word ) {
    $word = rtrim ( $word, "\r\n" ); $want = rtrim ( $expected [$i], "\r\n" );
    $actual = $language ['stem'] ( $word );
    if ( $actual !== $want ) throw new Exception ( "Mismatch line " . ( $i + 1 ) . ": $word => $actual; expected $want" );
  }
  echo "search:ro: " . count ( $words ) . " upstream vocabulary fixtures passed\n";
?>
