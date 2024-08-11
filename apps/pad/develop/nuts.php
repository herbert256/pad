<?php

  if ( ! isset ( $fromMenu ) )
    return NULL;

  $title = "Everything";

  foreach ( padList ( 0 ) as $one ) {
    $item = $one ['item'];
    $items [$item] ['item'] = $item;
  }

  ksort ($items);

?>