<?php

  global $padHost;

  $padCurlStats = TRUE;

  $list = [];

  if ( ! $padInfo      ) return TRUE;
  if ( ! $padInfoStats ) return TRUE;

  $title = "Benchmark";

  foreach ( padAppsList () as $one ) {

    $item = $one ['item'];

    $store = DATA . "regression/" . $one ['app'] . "/$item.txt";
    $bm    = DATA . "_benchmark/$item.json";

    $status = padFileGet ( $store );
    if ( $status != 'ok' )
      continue;

    $curl = padCurl ( $padHost . $one ['app'] . "/?$item&padInclude" );

    if ( ! str_starts_with ( $curl ['result'], '2') ) continue;
    if ( ! isset           ( $curl ['stats'] )      ) continue;

    $list [$item] = $curl ['stats'];
    $list [$item] ['item'] = $item;

    padFilePut ( $bm, padJson ( $curl ['stats'] ) );

  }

  ksort ($list);

?>