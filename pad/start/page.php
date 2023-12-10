<?php

  $padStartType = 'page';

  if ( padXref ) 
    include pad . 'info/types/xref/items/entry.php';

  $padPagePage    = padPageGetName ();
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'ajax' );
  $padPageStart   = 'page';

  return include pad . "start/lib/page.php";

?>