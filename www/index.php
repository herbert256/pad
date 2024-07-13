<?php

  //  =============================================================================
  //  PAD - (P)HP (A)pplication (D)river
  //  (c) 2004-2024 by Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  =============================================================================
  //
  //  This is the PAD startup file for HTTP
  //
  //  Only this file must be inside the webservers htdocs directory,
  //  all other PAD files must be stored *OUTSIDE* the webservers htdocs directory.
  //
  //  =============================================================================

  $padOS = substr ( strtolower ( php_uname ('s') ), 0, 3 );

  if     ( $padOS == 'dar' ) $padHome = '/Users/herbert';
  elseif ( $padOS == 'win' ) $padHome = '/xampp';
  else                       $padHome = '/home/herbert';

  define ( 'pad',     "$padHome/pad/pad/"      );   // Home of PAD itself
  define ( 'padApp',  "$padHome/pad/apps/pad/" );   // The application files
  define ( 'padData', "$padHome/data/"         );   // Data locaction, used for logs/cache/errors/etc.
  
  include pad . 'pad.php';

?>