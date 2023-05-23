<?php

  //  ============================================================================
  //  PAD - (P)HP (A)pplication (D)river
  //  (c) 2004-2023 by Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  ============================================================================
  //
  //  This is the PAD startup file, the first file that becomes active.
  //
  //  Only this file must be located inside the webservers htdocs directory,
  //  all other PAD files must be stored *OUTSIDE* the webservers htdocs directory.
  //
  //  ============================================================================

  $padHome = ( $_SERVER ['HTTP_HOST'] == 'penguin.linux.test' ) ? 'home' : 'Users';

  define ( 'pad',     "/$padHome/herbert/pad/"     ); // Home of PAD itself
  define ( 'padApp',  "/$padHome/herbert/pad/app/" ); // The PAD application files
  define ( 'padData', "/$padHome/herbert/data/"    ); // Data locaction, used for logs/cache/errors/etc.
  
  include pad . 'start.php';

?>