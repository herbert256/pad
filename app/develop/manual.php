<?php

  $title = "All manual pages";

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    if ( str_starts_with ( $item, 'manual/' ) )
      $items [$item] ['item'] = $item;
  
  }

  ksort ($items);

?>