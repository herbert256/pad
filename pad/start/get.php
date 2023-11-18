<?php

  $padStartType = 'get';

  if ( $padXref ) 
    padXref ( 'start', $padStartType );

  $padPagePage    = $padGet;
  $padPageInclude = TRUE;
  $padPageType    = 'sandbox';
  $padPageStart   = 'get';

  return include pad . "start/lib/page.php";

?>