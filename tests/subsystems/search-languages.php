<?php
  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  function padDirList ( $p ) { return scandir ( $p ); }
  function padUnprotect ( $v ) { return $v; }
  function padArrGet ( $row, $key, $default ) { return $row [$key] ?? $default; }
  require PAD . 'lib/subsystem.php';
  require PAD . 'subs/search/_lib/search.php';
  function check ( $v ) { if ( ! $v ) throw new Exception ( 'search integration fixture failed' ); }
  foreach ( [ 'fi' => ['talossa ja talot','tal'], 'pl' => ['kotami i koty','kot'], 'tr' => ['KİTAPLAR ve kitaplar','kit'] ] as $code => [ $text, $prefix ] ) {
    $language = padSearchLanguage ( $code );
    $index = padSearchIndex ( [ ['body'=>$text] ], [ ['body',1] ], $language );
    $index ['mem'] = padSearchParts ( $index ); $index ['shards'] = count ( $index ['mem']['w'] );
    check ( count ( padSearchExpand ( $prefix, $index ) ) > 0 );
    $query = padSearchQuery ( '"' . $text . '"', $language, $index );
    $phrase = array_values ( $query ['phrases'] )[0];
    check ( count ( $phrase ) === 2 and $phrase [1][1] === 2 );
    check ( array_intersect ( $phrase [0][0], $phrase [1][0] ) !== [] );
  }
  $tr = padSearchLanguage ( 'tr' );
  check ( padSearchWords ( 'I İ ISPARTA İSTANBUL', $tr ) === ['ı','i','ısparta','istanbul'] );
  check ( padSearchWords ( 'I İ', padSearchLanguage ( 'en' ) ) !== ['ı','i'] );
  check ( padSearchTerms ( 'kitaplar', $tr ) === padSearchTerms ( 'kitap', $tr ) );
  echo "search integration: language case folding, stemming, stopword positions and prefixes passed\n";
?>
