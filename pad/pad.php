<?php

  if ( ! isset ( $padMicro ) ) $padMicro = microtime ( TRUE );
  if ( ! isset ( $padHR )    ) $padHR    = hrtime    ( TRUE );

  define ( 'PAD', dirname ( __FILE__ ) . '/' ) ;

  include PAD . 'error/boot.php';

  if ( ! defined ( 'APP' ) ) padBootError ( 'Constant APP must be set before calling this script' );
  if ( ! defined ( 'DAT' ) ) padBootError ( 'Constant DAT must be set before calling this script' );

  if ( ! str_ends_with ( APP, '/' ) ) padBootError ( 'Constant APP must end with a / char' );
  if ( ! str_ends_with ( DAT, '/' ) ) padBootError ( 'Constant DAT must end with a / char' );

  if ( ! file_exists (APP) or ! is_dir (APP) ) padBootError ( "Application directory not found: " . APP );

  chdir            ( APP );
  set_include_path ( APP );

  include PAD . 'config/config.php';
  include PAD . 'start/enter/start.php';

?>
