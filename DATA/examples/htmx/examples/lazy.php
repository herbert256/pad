<?php

  // The slow part. The page itself renders a skeleton where the chart goes; once it has
  // loaded, htmx asks for the {fragment 'chart'} (hx-trigger="load", HX-Target: chart) and
  // that answer holds the chart PAD draws. The wait is made up - the answer of a slow
  // database or service - and only an htmx request has it.

  $slow = ( $_SERVER ['HTTP_HX_REQUEST'] ?? '' ) === 'true';

  if ( $slow )
    usleep ( 700000 );

  $sales = htmxData ( 'sales' );

?>
