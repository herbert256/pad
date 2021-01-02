<?php

  $pad_trace_file    = ( strpos($pad_trace, 'file')    !== FALSE );
  $pad_trace_browser = ( strpos($pad_trace, 'browser') !== FALSE );
  $pad_trace_memory  = ( strpos($pad_trace, 'memory')  !== FALSE );

  $pad_trace_log     = "trace/$app/$page/$PADREQID.txt";
  $pad_trace_hist    = [];
  $pad_trc_cnt       = 0;

  pad_trace ("pad/start", "app=$app page=$page session=$PADSESSID request=$PADREQID", TRUE);

  function pad_trace ($type, $parm='', $skip=FALSE) {

    global $pad_trace, $pad_trc_cnt, $pad_trace_log, $pad_trace_hist, $pad_trace_browser, $pad_trace_file;
    global $pad_lvl, $PADREQID, $pad_lvl_cnt, $pad_trc_cnt, $pad_occur_cnt, $pad_occur;

    if ( $pad_trace == 'none')
      return;

    if ( ! $pad_trc_cnt and $pad_trace_browser )
      echo "<pre>";

    $pad_trc_cnt++;

    $lineX  = str_pad ( $pad_trc_cnt, 3, ' ', STR_PAD_LEFT );
    $lvlX   = str_pad ( $pad_lvl,     3, ' ', STR_PAD_LEFT );

    if ( isset( $pad_occur [$pad_lvl] ) and $pad_occur [$pad_lvl] and !$skip )
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
  
    if ( $pad_trace_file )  
      pad_file_put_contents ($GLOBALS['pad_trace_log'], "$lineX $lineL" . PHP_EOL, 1);

    if ( $pad_trace_browser )
      echo htmlentities("$lineL\n");

  }  
      
?>