<?php

  if ( $pad_walks [$pad_lvl] == 'next' ) {
    
    foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
      $GLOBALS['pad_'.$pad_k] = $pad_v;

    $pad_walk = 'next';
    include PAD_HOME . 'level/tag.php';
  
    if ( $pad_walk == 'next' )
      return include PAD_HOME . 'occurrence/start.php';

  }

  if ( $pad_walks [$pad_lvl] == 'end' ) {

    foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
      $GLOBALS['pad_'.$pad_k] = $pad_v;

    $pad_walk = 'end';

    $pad_content = $pad_result[$pad_lvl];
    $pad_result[$pad_lvl] = '';
    include PAD_HOME . "level/type.php";
    $pad_result[$pad_lvl] = $pad_content . $pad_result[$pad_lvl];

  }

  if ( isset($pad_parms_pad ['callback']) ) {
    $pad_callback = "exit_tag";
    include PAD_HOME . 'level/callback.php' ;
  }

  if ( isset ( $pad_parms_pad ['toContentStore'] )  
    or isset ( $pad_parms_pad ['toDataStore']    )  
    or isset ( $pad_parms_pad ['toFlagStore']    )  )
      include PAD_HOME . 'level/store.php' ;

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