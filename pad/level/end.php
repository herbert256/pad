<?php

  if ( count ($pad_data[$pad] ) )
    include PAD . 'occurrence/end.php';

  foreach ( $pad_parms [$pad] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  if ( next($pad_data[$pad]) !== FALSE )
    return include PAD . 'occurrence/start.php';

  if ( $pad_walks [$pad] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $pad_walk == 'next' )
      return include PAD . 'occurrence/start.php';
  }

  $pad_occur [$pad] = 0;

  if ( $pad_walks [$pad] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($pad_prms_tag ['callback']) and ! isset($pad_prms_tag ['before']) )
    include PAD . 'callback/exit.php' ;

  include PAD . "options/go/end.php";

  pad_reset ($pad);

  if ( $pad_trace ) 
    include 'trace/end.php';

  $pad--;

  if ($pad > 1)
    foreach ( $pad_parms [$pad] as $pad_k => $pad_v )
      $GLOBALS['pad_'.$pad_k] = $pad_v;
  
  if ($pad)
    pad_html ( $pad_result[$pad+1]);
  
?>