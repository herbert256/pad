<?php

  $padStartType = 'page';

  if ( $padXref ) 
    padXref ( 'start', $padStartType );

  $padPagePage    = padPageGetName ();
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'include' );
  $padPageStart   = 'page';

  return include pad . "start/lib/page.php";

?>