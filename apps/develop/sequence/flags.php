<?php

  $flagDir = PT . "$type/flags/";

  if ( is_dir ( $flagDir ) )
    foreach ( glob ( $flagDir . '*' ) as $file ) unlink ( $file );
  else
    mkdir ( $flagDir );

  file_put_contents ( $flagDir . 'readme.txt', 'This directory is generated', LOCK_EX );

?>
