<?php

  $pad_occur [$pad]++;
  $pad_html  [$pad] = $pad_base[$pad];
  $pad_key   [$pad] = key($pad_data[$pad]);

  $pad_current [$pad] = $pad_data [$pad] [$pad_key [$pad]];

  if ( $pad_walks [$pad] <> 'start' )
    $pad_walks_data [ $pad] [] = $pad_current [$pad];

  if ( $pad > 1 ) {

    if ( pad_is_default_data ($pad_data [$pad]) ) {
      if ( isset($pad_prms_val[0]) )
        pad_set_global ( $pad_name, $pad_prms_val[0] );
    } else
      pad_set_global ( $pad_name, $pad_current [$pad] );

    foreach ( $pad_current [$pad] as $pad_k => $pad_v )
      pad_set_global ( $pad_k, $pad_v );

  }

  if ( isset($pad_prms_tag ['callback']) and ! isset($pad_prms_tag ['before']) )
    include PAD . 'callback/row.php' ;

  include PAD . 'occurrence/db.php';

  if ( $pad_trace_occurence ) 
    include 'trace/start.php';

?>