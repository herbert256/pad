<?php

  $padStartType = 'page';

  if ( padXref ) 
    include pad . 'info/types/xref/items/entry.php';

  $padPagePage    = padPageGetName ();
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'ajax' );
  $padPageStart   = 'page';

  if ( isset ( $_SERVER ['HTTP_REFERER'] ) )
    if ( str_contains ( $_SERVER ['HTTP_REFERER'] , 'develop/all&fromMenu=1' ) )
      $padPageType = 'get';

  return include pad . "start/lib/page.php";

?>