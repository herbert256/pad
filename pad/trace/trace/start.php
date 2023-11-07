<?php

  include_once pad . 'trace/lib/trace.php';
  include_once pad . 'trace/lib/lib.php';
  include_once pad . 'trace/lib/set.php';
  include_once pad . 'trace/lib/xml.php';

  include pad . 'trace/config/trace.php';
  include pad . 'trace/config/xml.php';

  $padTraceActive    = TRUE;  
  $padTraceSpaces    = 0;
  $padTraceSkipLevel = 0;
  $padTraceMaxLevel  = 0;

  $padTraceBase = "trace/$padPage/$padReqID-" . padRandomString (8); 

  if ( ! isset ( $padTraceLine ) )
    $padTraceLine = 0;

  padTraceInit ( $pad );

  $padTraceXmlWhere = 'boot';

  padTraceSet ( 'trace', 'start' );

  $padTraceXmlWhere = 'inits';

  if ( $padTraceOpenClose )
    padTrace ( 'trace', 'start' );

  if ( $padTraceDumps ) 
    padTraceDump ( 'start' );

  if ( $padTraceSession )
    foreach ( padSessionInfoStart () as $padK => $padV )
      padTrace ( 'session', $padK, $padV );

?>