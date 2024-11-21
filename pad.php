<?php

  if ( ! isset ( $padApp ) )
    $padApp = 'pad';

  $padHome = dirname ( __FILE__ );

  define ( 'PAD', "$padHome/pad/" );
  define ( 'APP', "$padHome/apps/$padApp/" );
  define ( 'DAT', "$padHome/data/$padApp/" );

  chdir            ( PAD );
  set_include_path ( PAD );

  include 'error/boot.php';
  include 'config/config.php';
  include 'catch/start.php';

?>