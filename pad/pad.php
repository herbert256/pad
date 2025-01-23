<?php

  if ( ! defined ( 'APP' ) ) die ( 'Constant APP must be set before calling this script' );
  if ( ! defined ( 'DAT' ) ) die ( 'Constant DAT must be set before calling this script' );

  chdir            ( dirname ( __FILE__ ) );
  set_include_path ( dirname ( __FILE__ ) );

  include 'error/boot.php';
  include 'config/config.php';
  include 'tryCatch/go/start.php';
  
?>