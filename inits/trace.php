<?php

  if ( isset($_REQUEST['pad_trace']) )
    $pad_trace = TRUE;

  if ( ! $pad_trace )
    return;

  $pad_trace_dir_base = PAD_DATA . "trace/$app/$page/$PADREQID";
  $pad_trace_dir_lvl  = "$pad_trace_dir_base/levels/0.pad";
    
  mkdir ("$pad_trace_dir_lvl/fields", 0777, true);

  file_put_contents ($pad_trace_dir_base . "/start.html", pad_get_info ('TRACE - start') );

  pad_trace ("pad/start", "app=$app page=$page session=$PADSESSID request=$PADREQID");

  function pad_trace ($type, $parm='') {

    global $pad_trace, $pad_trc_cnt, $pad_trace_hist, $pad_trace_dir_base;
    global $pad_lvl, $PADREQID, $pad_lvl_cnt, $pad_trc_cnt, $pad_occur_cnt, $pad_occur;

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
  
    $id = pad_id ();
     
    file_put_contents ( "$pad_trace_dir_base/trace.txt", $lineL . "\n", FILE_APPEND );
    
    if ( isset ($GLOBALS['pad_trace_dir_lvl']) )
      file_put_contents ( $GLOBALS['pad_trace_dir_lvl'] . "/trace.txt",  $lineL . "\n", FILE_APPEND );
 
    if ( isset ($GLOBALS['pad_trace_dir_occ']) )
      file_put_contents ( $GLOBALS['pad_trace_dir_occ'] . "/trace.txt",  $lineL . "\n", FILE_APPEND );

 }  
      
?>