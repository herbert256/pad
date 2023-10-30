<?php
  
  $padTraceLocal    = TRUE;

  $padTraceOccurDir  = 'never';        // 'never', 'smart', 'always'
  $padTraceTypesDir  = FALSE;       
  $padTraceFilesDir  = FALSE;       
  $padTraceEvalShort = FALSE;       

  
  $padTraceTypes ['files']    = TRUE;
  $padTraceTypes ['types']    = FALSE;

  $padTraceTypes ['dumps']    = FALSE;

  $padTraceTypes ['build']    = TRUE;

  $padTraceTypes ['parse']    = TRUE;

  $padTraceTypes ['parms']    = TRUE;
  $padTraceTypes ['options']  = TRUE;
  $padTraceTypes ['content']  = TRUE;
  $padTraceTypes ['flags']    = TRUE;
  $padTraceTypes ['true']     = TRUE;
  $padTraceTypes ['false']    = TRUE;
  $padTraceTypes ['base']     = TRUE;
  $padTraceTypes ['data']     = TRUE;

  $padTraceTypes ['occur']    = TRUE; 

  $padTraceTypes ['store']    = TRUE;
  $padTraceTypes ['sequence'] = TRUE;

  $padTraceTypes ['result']   = TRUE;

  $padTraceTypes ['var']      = TRUE;
  $padTraceTypes ['field']    = TRUE;

  $padTraceTypes ['eval']     = TRUE;
  $padTraceTypes ['call']     = TRUE;
  $padTraceTypes ['exists']   = FALSE;
  $padTraceTypes ['sql']      = TRUE;
  $padTraceTypes ['get']      = TRUE;
  $padTraceTypes ['curl']     = TRUE;

?>