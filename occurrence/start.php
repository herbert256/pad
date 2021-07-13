<?php

  foreach ( $pad_parameters [$pad_lvl] as $pad_k => $pad_v )
    $GLOBALS['pad_'.$pad_k] = $pad_v;

  $pad_occur [$pad_lvl]++;
  $pad_html  [$pad_lvl] = $pad_base[$pad_lvl];
  $pad_key   [$pad_lvl] = key($pad_data[$pad_lvl]);

  $pad_current [$pad_lvl] = $pad_data [$pad_lvl] [$pad_key [$pad_lvl]];

  if ( $pad_walks [$pad_lvl] <> 'start' and isset ( $pad_parms_tag ['toData'] ))
    $pad_walks_data [ $pad_lvl] [] = $pad_current [$pad_lvl];

  if ( $pad_lvl > 1 ) {

    if ( pad_is_default_data ($pad_data [$pad_lvl]) ) {
      if ( isset($pad_parms_val[0]) )
        pad_set_global ( $pad_name, $pad_parms_val[0] );
    } else
      pad_set_global ( $pad_name, $pad_current [$pad_lvl] );

    foreach ( $pad_current [$pad_lvl] as $pad_k => $pad_v )
      pad_set_global ( $pad_k, $pad_v );

  }

  if ( isset($pad_parms_tag ['callback']) and ! isset($pad_parms_tag ['before']) )
    include PAD_HOME . 'callback/row.php' ;

  include PAD_HOME . 'occurrence/db.php';

  $pad_options = 'occur_start';
  include PAD_HOME . "options/go/options.php";

  if ( $pad_walks [$pad_lvl] == 'occurrence-start' )
    include PAD_HOME . "walk/occurrence-start.php";

  if ( $pad_trace ) 
    include 'trace/start.php';

?>