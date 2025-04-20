<?php

  $padCurlStats = [];
  $list         = [];

  if ( ! $padInfo      ) return TRUE;
  if ( ! $padInfoStats ) return TRUE; 

  $title = "Benchmark";

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    $store = APP . "_regression/$item.txt";
    $status = padFileGetContents ($store);

    if ( $status <> 'ok' )
      continue;

    $curl = getPage ( $one ['item'], 1, 1 );

    if ( ! str_starts_with ( $curl ['result'], '2') ) continue;
    if ( ! isset ( $padCurlStats ['curl'] )         ) continue;

    $list [$item] = $padCurlStats;
    $list [$item] ['item'] = $item;

  }

  ksort ($list);

?>