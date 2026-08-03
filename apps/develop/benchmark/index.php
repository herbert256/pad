<?php

  $padCurlStats = TRUE;

  $list = [];

  if ( ! $padInfo      ) return TRUE;
  if ( ! $padInfoStats ) return TRUE;

  $title = "Benchmark";

  foreach ( padAppsList () as $one ) {

    $item = $one ['item'];

    $store = DAT . "regression/" . $one ['app'] . "/$item.txt";
    $bm    = DAT . "_benchmark/$item.json";

    $status = padFileGet ( $store );
    if ( $status <> 'ok' )
      continue;

    $curl = getPage ( $one ['item'], 1, 1 );

    if ( ! str_starts_with ( $curl ['result'], '2') ) continue;
    if ( ! isset           ( $curl ['stats'] )      ) continue;

    $list [$item] = $curl ['stats'];
    $list [$item] ['item'] = $item;

    padFilePut ( $bm, padJson ( $curl ['stats'] ) );

  }

  ksort ($list);

?>