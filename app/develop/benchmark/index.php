<?php

  $padCurlStats = TRUE;

  $list = [];

  if ( ! $padInfo      ) return TRUE;
  if ( ! $padInfoStats ) return TRUE; 

  $title = "Benchmark";

  foreach ( padList () as $one ) {

    $item = $one ['item'];

    $store = APP . "_regression/$item.txt";
    $bm    = APP . "_benchmark/$item.json";

    $status = padFileGet ( $store );
    if ( $status <> 'ok' )
      continue;

    $curl = getPage ( $one ['item'], 1, 1 );

    if ( ! str_starts_with ( $curl ['result'], '2') ) continue;
    if ( ! isset           ( $curl ['stats'] )      ) continue;

    $list [$item] = $curl ['stats'];
    $list [$item] ['item'] = $item;

    padFilePut ( $bm, $curl ['stats']  );

  }

  ksort ($list);

?>