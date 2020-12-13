<?php

  $pad_trace_file    = ( $GLOBALS['pad_trace'] == 'file'    or $GLOBALS['pad_trace'] == 'both' );
  $pad_trace_browser = ( $GLOBALS['pad_trace'] == 'browser' or $GLOBALS['pad_trace'] == 'both' );

  $pad_trace_log     = "trace/$app/$page/$PADREQID.txt";
  $pad_trace_hist    = [];
  $pad_trc_cnt       = 0;

  pad_trace ("pad/start", "app=$app page=$page session=$PADSESSID request=$PADREQID", TRUE);
      
?>