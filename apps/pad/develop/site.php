<?php

  $work = padList ();

  foreach ( $work as $key => $one ) {

    $pagesList [$key] ['item'] = $one ['item'];

  }

  set_time_limit ( 300 );

  $showTitle = FALSE;

?>