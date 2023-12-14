<?php

  $padStartType = 'page';

  if ( padXref ) 
    include pad . 'info/types/xref/items/entry.php';

  $padPagePage    = $padOpt [$pad] [1];
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'get' );
  $padPageStart   = 'page';

  return include pad . "start/lib/page.php";

?>