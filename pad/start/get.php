<?php

  $padStartType = 'get';

  if ( padXref ) 
    include pad . 'info/types/xref/items/entry.php';

  $padPagePage    = $padGet;
  $padPageInclude = TRUE;
  $padPageType    = 'sandbox';
  $padPageStart   = 'get';

  return include pad . "start/lib/page.php";

?>