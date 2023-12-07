<?php

  $padStartType = 'page';

  if ( padXref ) 
    include pad . 'info/types/xref/items/entry.php';

  $padPagePage    = padPageGetName ();
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'include' );
  $padPageStart   = 'page';

  return include pad . "start/lib/page.php";

?>