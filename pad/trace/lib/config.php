<?php

 $padTraceTree = FALSE; 

 if ( ! $padTraceTypes ['global'] and ! $padTraceTypes ['nested'] and ! $padTraceTypes ['local'] ) 
   $padTraceTypes ['nested'] = TRUE;

 if ( $padTraceTypes ['data'] or $padTraceTypes ['content'] or $padTraceTypes ['store'] or $padTraceTypes ['sequence'] ) 
   $padTraceTree = TRUE;

 if ( $padTraceTypes ['nested'] or $padTraceTypes ['local'] ) 
   $padTraceTree = TRUE;

  if ( ! $padTraceTree ) {
    $padTraceTypes ['global'] = TRUE;
    $padTraceTypes ['nested'] = FALSE;
    $padTraceTypes ['local']  = FALSE;
  }

 if ( $padTraceTypes ['nested'] ) 
   $padTraceTypes ['global'] = FALSE;

 if ( $padTraceTree and ( ! $padTraceTypes ['nested'] and ! $padTraceTypes ['local'] ) ) 
   $padTraceTypes ['nested'] = TRUE;
 
?>