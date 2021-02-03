<?php

  $pad_trace_log     = "trace/$app/$page/$PADREQID.txt";
  $pad_trace_hist    = [];
  $pad_trc_cnt       = 0;

  pad_trace ("pad/start", "app=$app page=$page session=$PADSESSID request=$PADREQID");

  function pad_trace ($type, $parm='') {

    global $pad_trace, $pad_trc_cnt, $pad_trace_log, $pad_trace_hist;
    global $pad_lvl, $PADREQID, $pad_lvl_cnt, $pad_trc_cnt, $pad_occur_cnt, $pad_occur;

    if ( ! $pad_trace )
      return;

    if ( is_array($parm) )
      $parm = pad_info($parm);

    $pad_trc_cnt++;

    $lineX  = str_pad ( $pad_trc_cnt, 3, ' ', STR_PAD_LEFT );
    $lvlX   = str_pad ( $pad_lvl,     3, ' ', STR_PAD_LEFT );

    if ( isset( $pad_occur [$pad_lvl] ) and $pad_occur [$pad_lvl] )
      $occurX = str_pad ( $pad_occur [$pad_lvl],  2, ' ', STR_PAD_LEFT );
    else
      $occurX = '  ';
    
    $typeX  = str_pad ( $type, 13);
    
    $parm = trim(preg_replace('/\s+/', ' ', $parm));
    
    if ( strlen($parm) > 100)
      $parm = substr($parm, 0, 100);
    
    $lineL = "$lvlX $occurX   $typeX $parm";
    
    if ($pad_trc_cnt > 50)
      unset ($pad_trace_hist [$pad_trc_cnt-50]);

    $pad_trace_hist [$pad_trc_cnt] = $lineL;
  
  }  
      
?>