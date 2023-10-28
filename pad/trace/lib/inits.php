<?php

  $padTraceActive = TRUE;
  
  include_once pad . 'trace/lib/trace.php';

  $padTrace         = $padTraceBase ?? 0;
  $padInTrace       = FALSE;
  $padTraceTree     = FALSE; 
  $padTraceLocation = "trace/$padPage/$padReqID"; 

  if ( ! $padTraceTypes ['global'] and ! $padTraceTypes ['nested'] and ! $padTraceTypes ['local'] ) 
    $padTraceTypes ['nested'] = TRUE;

  if ( $padTraceTypes ['xml'] ) {
    $padTraceXml = '';
    $padTraceTree            = TRUE; 
    $padTraceTypes ['start'] = TRUE;
    $padTraceTypes ['end']   = TRUE;
    $padTraceTypes ['occur'] = TRUE;
  } 

  if ( $padTraceTypes ['occur'] ) {
    $padTraceTree            = TRUE; 
    $padTraceTypes ['start'] = TRUE;
    $padTraceTypes ['end']   = TRUE;
  } 

  if ( $padTraceTypes ['store'] or $padTraceTypes ['sequence'] ) 
    $padTraceTree = TRUE;
  elseif ( $padTraceTypes ['data'] or $padTraceTypes ['content'] ) 
    $padTraceTree = TRUE;
  elseif ( $padTraceTypes ['nested'] or $padTraceTypes ['local'] ) 
    $padTraceTree = TRUE;
  elseif ( $padTraceTypes ['base'] or $padTraceTypes ['result'] ) 
    $padTraceTree = TRUE;
  elseif ( $padTraceTypes ['types-global'] or  $padTraceTypes ['types-local'] ) 
    $padTraceTree = TRUE;

  if ( $padTraceTypes ['nested'] ) 
    $padTraceTypes ['global'] = FALSE;

  if ( $padTraceTree and ( ! $padTraceTypes ['nested'] and ! $padTraceTypes ['local'] ) ) 
    $padTraceTypes ['nested'] = TRUE;

  if ( $padTraceTree and $padTraceTypes ['dumps'] )
    padDumpToDir ( '', $padTraceLocation . "/START" );

  $padTraceId [$pad] = $padTrace + 1;

  padTrace ( 'trace', 'start' );

?>