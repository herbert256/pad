<?php

  set_time_limit ( 300 );

  foreach ( padList () as $one ) {

    $item = $one ['item'];

    if ( str_starts_with ( $item, 'sequence/' ) )
      $list [$item] = file_get_contents ( APP . "$item.pad" );

  }
     
  ksort ( $list );

  foreach ( $list as $item => $code )
    echo "<h4>$item</h4>{page '$item', sandbox}";
  
?>