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

  $padOS = substr ( strtolower ( php_uname('s') ), 0, 3);

  if     ( $padOS == 'lin' ) $padHome = '/home/herbert';
  elseif ( $padOS == 'dar' ) $padHome = '/Users/herbert';
  elseif ( $padOS == 'win' ) $padHome = '/xampp';
  else                       $padHome = '/oops/no/os/found';

  define ( 'pad',     "$padHome/pad/"     ); // Home of PAD itself
  define ( 'padApp',  "$padHome/pad/app/" ); // The PAD application files
  define ( 'padData', "$padHome/data/"    ); // Data locaction, used for logs/cache/errors/etc.
  
  include pad . 'start.php';

?>