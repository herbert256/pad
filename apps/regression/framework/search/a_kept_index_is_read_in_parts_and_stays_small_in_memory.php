<?php

  // 1500 rows of 200 words - 300,000 - searched, the index made in the first run and read
  // back in every later one: the memory the search takes above the rows stays small, the
  // index kept as one string per term in buckets and a request reading the buckets of its
  // query's terms only. Kept as nested arrays and read whole, it took some 185 bytes a
  // word - 145 MB here, past PHP's memory_limit of 128M on every request.

  $syllables = [ 'ka', 'lo', 'mi', 'ter', 'san', 'dor', 'vel', 'pra', 'nu', 'qui', 'zen', 'bar', 'fo', 'lit', 'ron', 'ge' ];
  $seed      = 7;
  $next      = function ( $n ) use ( &$seed ) {
    $seed = ( $seed * 1103515245 + 12345 ) % 2147483648;
    return intdiv ( $seed, 65536 ) % $n;
  };

  $vocabulary = [];
  for ( $i = 0; $i < 5000; $i++ ) {
    $word = '';
    for ( $s = 0, $k = 2 + $next ( 3 ); $s < $k; $s++ )
      $word .= $syllables [ $next ( 16 ) ];
    $vocabulary [] = $word;
  }

  $big = [];
  for ( $r = 0; $r < 1500; $r++ ) {
    $body = [];
    for ( $w = 0; $w < 200; $w++ )
      $body [] = $vocabulary [ $next ( 5000 ) ];
    $big [] = [ 'id' => $r, 'body' => implode ( ' ', $body ) ];
  }

  unset ( $body, $vocabulary );

  $before = memory_get_usage ();
  memory_reset_peak_usage ();

  $hits = [];
  foreach ( [ 'kalomi', 'kalo*', '"kalomi terter"', 'kalomi -terter' ] as $q )
    $hits [] = count ( padSearch ( $big, $q, [ 'name' => 'big', 'words' => 5 ] ) );

  $used = ( memory_get_peak_usage () - $before ) / 1e6;

  $kept = implode ( ' ', $hits ) . ( $used < 16 ? ' - within 16 MB' : ' - ' . round ( $used ) . ' MB' );

?>
