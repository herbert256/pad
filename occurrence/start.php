<?php

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  $pad_occur [$pad_lvl]++;
  $pad_occur_cnt++;

  $pad_html [$pad_lvl] = $pad_base[$pad_lvl];
  $pad_key  [$pad_lvl] = key($pad_data[$pad_lvl]);

  pad_trace ("occur/start", "nr=$pad_occur_cnt key=" . $pad_key [$pad_lvl] . ' html=' . $pad_html[$pad_lvl]);

  $pad_current [$pad_lvl] = $pad_data [$pad_lvl] [$pad_key [$pad_lvl]];

  if ( $pad_lvl > 1 ) {

    if ( count($pad_data [$pad_lvl]) <> 1 
       or !isset($pad_data [$pad_lvl][1]) 
       or count($pad_data [$pad_lvl][1]) 
       or $pad_parameters [$pad_lvl] ['name'] <> $pad_parameters [$pad_lvl] ['tag'] )
     pad_set_global ( $pad_name, $pad_current [$pad_lvl] );

    foreach ( $pad_current [$pad_lvl] as $pad_k => $pad_v )
      pad_set_global ( $pad_k, $pad_v );

    if ( $pad_tag_parms )
      foreach ( $pad_parms_app as $pad_k => $pad_v )
        pad_set_global ( $pad_k, $pad_v );

  }

  if ( isset($pad_parms_pad ['callback']) ) {
    $pad_callback = "init_occurrence";
    include PAD_HOME . 'level/callback.php';
  }

  include PAD_HOME . 'occurrence/db.php';

  if ($pad_name == 'trace')  {
    $pad_trace = TRUE;
  }


?>