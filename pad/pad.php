<?php

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR )    ) $padHR    = hrtime    ( TRUE );
  
  if ( ! defined ( 'APP' ) ) die ( 'Constant APP must be set before calling this script' );
  if ( ! defined ( 'DAT' ) ) die ( 'Constant DAT must be set before calling this script' );

  define ( 'PAD', dirname ( __FILE__ ) );

  chdir            ( PAD );
  set_include_path ( PAD );

  include 'error/boot.php';
  include 'config/config.php';
  include 'start/enter/start.php'
  
?>