<?php

  $padTraceTrace = TRUE;
  $padTraceLocal = FALSE;
  
  $padTraceLevel = TRUE;       
  $padTraceOccur = TRUE;

  $padTraceLevelDir = FALSE;       
  $padTraceOccurDir = 'never';     // 'never', 'smart', 'always'
  
  $padTraceTypes = FALSE;       
  $padTraceFiles = FALSE;       

  $padTraceTypesDir = FALSE;       
  $padTraceFilesDir = FALSE;       
  
  $padTraceItems ['dumps']    = FALSE;
  $padTraceItems ['session']  = FALSE;
  $padTraceItems ['build']    = TRUE;
  $padTraceItems ['parse']    = TRUE;
  $padTraceItems ['parms']    = TRUE;
  $padTraceItems ['options']  = TRUE;
  $padTraceItems ['content']  = TRUE;
  $padTraceItems ['flags']    = TRUE;
  $padTraceItems ['true']     = TRUE;
  $padTraceItems ['false']    = TRUE;
  $padTraceItems ['base']     = TRUE;
  $padTraceItems ['data']     = TRUE;
  $padTraceItems ['store']    = TRUE;
  $padTraceItems ['sequence'] = TRUE;
  $padTraceItems ['result']   = TRUE;
  $padTraceItems ['var']      = TRUE;
  $padTraceItems ['field']    = TRUE;
  $padTraceItems ['eval']     = TRUE;
  $padTraceItems ['call']     = TRUE;
  $padTraceItems ['exists']   = FALSE;
  $padTraceItems ['sql']      = TRUE;
  $padTraceItems ['get']      = TRUE;
  $padTraceItems ['curl']     = TRUE;

?>