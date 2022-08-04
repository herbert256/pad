<?php

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD . 'occurrence/end.php';

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  if ( next($pad_data[$pad_lvl]) !== FALSE )
    return include PAD . 'occurrence/start.php';

  if ( $pad_walks [$pad_lvl] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $pad_walk == 'next' )
      return include PAD . 'occurrence/start.php';
  }

  $pad_occur [$pad_lvl] = 0;

  if ( $pad_walks [$pad_lvl] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']) )
    include PAD . 'callback/exit.php' ;

  include PAD . "options/go/end.php";

  pad_reset ($pad_lvl);

  if ( $pad_trace ) 
    include 'trace/end.php';

  $pad_lvl--;

  if ($pad_lvl > 1)
    foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
      $GLOBALS['pad_'.$pad_k] = $pad_v;
  
  if ($pad_lvl)
    pad_html ( $pad_result[$pad_lvl+1]);
  
?>