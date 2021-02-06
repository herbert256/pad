<?php

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD_HOME . 'occurrence/end.php';

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  if ( next($pad_data[$pad_lvl]) !== FALSE )
    return include PAD_HOME . 'occurrence/start.php';

  if ( $pad_walks [$pad_lvl] == 'next' ) {

    include PAD_HOME . 'walk/next.php';
  
    if ( $pad_walk == 'next' )
      return include PAD_HOME . 'occurrence/start.php';

  }

  $pad_occur [$pad_lvl] = 0;

  if ( $pad_walks [$pad_lvl] == 'end' )
    include PAD_HOME . 'walk/end.php';

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']) )
    include PAD_HOME . 'callback/exit.php' ;

  $pad_options = 'level_end';
  include PAD_HOME . "level/options.php";

  pad_reset_set ($pad_lvl);

  pad_trace ("level/end", "nr=$pad_lvl_cnt");

  $pad_lvl--;

  if ($pad_lvl > 1)
    foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
      $GLOBALS['pad_'.$pad_k] = $pad_v;
  
  if ($pad_lvl)
    pad_html ($pad_result[$pad_lvl+1]);
  else
    $pad_output = $pad_result [1];
  
?>