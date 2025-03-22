<?php

  set_time_limit ( 300 );

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    if ( ! str_starts_with ( $item, 'sequence/' ) )
      continue;

    $source = file_get_contents ( APP . "$item.pad" );

    $source = str_replace ( '{table}',  '', $source );
    $source = str_replace ( '{demo}',   '', $source );
    $source = str_replace ( '{/table}', '', $source );
    $source = str_replace ( '{/demo}',  '', $source );
    $source = str_replace ( "\n",       '', $source );

    $start = hrtime ( TRUE );

    padSandbox ( $source );

    $bm [$item] - hrtime ( TRUE ) - $start;

  }
     
?>