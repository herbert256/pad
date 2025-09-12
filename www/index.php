<?php

  $padMicro = microtime ( TRUE );
  $padHR    = hrtime    ( TRUE );

  $padHome = dirname ( __FILE__ );
  $padHome = str_replace ( '\\',   '/', $padHome );
  $padHome = str_replace ( '/www', '', $padHome );

  define ( 'APP', "$padHome/app/" );
  define ( 'DAT', "$padHome/data/" );

  include "$padHome/pad/pad.php";

?>