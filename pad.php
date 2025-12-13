<?php

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR    ) ) $padHR    = hrtime    ( TRUE );

  if ( ! defined ( 'APP' ) ) die ( 'Constant APP must be set before calling this script' );
  if ( ! defined ( 'DAT' ) ) die ( 'Constant DAT must be set before calling this script' );

  if ( !  str_ends_with ( APP, '/' ) ) die ( 'Constant APP must end with a / char' );
  if ( !  str_ends_with ( DAT, '/' ) ) die ( 'Constant DAT must end with a / char' );

  chdir            ( APP );
  set_include_path ( APP );

  define ( 'PAD', dirname ( __FILE__ ) . '/' ) ;

  include PAD . 'error/boot.php';
  include PAD . 'config/config.php';
  include PAD . 'start/enter/start.php';
  
?>