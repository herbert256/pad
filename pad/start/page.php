<?php

  $padStartType = 'page';

  if ( $padXref ) 
    include pad . 'xref/items/entry.php';

  $padPagePage    = padPageGetName ();
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'include' );
  $padPageStart   = 'page';

  return include pad . "start/lib/page.php";

?>