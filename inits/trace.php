<?php

  if ( isset($_REQUEST['pad_trace']) )
    $pad_trace = TRUE;

  if ( ! $pad_trace )
    return;

  $pad_trace_dir_base = "trace/$app/$page/$PADREQID";
    
  pad_file_put_contents ($pad_trace_dir_base . "/start.html", pad_get_info ('TRACE - start') );
  pad_file_put_contents ($pad_trace_dir_base . "/start.json", pad_json ( pad_track ('000') ) );

  function pad_trace ($type, $parm='') {

    global $pad_trace, $pad_trc_cnt, $pad_trace_hist;
    global $pad_lvl, $pad_trc_cnt, $pad_occur;

    if ( ! $pad_trace )
      return;

    if ( is_array($parm) )
      $parm = pad_info($parm);

    $parm = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $parm);
    if ( strlen($parm) > 100 )
      $parm = substr($parm, 0, 100);

    $pad_trc_cnt++;

    $lineX  = str_pad ( $pad_trc_cnt, 3, ' ', STR_PAD_LEFT );
    $lvlX   = str_pad ( $pad_lvl,     3, ' ', STR_PAD_LEFT );

    if ( isset( $pad_occur [$pad_lvl] ) and $pad_occur [$pad_lvl] )
      $occurX = str_pad ( $pad_occur [$pad_lvl],  2, ' ', STR_PAD_LEFT );
    else
      $occurX = '  ';
    
    $typeX  = str_pad ( $type, 13);    
    $lineL = "$lvlX $occurX   $typeX $parm";
    
    if ($pad_trc_cnt > 50)
      unset ($pad_trace_hist [$pad_trc_cnt-50]);

    $pad_trace_hist [$pad_trc_cnt] = $lineL;
     
    pad_file_put_contents ( $GLOBALS['pad_trace_dir_base'] . "/trace.txt", $lineL, TRUE );

 }  
      
?>