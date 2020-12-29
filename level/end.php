<?php

  if ( count ($pad_data[$pad_lvl] ) )
    include PAD_HOME . 'occurrence/end.php';

  if ( next($pad_data[$pad_lvl]) !== FALSE )
    return include PAD_HOME . 'occurrence/start.php';

  if ( $pad_walks [$pad_lvl] == 'next' ) {
    
    foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
      $GLOBALS['pad_'.$pad_k] = $pad_v;

    $pad_walk = 'next';
    include PAD_HOME . 'level/tag.php';
  
    if ( $pad_walk == 'next' )
      return include PAD_HOME . 'occurrence/start.php';

  }

  if ( $pad_walks [$pad_lvl] == 'end' )
    include PAD_HOME . 'walk/end.php';

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']))
    include PAD_HOME . 'callback/exit.php' ;

  foreach ($pad_parms_end as $pad_v)
    if ( isset ( $pad_parms_tag [$pad_v] ) )
        include PAD_HOME . "parms/$pad_v.php" ;

  pad_trace ("level/end", "nr=$pad_lvl_cnt", TRUE);

  $pad_lvl--;

  if ($pad_lvl > 1)
    foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
      $GLOBALS['pad_'.$pad_k] = $pad_v;
  
  if ($pad_lvl)
    pad_html ($pad_result[$pad_lvl+1]);
  else
    $pad_output = $pad_result [1];
  
?>